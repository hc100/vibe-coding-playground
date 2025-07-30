<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('タグ一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($tags->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($tags as $tag)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex justify-between items-center">
                                        <h3 class="text-lg font-medium text-gray-900">
                                            <a href="{{ route('tags.show', $tag) }}" class="hover:text-blue-600">
                                                {{ $tag->name }}
                                            </a>
                                        </h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $tag->logs_count }} ログ
                                        </span>
                                    </div>
                                    <div class="mt-2">
                                        <a href="{{ route('logs.index', ['tag' => $tag->name]) }}" 
                                           class="text-sm text-indigo-600 hover:text-indigo-900">
                                            このタグのログを見る →
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-500 text-lg">タグがありません。</p>
                            <a href="{{ route('logs.create') }}" 
                               class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                ログを作成してタグを追加
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>