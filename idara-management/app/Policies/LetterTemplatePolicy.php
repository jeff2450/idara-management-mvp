<?php

namespace App\Policies;

use App\Models\LetterTemplate;
use App\Models\User;

/**
 * Templates ni za JUMLA (siyo za idara moja) - kutengeneza/kuhariri/kufuta ni
 * Admin pekee. Kiongozi yeyote anaweza KUZITUMIA (angalia LetterPolicy) bila
 * kuweza kuzihariri - angalia architecture.md §2.5.
 */
class LetterTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        // Kiongozi/Admin wanahitaji kuona orodha ili kuchagua template
        // watakayotumia kuzalisha barua.
        return $user->isAdmin() || $user->ledDepartments()->exists();
    }

    public function view(User $user, LetterTemplate $letterTemplate): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, LetterTemplate $letterTemplate): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, LetterTemplate $letterTemplate): bool
    {
        return $user->isAdmin();
    }
}
