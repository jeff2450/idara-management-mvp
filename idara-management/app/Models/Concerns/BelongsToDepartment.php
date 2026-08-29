<?php

namespace App\Models\Concerns;

use App\Models\Department;
use App\Models\Scopes\DepartmentScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait ya kutumiwa na modeli zote zenye safu ya `department_id` (SmsLog,
 * Letter, DepartmentTransaction, ActivityLog, Report - Awamu ya 2/3).
 * Angalia architecture-essentials.md "Vitu vya Kuepuka": "Usisahau Global
 * Scope kwenye model mpya - hii ndiyo chanzo namba 1 cha data leakage".
 */
trait BelongsToDepartment
{
    protected static function bootBelongsToDepartment(): void
    {
        static::addGlobalScope(new DepartmentScope);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
