<?php

namespace App\Models;

use App\Models\Scopes\DepartmentVisibilityScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Idara (Department). Angalia architecture.md §2.2 - idara ni configurable
 * (rows za database), siyo hardcoded. Data ya awali (Watoto, Wamama, Vijana,
 * Kusifu na Kuabudu, Mashemasi) inaingizwa na DepartmentSeeder pekee - kwenye
 * code hakuna orodha ngumu (hardcoded) ya majina ya idara.
 */
class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    protected static function booted(): void
    {
        // Global Scope: mtumiaji asiye admin anaona idara zake pekee kwenye
        // orodha (Department::all()/index) - angalia architecture-essentials.md
        // "Msingi wa Data Scoping". Admin (na queries za ndani ya console/seeder
        // ambako hakuna auth) haziathiriwi.
        static::addGlobalScope(new DepartmentVisibilityScope);

        static::creating(function (Department $department) {
            if (blank($department->slug)) {
                $department->slug = Str::slug($department->name);
            }
        });
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'department_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function leaders(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'leader');
    }

    public function members(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'member');
    }

    /**
     * Awamu ya 3 (prd.md §5.3) - Ratiba ya Mwaka, Shughuli, na Ripoti za
     * idara hii. Modeli hizi tatu zinatumia trait `BelongsToDepartment`
     * (Global Scope), hivyo `$department->annualSchedules` daima inarudisha
     * rekodi za idara hii pekee bila hatari ya kuvuja kwenda idara nyingine.
     */
    public function annualSchedules(): HasMany
    {
        return $this->hasMany(AnnualSchedule::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
