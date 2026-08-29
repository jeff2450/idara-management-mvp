<?php

namespace App\Http\Middleware;

use App\Models\Department;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Inakagua kila request dhidi ya idara husika - angalia architecture.md §2.1.
 * Inatumika kwenye routes zenye {department} route-model-binding. Admin
 * anapita bila kikwazo; kiongozi/mwanachama lazima ahusike na idara hiyo
 * (department_user), la sivyo 403.
 */
class EnsureDepartmentAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $department = $request->route('department');

        if ($department instanceof Department) {
            $user = $request->user();

            abort_unless(
                $user && $user->belongsToDepartment($department),
                403,
                'Huna ruhusa ya kufikia idara hii.'
            );
        }

        return $next($request);
    }
}
