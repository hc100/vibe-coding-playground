<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $log->title }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('logs.edit', $log) }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    編集
                </a>
                <form method="POST" action="{{ route('logs.destroy', $log) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('本当に削除しますか？')"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        削除
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- メタ情報 -->
                    <div class="mb-6 pb-4 border-b border-gray-200">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500">
                                    日付: {{ $log->date->format('Y年m月d日') }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    作成日時: {{ $log->created_at->format('Y年m月d日 H:i') }}
                                </p>
                                @if($log->updated_at->ne($log->created_at))
                                    <p class="text-sm text-gray-500">
                                        更新日時: {{ $log->updated_at->format('Y年m月d日 H:i') }}
                                    </p>
                                @endif
                            </div>
                            
                            @if($log->tags->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($log->tags as $tag)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <a href="{{ route('logs.index', ['tag' => $tag->name]) }}">
                                                {{ $tag->name }}
                                            </a>
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 本文 -->
                    @if($log->content)
                        <div class="prose max-w-none">
                            {!! $renderedContent !!}
                        </div>
                    @else
                        <p class="text-gray-500 italic">本文はありません。</p>
                    @endif

                    <!-- ナビゲーション -->
                    <div class="mt-8 pt-4 border-t border-gray-200">
                        <div class="flex justify-between">
                            <a href="{{ route('logs.index') }}" 
                               class="text-indigo-600 hover:text-indigo-900">
                                ← ログ一覧に戻る
                            </a>
                            <a href="{{ route('logs.by-date', $log->date->format('Y-m-d')) }}" 
                               class="text-indigo-600 hover:text-indigo-900">
                                同じ日のログを見る →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>