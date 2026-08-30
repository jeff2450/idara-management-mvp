<?php

namespace App\Policies;

use App\Models\DepartmentTransaction;
use App\Models\User;
use App\Policies\Concerns\ChecksDepartmentLeadership;

/**
 * ANGALIZO MUHIMU - angalia architecture.md §5 na prd.md §7:
 * "ulinzi wa ziada kwa data ya Idara ya Watoto na miamala/fedha".
 *
 * Tofauti na Policy nyingine za Awamu ya 2/3 (SmsLog, Letter, n.k.) ambako
 * "mwanachama wa kawaida" anaruhusiwa kuona data ya idara yake, HAPA
 * hairuhusiwi - miamala ya fedha ni Kiongozi/Admin PEKEE, hata kwa idara
 * ambayo mwanachama huyo yumo. Hii ndiyo TransactionPolicy iliyotajwa wazi
 * kwenye architecture.md §5.
 */
class TransactionPolicy
{
    use ChecksDepartmentLeadership;

    public function viewAny(User $user, \App\Models\Department $department): bool
    {
        return $this->managesDepartment($user, $department);
    }

    public function view(User $user, DepartmentTransaction $transaction): bool
    {
        return $this->managesDepartment($user, $transaction->department);
    }

    public function create(User $user, \App\Models\Department $department): bool
    {
        return $this->managesDepartment($user, $department);
    }

    public function update(User $user, DepartmentTransaction $transaction): bool
    {
        return $this->managesDepartment($user, $transaction->department);
    }

    public function delete(User $user, DepartmentTransaction $transaction): bool
    {
        return $this->managesDepartment($user, $transaction->department);
    }
}
