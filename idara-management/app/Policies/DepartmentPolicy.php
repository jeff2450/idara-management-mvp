<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;

/**
 * Angalia architecture.md §5 - Policy classes kwa kila model muhimu.
 * Uundaji/uhariri/ufutaji wa idara ni Admin pekee (prd.md §10: swali hili
 * limejibiwa kama Admin pekee kwa MVP - angalia README.md kwa maelezo zaidi).
 */
class DepartmentPolicy
{
    public function viewAny(User $user): bool
    {
        // Kila mtumiaji aliyeingia anaweza kuona orodha (itachujwa na
        // DepartmentVisibilityScope kiotomatiki kulingana na role yake).
        return true;
    }

    public function view(User $user, Department $department): bool
    {
        return $user->belongsToDepartment($department);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Department $department): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Department $department): bool
    {
        return $user->isAdmin();
    }

    /**
     * Kuongeza/kuondoa kiongozi/mwanachama kwenye idara. Sheria ya ziada
     * "leader ni Admin pekee" inathibitishwa kwenye
     * StoreDepartmentMemberRequest::withValidator().
     */
    public function manageMembers(User $user, Department $department): bool
    {
        return $user->isAdmin() || $user->leadsDepartment($department);
    }
}
