<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

/**
 * Roles 3 pekee - angalia architecture-essentials.md "Roles 3 Pekee".
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['admin', 'idara_leader', 'member'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }
}
