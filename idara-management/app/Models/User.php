<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Idara zote ambazo mtumiaji huyu anahusika nazo (kiongozi au mwanachama).
     * Angalia architecture.md §2.1 - `department_user` ndiyo msingi wa scoping.
     */
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'department_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /** Idara ambazo mtumiaji huyu ni Kiongozi. */
    public function ledDepartments(): BelongsToMany
    {
        return $this->departments()->wherePivot('role', 'leader');
    }

    /** Idara ambazo mtumiaji huyu ni Mwanachama (siyo kiongozi). */
    public function memberDepartments(): BelongsToMany
    {
        return $this->departments()->wherePivot('role', 'member');
    }

    /** Orodha ya department_id ambazo mtumiaji anahusika nazo - kwa scoping. */
    public function departmentIds(): array
    {
        return $this->departments()->pluck('departments.id')->all();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /** Je, mtumiaji ni kiongozi wa idara husika? */
    public function leadsDepartment(Department $department): bool
    {
        return $this->ledDepartments()->where('departments.id', $department->id)->exists();
    }

    /** Je, mtumiaji anahusika (kiongozi au mwanachama) na idara husika? */
    public function belongsToDepartment(Department $department): bool
    {
        return $this->isAdmin() || $this->departments()->where('departments.id', $department->id)->exists();
    }

    /**
     * `spatie/laravel-permission` role ni ya jumla (system-wide) - inatumika
     * kutofautisha "admin" (ana ufikiaji wa mfumo mzima) dhidi ya "idara_leader"
     * (ni kiongozi wa idara moja au zaidi) dhidi ya "member" wa kawaida. Role
     * halisi ya idara maalum (leader|member) inabaki kwenye `department_user`
     * pivot pekee. Tunaita hii baada ya kuongeza/kuondoa mtumiaji kwenye idara.
     */
    public function syncGlobalRoleFromDepartments(): void
    {
        if ($this->isAdmin()) {
            return;
        }

        if ($this->ledDepartments()->exists()) {
            $this->syncRoles(['idara_leader']);

            return;
        }

        if ($this->departments()->exists()) {
            $this->syncRoles(['member']);

            return;
        }

        $this->syncRoles([]);
    }
}
