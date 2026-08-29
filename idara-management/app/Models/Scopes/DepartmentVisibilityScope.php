<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Inatumika kwenye Department::class pekee. Inazuia mtumiaji asiye admin
 * kuona idara asizomo - "member anaona idara alizomo tu" (prd.md §6.3).
 * Admin anaona idara zote.
 */
class DepartmentVisibilityScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (! Auth::check()) {
            // Hakuna mtumiaji aliyeingia (mfano: seeder/console) - usichuje.
            return;
        }

        $user = Auth::user();

        if ($user->isAdmin()) {
            return;
        }

        $builder->whereHas('users', function (Builder $query) use ($user) {
            $query->where('users.id', $user->id);
        });
    }
}
