<?php

namespace App\Policies;

use App\Models\SmsLog;
use App\Models\User;
use App\Policies\Concerns\ChecksDepartmentLeadership;

/**
 * Angalia architecture.md §2.4 na §5. Wanachama wa idara wanaweza kuona
 * historia ya SMS zilizotumwa (uwazi), lakini kutuma SMS ni Kiongozi/Admin
 * pekee.
 */
class SmsLogPolicy
{
    use ChecksDepartmentLeadership;

    public function viewAny(User $user, \App\Models\Department $department): bool
    {
        return $this->belongsToDepartment($user, $department);
    }

    public function view(User $user, SmsLog $smsLog): bool
    {
        return $this->belongsToDepartment($user, $smsLog->department);
    }

    public function create(User $user, \App\Models\Department $department): bool
    {
        return $this->managesDepartment($user, $department);
    }
}
