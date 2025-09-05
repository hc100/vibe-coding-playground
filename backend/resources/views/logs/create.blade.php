<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('新しいログを作成') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('logs.store') }}">
                        @csrf

                        <!-- タイトル -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">タイトル *</label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-500 @enderror">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 日付 -->
                        <div class="mb-4">
                            <label for="date" class="block text-sm font-medium text-gray-700">日付 *</label>
                            <input type="date" id="date" name="date" value="{{ old('date', request('date', date('Y-m-d'))) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('date') border-red-500 @enderror">
                            @error('date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- タグ -->
                        <div class="mb-4">
                            <label for="tags" class="block text-sm font-medium text-gray-700">タグ (カンマ区切り)</label>
                            <input type="text" id="tags" name="tags" value="{{ old('tags') }}" 
                                   placeholder="例: Laravel, PHP, 勉強"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('tags') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500">複数のタグをカンマ区切りで入力してください</p>
                            @error('tags')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            
                            @if($tags->count() > 0)
                                <div class="mt-2">
                                    <p class="text-sm text-gray-700 mb-2">既存のタグ:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($tags as $tag)
                                            <button type="button" onclick="addTag('{{ $tag->name }}')"
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200 cursor-pointer">
                                                {{ $tag->name }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- 本文 -->
                        <div class="mb-6">
                            <label for="content" class="block text-sm font-medium text-gray-700">本文 (Markdown)</label>
                            <textarea id="content" name="content" rows="12" placeholder="Markdown記法で記述できます。

例：
# 見出し
## 小見出し

**太字** や *斜体* が使えます。

- リスト項目1
- リスト項目2

```php
echo 'Hello World';
```

[リンク](https://example.com)"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('content') border-red-500 @enderror">{{ old('content') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">
                                Markdown記法で記述できます。見出し、リスト、コードブロック、リンクなどが使用可能です。
                            </p>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 送信ボタン -->
                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('logs.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                キャンセル
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                作成
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function addTag(tagName) {
            const tagsInput = document.getElementById('tags');
            const currentTags = tagsInput.value.split(',').map(tag => tag.trim()).filter(tag => tag);
            
            if (!currentTags.includes(tagName)) {
                if (currentTags.length > 0) {
                    tagsInput.value = currentTags.join(', ') + ', ' + tagName;
                } else {
                    tagsInput.value = tagName;
                }
            }
        }
    </script>
</x-app-layout>