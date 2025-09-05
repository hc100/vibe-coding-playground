<?php

namespace App\Policies;

use App\Models\Log;
use App\Models\User;

class LogPolicy
{
    /**
     * ログを閲覧できるか
     */
    public function view(User $user, Log $log): bool
    {
        return $user->id === $log->user_id;
    }

    /**
     * ログを作成できるか
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * ログを更新できるか
     */
    public function update(User $user, Log $log): bool
    {
        return $user->id === $log->user_id;
    }

    /**
     * ログを削除できるか
     */
    public function delete(User $user, Log $log): bool
    {
        return $user->id === $log->user_id;
    }
}