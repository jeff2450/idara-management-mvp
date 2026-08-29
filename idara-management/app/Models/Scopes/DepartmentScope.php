<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Global Scope ya JUMLA kwa modeli za Awamu ya 2/3 zenye safu ya
 * `department_id` moja kwa moja (SmsLog, Letter, DepartmentTransaction,
 * ActivityLog, Report - angalia architecture.md §3). Inachuja kiotomatiki
 * kwa idara za mtumiaji aliyeingia, ili data isivuke kati ya idara
 * (architecture-essentials.md "Msingi wa Data Scoping").
 *
 * Tumia pamoja na trait `App\Models\Concerns\BelongsToDepartment`:
 *
 *   class SmsLog extends Model
 *   {
 *       use BelongsToDepartment;
 *   }
 */
class DepartmentScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (! Auth::check()) {
            return;
        }

        $user = Auth::user();

        if ($user->isAdmin()) {
            return;
        }

        $builder->whereIn($model->getTable().'.department_id', $user->departmentIds());
    }
}
