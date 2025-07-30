<?php

namespace Tests\Feature;

use App\Models\Log;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
    }

    /** @test */
    public function authenticated_user_can_create_log(): void
    {
        $logData = [
            'title' => 'Test Log',
            'content' => 'This is a test log content.',
            'date' => '2024-01-15',
            'tags' => 'Laravel, PHP, Testing',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('logs.store'), $logData);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('logs', [
            'title' => 'Test Log',
            'content' => 'This is a test log content.',
            'user_id' => $this->user->id,
        ]);
        
        // 日付が正しく保存され、タグが作成・関連付けされることを確認
        $log = Log::where('title', 'Test Log')->first();
        $this->assertEquals('2024-01-15', $log->date->format('Y-m-d'));
        $this->assertEquals(3, $log->tags()->count());
        $this->assertTrue($log->tags->pluck('name')->contains('Laravel'));
        $this->assertTrue($log->tags->pluck('name')->contains('PHP'));
        $this->assertTrue($log->tags->pluck('name')->contains('Testing'));
    }

    /** @test */
    public function guest_user_cannot_create_log(): void
    {
        $logData = [
            'title' => 'Test Log',
            'content' => 'This is a test log content.',
            'date' => '2024-01-15',
        ];

        $response = $this->post(route('logs.store'), $logData);

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
        
        $this->assertDatabaseMissing('logs', [
            'title' => 'Test Log',
        ]);
    }

    /** @test */
    public function user_can_edit_own_log(): void
    {
        $log = Log::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('logs.edit', $log));

        $response->assertStatus(200);
        $response->assertSee($log->title);
    }

    /** @test */
    public function user_cannot_edit_other_users_log(): void
    {
        $log = Log::factory()->create(['user_id' => $this->otherUser->id]);

        $response = $this->actingAs($this->user)
            ->get(route('logs.edit', $log));

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_update_own_log(): void
    {
        $log = Log::factory()->create(['user_id' => $this->user->id]);
        
        $updateData = [
            'title' => 'Updated Log Title',
            'content' => 'Updated content',
            'date' => '2024-01-20',
            'tags' => 'Updated, Tags',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('logs.update', $log), $updateData);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('logs', [
            'id' => $log->id,
            'title' => 'Updated Log Title',
            'content' => 'Updated content',
        ]);
        
        $log->refresh();
        $this->assertEquals('2024-01-20', $log->date->format('Y-m-d'));

        // タグが更新されることを確認
        $this->assertEquals(2, $log->tags()->count());
        $this->assertTrue($log->tags->pluck('name')->contains('Updated'));
        $this->assertTrue($log->tags->pluck('name')->contains('Tags'));
    }

    /** @test */
    public function user_cannot_update_other_users_log(): void
    {
        $log = Log::factory()->create(['user_id' => $this->otherUser->id]);
        
        $updateData = [
            'title' => 'Malicious Update',
            'content' => 'Malicious content',
            'date' => '2024-01-20',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('logs.update', $log), $updateData);

        $response->assertStatus(403);
        
        $this->assertDatabaseMissing('logs', [
            'id' => $log->id,
            'title' => 'Malicious Update',
        ]);
    }

    /** @test */
    public function user_can_delete_own_log(): void
    {
        $log = Log::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('logs.destroy', $log));

        $response->assertStatus(302);
        $response->assertRedirect(route('logs.index'));
        
        $this->assertDatabaseMissing('logs', [
            'id' => $log->id,
        ]);
    }

    /** @test */
    public function user_cannot_delete_other_users_log(): void
    {
        $log = Log::factory()->create(['user_id' => $this->otherUser->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('logs.destroy', $log));

        $response->assertStatus(403);
        
        $this->assertDatabaseHas('logs', [
            'id' => $log->id,
        ]);
    }

    /** @test */
    public function duplicate_tags_are_not_created(): void
    {
        // 既存のタグを作成
        $existingTag = Tag::factory()->create(['name' => 'Laravel']);

        $logData = [
            'title' => 'Test Log',
            'content' => 'Test content',
            'date' => '2024-01-15',
            'tags' => 'Laravel, PHP, Laravel', // Laravelが重複
        ];

        $response = $this->actingAs($this->user)
            ->post(route('logs.store'), $logData);

        $response->assertStatus(302);

        // Laravelタグが1つだけ存在することを確認
        $this->assertEquals(1, Tag::where('name', 'Laravel')->count());
        
        // ログに正しいタグが関連付けられていることを確認
        $log = Log::where('title', 'Test Log')->first();
        $tagNames = $log->tags->pluck('name')->toArray();
        $this->assertContains('Laravel', $tagNames);
        $this->assertContains('PHP', $tagNames);
        $this->assertEquals(2, count($tagNames)); // 重複なしで2つ
    }

    /** @test */
    public function calendar_api_returns_correct_json_format(): void
    {
        $log1 = Log::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'First Log',
            'date' => '2024-01-15',
        ]);
        
        $log2 = Log::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Second Log',
            'date' => '2024-01-16',
        ]);

        // 他のユーザーのログ（表示されないはず）
        Log::factory()->create([
            'user_id' => $this->otherUser->id,
            'title' => 'Other User Log',
            'date' => '2024-01-15',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('api.calendar.events', [
                'start' => '2024-01-01',
                'end' => '2024-01-31',
            ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'title',
                'start',
                'url',
                'extendedProps' => [
                    'tags'
                ]
            ]
        ]);

        $events = $response->json();
        $this->assertCount(2, $events);
        
        $titles = collect($events)->pluck('title')->toArray();
        $this->assertContains('First Log', $titles);
        $this->assertContains('Second Log', $titles);
        $this->assertNotContains('Other User Log', $titles);
    }

    /** @test */
    public function markdown_script_tags_are_sanitized(): void
    {
        $maliciousContent = '# Heading

This is normal text.

<script>alert("XSS");</script>

More normal content.

<img src="x" onerror="alert(\'XSS\')">

**Bold text**';

        $log = Log::factory()->create([
            'user_id' => $this->user->id,
            'content' => $maliciousContent,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('logs.show', $log));

        $response->assertStatus(200);
        
        // scriptタグが除去されていることを確認
        $response->assertDontSee('<script>');
        $response->assertDontSee('alert("XSS")');
        $response->assertDontSee('<img src="x"');
        $response->assertDontSee('onerror');
        
        // 正常なコンテンツは表示されることを確認
        $response->assertSee('Heading');
        $response->assertSee('This is normal text');
        $response->assertSee('More normal content');
        $response->assertSee('Bold text');
    }

    /** @test */
    public function log_creation_validation(): void
    {
        // タイトルが必須
        $response = $this->actingAs($this->user)
            ->post(route('logs.store'), [
                'content' => 'Content without title',
                'date' => '2024-01-15',
            ]);

        $response->assertSessionHasErrors(['title']);

        // 日付が必須
        $response = $this->actingAs($this->user)
            ->post(route('logs.store'), [
                'title' => 'Title without date',
                'content' => 'Some content',
            ]);

        $response->assertSessionHasErrors(['date']);

        // 無効な日付形式
        $response = $this->actingAs($this->user)
            ->post(route('logs.store'), [
                'title' => 'Title with invalid date',
                'content' => 'Some content',
                'date' => 'invalid-date',
            ]);

        $response->assertSessionHasErrors(['date']);
    }

    /** @test */
    public function tags_with_whitespace_are_handled_correctly(): void
    {
        $logData = [
            'title' => 'Test Log',
            'content' => 'Test content',
            'date' => '2024-01-15',
            'tags' => '  Laravel  ,  PHP  ,  Testing  ', // 余分な空白
        ];

        $response = $this->actingAs($this->user)
            ->post(route('logs.store'), $logData);

        $response->assertStatus(302);

        $log = Log::where('title', 'Test Log')->first();
        $tagNames = $log->tags->pluck('name')->toArray();
        
        // 空白が除去されていることを確認
        $this->assertContains('Laravel', $tagNames);
        $this->assertContains('PHP', $tagNames);
        $this->assertContains('Testing', $tagNames);
        $this->assertEquals(3, count($tagNames));
    }

    /** @test */
    public function empty_tags_are_filtered_out(): void
    {
        $logData = [
            'title' => 'Test Log',
            'content' => 'Test content',
            'date' => '2024-01-15',
            'tags' => 'Laravel,,PHP,  ,Testing', // 空のタグと空白のみのタグ
        ];

        $response = $this->actingAs($this->user)
            ->post(route('logs.store'), $logData);

        $response->assertStatus(302);

        $log = Log::where('title', 'Test Log')->first();
        $tagNames = $log->tags->pluck('name')->toArray();
        
        // 有効なタグのみが作成されることを確認
        $this->assertContains('Laravel', $tagNames);
        $this->assertContains('PHP', $tagNames);
        $this->assertContains('Testing', $tagNames);
        $this->assertEquals(3, count($tagNames));
    }

    /** @test */
    public function guest_cannot_access_log_pages(): void
    {
        $log = Log::factory()->create(['user_id' => $this->user->id]);

        // ログ一覧
        $response = $this->get(route('logs.index'));
        $response->assertRedirect(route('login'));

        // ログ詳細
        $response = $this->get(route('logs.show', $log));
        $response->assertRedirect(route('login'));

        // ログ作成フォーム
        $response = $this->get(route('logs.create'));
        $response->assertRedirect(route('login'));

        // ログ編集フォーム
        $response = $this->get(route('logs.edit', $log));
        $response->assertRedirect(route('login'));

        // カレンダー
        $response = $this->get(route('logs.calendar'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function log_can_be_associated_with_multiple_tags(): void
    {
        $logData = [
            'title' => 'Multi-tag Log',
            'content' => 'This log has multiple tags',
            'date' => '2024-01-15',
            'tags' => 'Laravel, PHP, Testing, Vue.js, MySQL',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('logs.store'), $logData);

        $response->assertStatus(302);

        $log = Log::where('title', 'Multi-tag Log')->first();
        $this->assertEquals(5, $log->tags()->count());
        
        $tagNames = $log->tags->pluck('name')->toArray();
        $this->assertContains('Laravel', $tagNames);
        $this->assertContains('PHP', $tagNames);
        $this->assertContains('Testing', $tagNames);
        $this->assertContains('Vue.js', $tagNames);
        $this->assertContains('MySQL', $tagNames);
    }

    /** @test */
    public function user_can_view_own_log(): void
    {
        $log = Log::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('logs.show', $log));

        $response->assertStatus(200);
        $response->assertSee($log->title);
    }

    /** @test */
    public function user_cannot_view_other_users_log(): void
    {
        $log = Log::factory()->create(['user_id' => $this->otherUser->id]);

        $response = $this->actingAs($this->user)
            ->get(route('logs.show', $log));

        $response->assertStatus(403);
    }
}