<?php

namespace App\Policies\Concerns;

use App\Models\Department;
use App\Models\User;

/**
 * Sheria ya msingi inayojirudia kwenye Policy nyingi za Awamu ya 2/3:
 * "Admin, au Kiongozi wa idara husika, ndiye anayeweza ku-manage" - angalia
 * architecture.md §5.
 */
trait ChecksDepartmentLeadership
{
    protected function managesDepartment(User $user, Department $department): bool
    {
        return $user->isAdmin() || $user->leadsDepartment($department);
    }

    protected function belongsToDepartment(User $user, Department $department): bool
    {
        return $user->belongsToDepartment($department);
    }
}
