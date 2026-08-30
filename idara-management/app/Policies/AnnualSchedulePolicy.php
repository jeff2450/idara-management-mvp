<?php

namespace App\Policies;

use App\Models\AnnualSchedule;
use App\Models\Department;
use App\Models\User;

/**
 * Angalia app/Policies/DepartmentPolicy.php kwa muundo asilia. Kiongozi wa
 * idara husika (au Admin) ndiye pekee anayeweza kuunda/kuhariri/kufuta
 * vipengele vya ratiba - member ana `view` pekee (kupitia Global Scope).
 */
class AnnualSchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return true; // itachujwa na Global Scope ya BelongsToDepartment
    }

    public function view(User $user, AnnualSchedule $schedule): bool
    {
        return $user->isAdmin() || $user->belongsToDepartment($schedule->department);
    }

    public function create(User $user, Department $department): bool
    {
        return $user->isAdmin() || $user->leadsDepartment($department);
    }

    public function update(User $user, AnnualSchedule $schedule): bool
    {
        return $user->isAdmin() || $user->leadsDepartment($schedule->department);
    }

    public function delete(User $user, AnnualSchedule $schedule): bool
    {
        return $user->isAdmin() || $user->leadsDepartment($schedule->department);
    }
}
