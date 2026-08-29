<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentMemberRequest;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

/**
 * Kuongeza/kuondoa Kiongozi au Mwanachama kwenye idara. Angalia
 * architecture.md §2.1 na §2.3.
 *
 * Note: hii tayari inalindwa mara mbili - middleware ya 'department.access'
 * kwenye routes/web.php (mtumiaji lazima ahusike na idara hii, au awe admin)
 * na Form Request (StoreDepartmentMemberRequest) inayothibitisha ruhusa
 * mahususi ya "manageMembers" na "leader ni Admin pekee".
 */
class DepartmentMemberController extends Controller
{
    public function store(StoreDepartmentMemberRequest $request, Department $department): RedirectResponse
    {
        $data = $request->validated();

        $user = $data['mode'] === 'existing'
            ? User::where('email', $data['email'])->firstOrFail()
            : User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
            ]);

        $department->users()->syncWithoutDetaching([
            $user->id => ['role' => $data['role']],
        ]);

        $user->syncGlobalRoleFromDepartments();

        $roleLabel = $data['role'] === 'leader' ? 'Kiongozi' : 'Mwanachama';

        return redirect()
            ->route('departments.show', $department)
            ->with('status', "{$user->name} ameongezwa kwenye '{$department->name}' kama {$roleLabel}.");
    }

    public function destroy(Department $department, User $user): RedirectResponse
    {
        $this->authorize('manageMembers', $department);

        $department->users()->detach($user->id);
        $user->syncGlobalRoleFromDepartments();

        return redirect()
            ->route('departments.show', $department)
            ->with('status', "{$user->name} ameondolewa kwenye '{$department->name}'.");
    }
}
