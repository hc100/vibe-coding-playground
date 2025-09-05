<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'date',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * ログを投稿したユーザー
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ログに関連付けられたタグ
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * 特定ユーザーのログを取得
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * 特定日付のログを取得
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * 日付範囲でログを取得
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate);
    }

    /**
     * 特定タグを持つログを取得
     */
    public function scopeWithTag($query, $tagName)
    {
        return $query->whereHas('tags', function ($q) use ($tagName) {
            $q->where('name', $tagName);
        });
    }

    /**
     * 新しい順でソート
     */
    public function scopeOrderByDateDesc($query)
    {
        return $query->orderBy('date', 'desc')->orderBy('created_at', 'desc');
    }

    /**
     * 古い順でソート
     */
    public function scopeOrderByDateAsc($query)
    {
        return $query->orderBy('date', 'asc')->orderBy('created_at', 'asc');
    }
}