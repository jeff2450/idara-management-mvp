<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Report $report): bool
    {
        return $user->isAdmin() || $user->belongsToDepartment($report->department);
    }

    /** Kiongozi anaweza kuomba ripoti ya idara yake wakati wowote, siyo cron pekee. */
    public function generate(User $user, Department $department): bool
    {
        return $user->isAdmin() || $user->leadsDepartment($department);
    }
}
