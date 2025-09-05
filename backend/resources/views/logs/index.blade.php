<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('ログ一覧') }}
            </h2>
            <a href="{{ route('logs.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                新しいログ
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 検索・フィルターフォーム -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('logs.index') }}" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700">日付</label>
                            <input type="date" id="date" name="date" value="{{ request('date') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label for="tag" class="block text-sm font-medium text-gray-700">タグ</label>
                            <select id="tag" name="tag" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">全てのタグ</option>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->name }}" {{ request('tag') === $tag->name ? 'selected' : '' }}>
                                        {{ $tag->name }} ({{ $tag->logs_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="order" class="block text-sm font-medium text-gray-700">並び順</label>
                            <select id="order" name="order" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="desc" {{ request('order') === 'desc' ? 'selected' : '' }}>新しい順</option>
                                <option value="asc" {{ request('order') === 'asc' ? 'selected' : '' }}>古い順</option>
                            </select>
                        </div>
                        
                        <div>
                            <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                検索
                            </button>
                            <a href="{{ route('logs.index') }}" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                リセット
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ログ一覧 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($logs->count() > 0)
                        <div class="space-y-6">
                            @foreach($logs as $log)
                                <div class="border-b border-gray-200 pb-6 last:border-b-0">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                <a href="{{ route('logs.show', $log) }}" class="hover:text-blue-600">
                                                    {{ $log->title }}
                                                </a>
                                            </h3>
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ $log->date->format('Y年m月d日') }}
                                            </p>
                                            @if($log->content)
                                                <p class="text-gray-700 mt-2 line-clamp-3">
                                                    {{ Str::limit(strip_tags($log->content), 150) }}
                                                </p>
                                            @endif
                                            @if($log->tags->count() > 0)
                                                <div class="flex flex-wrap gap-2 mt-3">
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
                                        
                                        <div class="flex space-x-2 ml-4">
                                            <a href="{{ route('logs.edit', $log) }}" 
                                               class="text-indigo-600 hover:text-indigo-900 text-sm">編集</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6">
                            {{ $logs->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-500 text-lg">ログが見つかりませんでした。</p>
                            <a href="{{ route('logs.create') }}" 
                               class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                最初のログを作成
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>