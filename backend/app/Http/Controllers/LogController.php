<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Environment\Environment;
use HTMLPurifier;
use HTMLPurifier_Config;

class LogController extends Controller
{
    private $markdownConverter;
    private $purifier;

    public function __construct()
    {
        // Markdownコンバーター設定
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());
        
        $this->markdownConverter = new CommonMarkConverter([], $environment);
        
        // HTMLPurifier設定（XSS対策）
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,br,strong,em,ul,ol,li,h1,h2,h3,h4,h5,h6,blockquote,code,pre,table,thead,tbody,tr,th,td,a[href]');
        $config->set('HTML.SafeIframe', true);
        $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube\.com/embed/|player\.vimeo\.com/video/)%');
        $this->purifier = new HTMLPurifier($config);
    }

    /**
     * ログ一覧表示
     */
    public function index(Request $request)
    {
        $query = Log::with('tags')->forUser(Auth::id());

        // 日付フィルター
        if ($request->filled('date')) {
            $query->byDate($request->date);
        }

        // タグフィルター
        if ($request->filled('tag')) {
            $query->withTag($request->tag);
        }

        // 並び順
        $order = $request->get('order', 'desc');
        if ($order === 'asc') {
            $query->orderByDateAsc();
        } else {
            $query->orderByDateDesc();
        }

        $logs = $query->paginate(10);
        $tags = Tag::popular()->get();

        return view('logs.index', compact('logs', 'tags'));
    }

    /**
     * ログ詳細表示
     */
    public function show(Log $log)
    {
        Gate::authorize('view', $log);
        
        $log->load('tags');
        $renderedContent = '';
        
        if ($log->content) {
            $html = $this->markdownConverter->convert($log->content);
            $renderedContent = $this->purifier->purify($html);
        }

        return view('logs.show', compact('log', 'renderedContent'));
    }

    /**
     * ログ作成フォーム表示
     */
    public function create()
    {
        $tags = Tag::orderBy('name')->get();
        return view('logs.create', compact('tags'));
    }

    /**
     * ログ保存
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'date' => 'required|date',
            'tags' => 'nullable|string',
        ]);

        $log = Log::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'date' => $validated['date'],
            'user_id' => Auth::id(),
        ]);

        // タグの処理
        if (!empty($validated['tags'])) {
            $tagNames = array_unique(array_filter(array_map('trim', explode(',', $validated['tags']))));
            $tags = Tag::findOrCreateByNames($tagNames);
            $log->tags()->attach(collect($tags)->pluck('id'));
        }

        return redirect()->route('logs.show', $log)
            ->with('success', 'ログが作成されました。');
    }

    /**
     * ログ編集フォーム表示
     */
    public function edit(Log $log)
    {
        Gate::authorize('update', $log);
        
        $log->load('tags');
        $allTags = Tag::orderBy('name')->get();
        
        return view('logs.edit', compact('log', 'allTags'));
    }

    /**
     * ログ更新
     */
    public function update(Request $request, Log $log)
    {
        Gate::authorize('update', $log);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'date' => 'required|date',
            'tags' => 'nullable|string',
        ]);

        $log->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'date' => $validated['date'],
        ]);

        // タグの更新
        $log->tags()->detach();
        if (!empty($validated['tags'])) {
            $tagNames = array_unique(array_filter(array_map('trim', explode(',', $validated['tags']))));
            $tags = Tag::findOrCreateByNames($tagNames);
            $log->tags()->attach(collect($tags)->pluck('id'));
        }

        return redirect()->route('logs.show', $log)
            ->with('success', 'ログが更新されました。');
    }

    /**
     * ログ削除
     */
    public function destroy(Log $log)
    {
        Gate::authorize('delete', $log);
        
        $log->delete();

        return redirect()->route('logs.index')
            ->with('success', 'ログが削除されました。');
    }

    /**
     * カレンダー表示
     */
    public function calendar()
    {
        return view('logs.calendar');
    }

    /**
     * カレンダー用APIエンドポイント
     */
    public function calendarEvents(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $logs = Log::forUser(Auth::id())
            ->betweenDates($start, $end)
            ->with('tags')
            ->get();

        $events = $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'title' => $log->title,
                'start' => $log->date->format('Y-m-d'),
                'url' => route('logs.show', $log),
                'extendedProps' => [
                    'tags' => $log->tags->pluck('name')->toArray(),
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * 特定日のログ一覧
     */
    public function byDate($date)
    {
        $logs = Log::with('tags')
            ->forUser(Auth::id())
            ->byDate($date)
            ->orderByDateDesc()
            ->get();

        return view('logs.by-date', compact('logs', 'date'));
    }
}