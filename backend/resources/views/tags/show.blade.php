<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                タグ「{{ $tag->name }}」のログ
            </h2>
            <a href="{{ route('logs.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                新しいログ
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($logs->count() > 0)
                        <div class="mb-4">
                            <p class="text-sm text-gray-600">
                                {{ $logs->total() }} 件のログが見つかりました
                            </p>
                        </div>

                        <div class="space-y-6">
                            @foreach($logs as $log)
                                <div class="border-b border-gray-200 pb-6">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                <a href="{{ route('logs.show', $log) }}" class="hover:text-blue-600">
                                                    {{ $log->title }}
                                                </a>
                                            </h3>
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ $log->date->format('Y年m月d日') }}
                                                @if($log->user->name)
                                                    • by {{ $log->user->name }}
                                                @endif
                                            </p>
                                            @if($log->content)
                                                <p class="text-gray-700 mt-2 line-clamp-3">
                                                    {{ Str::limit(strip_tags($log->content), 150) }}
                                                </p>
                                            @endif
                                            @if($log->tags->count() > 1)
                                                <div class="flex flex-wrap gap-2 mt-3">
                                                    @foreach($log->tags as $logTag)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                                     {{ $logTag->id === $tag->id ? 'bg-blue-200 text-blue-900' : 'bg-gray-100 text-gray-800' }}">
                                                            @if($logTag->id !== $tag->id)
                                                                <a href="{{ route('tags.show', $logTag) }}">
                                                                    {{ $logTag->name }}
                                                                </a>
                                                            @else
                                                                {{ $logTag->name }}
                                                            @endif
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        
                                        @can('update', $log)
                                            <div class="flex space-x-2 ml-4">
                                                <a href="{{ route('logs.edit', $log) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900 text-sm">編集</a>
                                            </div>
                                        @endcan
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6">
                            {{ $logs->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-500 text-lg">このタグのログが見つかりませんでした。</p>
                            <a href="{{ route('logs.create') }}" 
                               class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                新しいログを作成
                            </a>
                        </div>
                    @endif

                    <!-- ナビゲーション -->
                    <div class="mt-8 pt-4 border-t border-gray-200">
                        <div class="flex justify-between">
                            <a href="{{ route('tags.index') }}" 
                               class="text-indigo-600 hover:text-indigo-900">
                                ← タグ一覧に戻る
                            </a>
                            <a href="{{ route('logs.index') }}" 
                               class="text-indigo-600 hover:text-indigo-900">
                                すべてのログを見る →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>