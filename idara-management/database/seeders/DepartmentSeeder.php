<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

/**
 * Idara 5 za awali zilizotajwa kwenye SRS §2. Hizi ni DATA (rows), siyo
 * orodha ngumu (hardcoded) kwenye code - Admin anaweza kuongeza nyingine
 * baadaye kupitia UI (angalia architecture.md §2.2).
 */
class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Idara ya Watoto', 'description' => 'Huduma na mafunzo kwa watoto.'],
            ['name' => 'Idara ya Wamama', 'description' => 'Huduma na mafunzo kwa wamama.'],
            ['name' => 'Idara ya Vijana', 'description' => 'Huduma na mafunzo kwa vijana.'],
            ['name' => 'Idara ya Kusifu na Kuabudu', 'description' => 'Huduma ya muziki, kusifu na kuabudu.'],
            ['name' => 'Idara ya Mashemasi', 'description' => 'Huduma ya kimashemasi na ustawi wa kanisa.'],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(['name' => $department['name']], $department);
        }
    }
}
