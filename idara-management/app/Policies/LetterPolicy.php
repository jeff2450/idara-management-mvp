<?php

namespace App\Policies;

use App\Models\Letter;
use App\Models\User;
use App\Policies\Concerns\ChecksDepartmentLeadership;

/**
 * Angalia architecture.md §2.5. Wanachama wanaweza kuona barua
 * zilizozalishwa kwa idara yao; kuzalisha barua mpya ni Kiongozi/Admin pekee.
 */
class LetterPolicy
{
    use ChecksDepartmentLeadership;

    public function viewAny(User $user, \App\Models\Department $department): bool
    {
        return $this->belongsToDepartment($user, $department);
    }

    public function view(User $user, Letter $letter): bool
    {
        return $this->belongsToDepartment($user, $letter->department);
    }

    public function create(User $user, \App\Models\Department $department): bool
    {
        return $this->managesDepartment($user, $department);
    }
}
