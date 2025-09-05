<?php

namespace Database\Seeders;

use App\Models\Log;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // テストユーザー作成
        $user1 = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $user2 = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
        ]);

        // タグ作成
        $tags = [
            'Laravel', 'PHP', 'JavaScript', 'Vue.js', 'React', 'MySQL', 
            'Redis', 'Docker', 'AWS', 'Git', 'Testing', 'API開発',
            '勉強', '読書', 'プログラミング', 'データベース', 'フロントエンド',
            'バックエンド', 'DevOps', 'セキュリティ'
        ];

        foreach ($tags as $tagName) {
            Tag::create(['name' => $tagName]);
        }

        // ログ作成
        $this->createLogsForUser($user1, 15);
        $this->createLogsForUser($user2, 10);
    }

    private function createLogsForUser($user, $count)
    {
        $tags = Tag::all();
        
        for ($i = 0; $i < $count; $i++) {
            $log = Log::create([
                'user_id' => $user->id,
                'title' => $this->generateLogTitle(),
                'content' => $this->generateLogContent(),
                'date' => now()->subDays(rand(0, 30))->format('Y-m-d'),
            ]);

            // ランダムにタグを割り当て
            $randomTags = $tags->random(rand(1, 4));
            $log->tags()->attach($randomTags);
        }
    }

    private function generateLogTitle()
    {
        $titles = [
            'Laravel 11の新機能を学習',
            'Vue.js コンポーネント設計',
            'MySQL インデックス最適化',
            'Docker コンテナ構築',
            'AWS デプロイメント',
            'PHPUnit テスト記述',
            'Redis キャッシュ実装',
            'API レスポンス改善',
            'フロントエンド パフォーマンス調整',
            'データベース設計見直し',
            '新しいライブラリ調査',
            'コードレビュー対応',
            'セキュリティ脆弱性修正',
            'ユーザビリティ改善',
            'プロジェクト進捗ミーティング',
            '技術書読書感想',
            'チーム開発ワークフロー改善',
            'エラーハンドリング実装',
            'ログ出力最適化',
            'デバッグ作業'
        ];

        return $titles[array_rand($titles)];
    }

    private function generateLogContent()
    {
        $contents = [
            "# 今日の学習内容\n\n新しい技術について調査しました。\n\n## 学んだこと\n\n- 基本的な概念\n- 実装方法\n- ベストプラクティス\n\n```php\n<?php\necho 'Hello World';\n```\n\n明日はより詳細な実装に取り組む予定です。",
            
            "## 作業ログ\n\n今日はバグ修正に時間を費やしました。\n\n**問題：**\n- エラーが発生していた箇所を特定\n- 原因を分析\n\n**解決策：**\n1. コードの見直し\n2. テストケース追加\n3. リファクタリング\n\n次回は予防策も検討したいと思います。",
            
            "### 読書メモ\n\n技術書を読んで印象に残った内容をまとめます。\n\n> 良いコードとは、読みやすく保守しやすいコードである\n\n**重要なポイント：**\n- 命名規則の統一\n- 適切なコメント\n- 単一責任の原則\n\n実際のプロジェクトでも意識して実装していきたいです。",
            
            "## プロジェクト進捗\n\n**完了したタスク：**\n- [x] 要件定義\n- [x] 基本設計\n- [x] 画面設計\n\n**進行中のタスク：**\n- [ ] 実装\n- [ ] テスト\n\n**今後の予定：**\n- 来週中にMVP完成予定\n- ユーザーテスト実施\n\n順調に進んでいます。",
            
            "# 技術調査結果\n\n新しいツールについて調査した結果をまとめます。\n\n## メリット\n- 開発効率の向上\n- 保守性の改善\n- パフォーマンスの最適化\n\n## デメリット\n- 学習コスト\n- 既存システムとの互換性\n\n総合的に判断して、導入を検討する価値があると思います。"
        ];

        return $contents[array_rand($contents)];
    }
}