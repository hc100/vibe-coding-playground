<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    public function __construct()
    {
        // 認証はルートで処理
    }

    /**
     * タグ一覧表示
     */
    public function index()
    {
        $tags = Tag::withCount(['logs' => function ($query) {
                $query->where('user_id', Auth::id());
            }])
            ->get()
            ->filter(function ($tag) {
                return $tag->logs_count > 0;
            })
            ->sortByDesc('logs_count')
            ->values();

        return view('tags.index', compact('tags'));
    }

    /**
     * 特定タグのログ一覧
     */
    public function show(Tag $tag)
    {
        $logs = $tag->logs()
            ->with('tags', 'user')
            ->where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('tags.show', compact('tag', 'logs'));
    }
}