<?php

namespace App\Policies;

use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\User;

class ActivityLogPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ActivityLog $log): bool
    {
        return $user->isAdmin() || $user->belongsToDepartment($log->department);
    }

    public function create(User $user, Department $department): bool
    {
        return $user->isAdmin() || $user->leadsDepartment($department);
    }
}
