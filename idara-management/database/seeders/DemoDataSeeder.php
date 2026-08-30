<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\AnnualSchedule;
use App\Models\Department;
use App\Models\DepartmentTransaction;
use App\Models\Report;
use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Departments exist with English/Swahili labels
        $departmentsData = [
            [
                'name' => 'Idara ya Watoto (Children)',
                'slug' => 'idara-ya-watoto',
                'description' => 'Huduma na mafunzo kwa watoto.',
                'is_sensitive' => true,
                'target_members' => 82,
                'progress' => 82,
                'leader_name' => 'Grace Peter',
                'leader_email' => 'grace@idara.test',
            ],
            [
                'name' => 'Idara ya Wamama (Women)',
                'slug' => 'idara-ya-wamama',
                'description' => 'Huduma na mafunzo kwa wamama.',
                'is_sensitive' => false,
                'target_members' => 145,
                'progress' => 90,
                'leader_name' => 'Rehema John',
                'leader_email' => 'rehema@idara.test',
            ],
            [
                'name' => 'Idara ya Vijana (Youth)',
                'slug' => 'idara-ya-vijana',
                'description' => 'Huduma na mafunzo kwa vijana.',
                'is_sensitive' => false,
                'target_members' => 156,
                'progress' => 78,
                'leader_name' => 'David Peter',
                'leader_email' => 'david@idara.test',
            ],
            [
                'name' => 'Idara ya Kusifu na Kuabudu (Worship)',
                'slug' => 'idara-ya-kusifu-na-kuabudu',
                'description' => 'Huduma ya muziki, kusifu na kuabudu.',
                'is_sensitive' => false,
                'target_members' => 38,
                'progress' => 63,
                'leader_name' => 'Michael John',
                'leader_email' => 'michael@idara.test',
            ],
            [
                'name' => 'Idara ya Mashemasi (Deacons)',
                'slug' => 'idara-ya-mashemasi',
                'description' => 'Huduma ya kimashemasi na ustawi wa kanisa.',
                'is_sensitive' => false,
                'target_members' => 24,
                'progress' => 60,
                'leader_name' => 'Joseph Paul',
                'leader_email' => 'joseph@idara.test',
            ],
        ];

        $departments = [];

        foreach ($departmentsData as $data) {
            $dept = Department::withoutGlobalScopes()->where('slug', $data['slug'])->first();
            if (! $dept) {
                $dept = Department::withoutGlobalScopes()->where('name', 'like', explode(' (', $data['name'])[0].'%')->first();
            }

            if ($dept) {
                $dept->update([
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'description' => $data['description'],
                    'is_sensitive' => $data['is_sensitive'],
                ]);
            } else {
                $dept = Department::create([
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'description' => $data['description'],
                    'is_sensitive' => $data['is_sensitive'],
                ]);
            }

            // Create Leader
            $leader = User::firstOrCreate(
                ['email' => $data['leader_email']],
                [
                    'name' => $data['leader_name'],
                    'phone' => '0767 '.rand(100, 999).' '.rand(100, 999),
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );
            $leader->syncRoles(['idara_leader']);

            if (! $dept->leaders()->where('users.id', $leader->id)->exists()) {
                $dept->users()->syncWithoutDetaching([$leader->id => ['role' => 'leader']]);
            }

            $departments[$data['slug']] = $dept;
        }

        $youthDept = $departments['idara-ya-vijana'];

        // 2. Specific Members for Idara ya Vijana
        $youthMembersData = [
            ['name' => 'Michael John', 'email' => 'michael.john@idara.test', 'phone' => '0712 345 678', 'created_at' => Carbon::parse('2026-01-15')],
            ['name' => 'Neema Paul', 'email' => 'neema.paul@idara.test', 'phone' => '0714 567 890', 'created_at' => Carbon::parse('2026-02-20')],
            ['name' => 'David Peter', 'email' => 'david.peter.member@idara.test', 'phone' => '0767 123 456', 'created_at' => Carbon::parse('2026-03-05')],
            ['name' => 'Bahati Grace', 'email' => 'bahati.grace@idara.test', 'phone' => '0716 789 123', 'created_at' => Carbon::parse('2026-04-12')],
            ['name' => 'Emmanuel Eliu', 'email' => 'emmanuel.eliu@idara.test', 'phone' => '0754 321 987', 'created_at' => Carbon::parse('2026-05-18')],
            ['name' => 'Sarah Joseph', 'email' => 'sarah.joseph@idara.test', 'phone' => '0782 112 233', 'created_at' => Carbon::parse('2026-06-01')],
            ['name' => 'Baraka James', 'email' => 'baraka.james@idara.test', 'phone' => '0743 445 566', 'created_at' => Carbon::parse('2026-06-15')],
        ];

        foreach ($youthMembersData as $m) {
            $user = User::firstOrCreate(
                ['email' => $m['email']],
                [
                    'name' => $m['name'],
                    'phone' => $m['phone'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'created_at' => $m['created_at'],
                ]
            );
            $user->syncRoles(['member']);
            $youthDept->users()->syncWithoutDetaching([$user->id => ['role' => 'member']]);
        }

        // 3. Upcoming Activities for Idara ya Vijana (September 2026)
        $youthSchedules = [
            [
                'title' => 'Youth Meeting',
                'description' => '10:00 AM - 12:00 PM @ Main Hall',
                'planned_year' => 2026,
                'planned_month' => 9,
                'status' => 'pending',
            ],
            [
                'title' => 'Bible Study',
                'description' => '04:00 PM - 05:30 PM @ Youth Room',
                'planned_year' => 2026,
                'planned_month' => 9,
                'status' => 'pending',
            ],
            [
                'title' => 'Community Outreach',
                'description' => '09:00 AM - 01:00 PM @ Mwenge Area',
                'planned_year' => 2026,
                'planned_month' => 9,
                'status' => 'pending',
            ],
            [
                'title' => 'Annual Youth Event',
                'description' => '02:00 PM - 06:00 PM @ Main Hall',
                'planned_year' => 2026,
                'planned_month' => 9,
                'status' => 'pending',
            ],
            [
                'title' => 'Leadership Training Workshop',
                'description' => '09:00 AM - 01:00 PM @ Conference Hall',
                'planned_year' => 2026,
                'planned_month' => 10,
                'status' => 'pending',
            ],
            [
                'title' => 'Youth Praise Night',
                'description' => '06:00 PM - 09:00 PM @ Main Sanctuary',
                'planned_year' => 2026,
                'planned_month' => 10,
                'status' => 'pending',
            ],
            [
                'title' => 'Youth Sports Bonanza',
                'description' => '08:00 AM - 04:00 PM @ Church Grounds',
                'planned_year' => 2026,
                'planned_month' => 11,
                'status' => 'pending',
            ],
            [
                'title' => 'End of Year Youth Thanksgiving',
                'description' => '10:00 AM - 02:00 PM @ Main Hall',
                'planned_year' => 2026,
                'planned_month' => 12,
                'status' => 'pending',
            ],
        ];

        foreach ($youthSchedules as $s) {
            AnnualSchedule::firstOrCreate(
                ['department_id' => $youthDept->id, 'title' => $s['title'], 'planned_year' => $s['planned_year']],
                $s
            );
        }

        // Add completed schedules for other months to calculate progress
        foreach ($departments as $dept) {
            for ($m = 1; $m <= 8; $m++) {
                AnnualSchedule::firstOrCreate(
                    ['department_id' => $dept->id, 'planned_year' => 2026, 'planned_month' => $m, 'title' => "Shughuli ya Mwezi wa {$m}"],
                    [
                        'description' => "Utekelezaji wa shughuli za idara mwezi wa {$m}",
                        'status' => $m <= 6 ? 'completed' : 'pending',
                    ]
                );
            }
        }

        $admin = User::where('email', 'admin@idara.test')->first();

        // 4. Sample Transactions
        DepartmentTransaction::create([
            'department_id' => $youthDept->id,
            'type' => 'Michango ya Vijana',
            'amount' => 450000,
            'description' => 'Michango ya semina ya vijana na vifaa',
            'recorded_by' => $admin?->id ?? 1,
            'occurred_at' => now()->subDays(2),
        ]);

        $worshipDept = $departments['idara-ya-kusifu-na-kuabudu'];
        DepartmentTransaction::create([
            'department_id' => $worshipDept->id,
            'type' => 'Sadaka & Michango',
            'amount' => 250000,
            'description' => 'Ununuzi wa nyaya na vinasa sauti vya kwaya',
            'recorded_by' => $admin?->id ?? 1,
            'occurred_at' => now()->subHours(2),
        ]);

        $womenDept = $departments['idara-ya-wamama'];
        DepartmentTransaction::create([
            'department_id' => $womenDept->id,
            'type' => 'Michango ya Mahema',
            'amount' => 545000,
            'description' => 'Mchango wa ununuzi wa mahema ya huduma',
            'recorded_by' => $admin?->id ?? 1,
            'occurred_at' => now()->subDays(5),
        ]);

        // 5. Sample SMS Logs
        SmsLog::create([
            'department_id' => $youthDept->id,
            'sent_by' => $admin?->id ?? 1,
            'message' => 'Kikumbusho: Mkutano wa vijana utafanyika Jumapili saa nne asubuhi.',
            'recipients_count' => 24,
            'sent_count' => 24,
            'failed_count' => 0,
            'status' => 'sent',
            'sent_at' => now()->subHours(1),
        ]);

        SmsLog::create([
            'department_id' => $womenDept->id,
            'sent_by' => $admin?->id ?? 1,
            'message' => 'Wapendwa kina mama, semina yetu ya ujasiriamali imeanza.',
            'recipients_count' => 104,
            'sent_count' => 104,
            'failed_count' => 0,
            'status' => 'sent',
            'sent_at' => now()->subDays(3),
        ]);

        // 6. Recent Activity Logs
        ActivityLog::create([
            'department_id' => $youthDept->id,
            'recorded_by' => $admin?->id ?? 1,
            'title' => 'Neema Paul was added to department',
            'description' => 'Mwanachama mpya amesajiliwa kwenye Idara ya Vijana',
            'occurred_at' => now(),
        ]);

        ActivityLog::create([
            'department_id' => $youthDept->id,
            'recorded_by' => $admin?->id ?? 1,
            'title' => 'Youth meeting reminder sent',
            'description' => 'SMS 24 zimetumwa kwa wanachama wa vijana',
            'occurred_at' => now()->subHours(1),
        ]);

        ActivityLog::create([
            'department_id' => $worshipDept->id,
            'recorded_by' => $admin?->id ?? 1,
            'title' => 'New transaction recorded in Worship Dept.',
            'description' => 'TZS 250,000 imeingizwa kwenye mfumo',
            'occurred_at' => now()->subHours(2),
        ]);

        // 7. Sample Reports
        Report::firstOrCreate(
            ['department_id' => $youthDept->id, 'period' => 'yearly:2026'],
            [
                'file_path' => 'reports/idara-ya-vijana-2026.pdf',
                'generated_at' => now()->subDays(10),
            ]
        );
        Report::firstOrCreate(
            ['department_id' => $youthDept->id, 'period' => 'monthly:2026-07'],
            [
                'file_path' => 'reports/idara-ya-vijana-2026-07.pdf',
                'generated_at' => now()->subDays(25),
            ]
        );
        Report::firstOrCreate(
            ['department_id' => $youthDept->id, 'period' => 'monthly:2026-08'],
            [
                'file_path' => 'reports/idara-ya-vijana-2026-08.pdf',
                'generated_at' => now()->subDays(3),
            ]
        );
    }
}
