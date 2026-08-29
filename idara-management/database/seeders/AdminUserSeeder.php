<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Akaunti ya kwanza ya Admin - badilisha nenosiri mara moja baada ya
 * kuingia mara ya kwanza kwenye mazingira ya uzalishaji (production).
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@idara.test'],
            [
                'name' => 'Msimamizi Mkuu',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $admin->syncRoles(['admin']);
    }
}
