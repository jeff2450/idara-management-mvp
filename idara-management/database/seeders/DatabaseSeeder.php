<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Tartibu ni muhimu: roles kabla ya admin (anahitaji role 'admin'),
     * departments kabla ya kuweza kuwaunganisha viongozi/wanachama.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
            DepartmentSeeder::class,
        ]);
    }
}
