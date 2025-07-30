<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * タグに関連付けられたログ
     */
    public function logs(): BelongsToMany
    {
        return $this->belongsToMany(Log::class);
    }

    /**
     * タグ名の配列から既存タグを検索または新規作成
     */
    public static function findOrCreateByNames(array $tagNames): array
    {
        $tags = [];
        foreach ($tagNames as $tagName) {
            $tagName = trim($tagName);
            if (!empty($tagName)) {
                $tags[] = static::firstOrCreate(['name' => $tagName]);
            }
        }
        return $tags;
    }

    /**
     * 人気のタグを取得（使用ログ数の多い順）
     */
    public function scopePopular($query, $limit = 10)
    {
        return $query->withCount('logs')
            ->orderBy('logs_count', 'desc')
            ->limit($limit);
    }

    /**
     * 名前でタグを検索
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', '%' . $term . '%');
    }
}