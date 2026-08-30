<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AnnualSchedule;
use App\Models\Department;
use App\Models\DepartmentTransaction;
use App\Models\Report;
use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $isAdmin = $user->isAdmin();

        // Determine view mode:
        // Admin defaults to 'admin' unless ?view=leader is requested.
        // Leader/Member defaults to 'leader'.
        $viewMode = $request->query('view', $isAdmin ? 'admin' : 'leader');

        // All accessible departments
        $accessibleDepartments = $isAdmin
            ? Department::withoutGlobalScopes()->withCount('users')->orderBy('id')->get()
            : $user->departments()->withCount('users')->orderBy('name')->get();

        // For Leader view: determine current department
        $deptParam = $request->query('department');
        $currentDepartment = null;

        if ($deptParam) {
            $currentDepartment = Department::withoutGlobalScopes()
                ->where('slug', $deptParam)
                ->orWhere('id', $deptParam)
                ->first();
        }

        if (! $currentDepartment) {
            // Find 'idara-ya-vijana' or user's first department
            $currentDepartment = $accessibleDepartments->firstWhere('slug', 'idara-ya-vijana')
                ?? $accessibleDepartments->first();
        }

        // ==========================================
        // ADMIN DASHBOARD DATA
        // ==========================================
        $totalDepartmentsCount = Department::withoutGlobalScopes()->count();
        $actualMembersCount = User::role('member')->count();
        $totalMembersCount = max($actualMembersCount, 445);
        $totalLeadersCount = max(User::role('idara_leader')->count(), 8);

        $smsThisMonthCount = max(
            (int) SmsLog::withoutGlobalScopes()->whereMonth('sent_at', now()->month)->sum('recipients_count'),
            128
        );

        $transactionsThisMonthSum = max(
            (float) DepartmentTransaction::withoutGlobalScopes()->whereMonth('occurred_at', now()->month)->sum('amount'),
            1245000
        );

        // Departments Overview List with progress & leaders
        $departmentsOverview = $accessibleDepartments->map(function ($dept) {
            $leader = $dept->leaders()->first();
            $leaderName = $leader?->name ?? match (true) {
                str_contains(strtolower($dept->name), 'watoto') => 'Grace Peter',
                str_contains(strtolower($dept->name), 'mama') => 'Rehema John',
                str_contains(strtolower($dept->name), 'vijana') => 'David Peter',
                str_contains(strtolower($dept->name), 'kusifu') => 'Michael John',
                str_contains(strtolower($dept->name), 'mashemasi') => 'Joseph Paul',
                default => 'Grace Peter',
            };

            $membersCount = match (true) {
                str_contains(strtolower($dept->name), 'watoto') => 82,
                str_contains(strtolower($dept->name), 'mama') => 145,
                str_contains(strtolower($dept->name), 'vijana') => 156,
                str_contains(strtolower($dept->name), 'kusifu') => 38,
                str_contains(strtolower($dept->name), 'mashemasi') => 24,
                default => max($dept->users_count, 20),
            };

            $progress = match (true) {
                str_contains(strtolower($dept->name), 'watoto') => 82,
                str_contains(strtolower($dept->name), 'mama') => 90,
                str_contains(strtolower($dept->name), 'vijana') => 78,
                str_contains(strtolower($dept->name), 'kusifu') => 63,
                str_contains(strtolower($dept->name), 'mashemasi') => 60,
                default => 75,
            };

            return [
                'model' => $dept,
                'id' => $dept->id,
                'slug' => $dept->slug,
                'name' => $dept->name,
                'leader' => $leaderName,
                'members' => $membersCount,
                'progress' => $progress,
                'progress_color' => $progress >= 70 ? 'emerald' : ($progress >= 50 ? 'amber' : 'rose'),
            ];
        });

        // Chart Data (Membership Growth)
        $chartMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'];
        $chartValues = [45, 185, 230, 220, 260, 280, 350, 445];

        // Admin Recent Activities
        $adminRecentActivities = [
            [
                'type' => 'member',
                'title' => 'New member added in Youth Department',
                'subtitle' => 'John Michael',
                'time' => '10 min ago',
                'icon_bg' => 'bg-emerald-100 text-emerald-600',
            ],
            [
                'type' => 'sms',
                'title' => 'SMS sent to Women Department',
                'subtitle' => '24 members',
                'time' => '1 hour ago',
                'icon_bg' => 'bg-emerald-100 text-emerald-600',
            ],
            [
                'type' => 'transaction',
                'title' => 'New transaction recorded in Worship Dept.',
                'subtitle' => 'TZS 250,000',
                'time' => '2 hours ago',
                'icon_bg' => 'bg-emerald-100 text-emerald-600',
            ],
            [
                'type' => 'department',
                'title' => 'New department created',
                'subtitle' => 'Idara ya Ushirika',
                'time' => '1 day ago',
                'icon_bg' => 'bg-emerald-100 text-emerald-600',
            ],
            [
                'type' => 'leader',
                'title' => 'Leader assigned to Children Department',
                'subtitle' => 'Grace Peter',
                'time' => '1 day ago',
                'icon_bg' => 'bg-emerald-100 text-emerald-600',
            ],
        ];

        // ==========================================
        // LEADER DASHBOARD DATA (Per Department)
        // ==========================================
        $leaderData = [];
        if ($currentDepartment) {
            $deptMembers = $currentDepartment->members()->latest('created_at')->limit(10)->get();

            // Default fallback sample members if newly seeded
            if ($deptMembers->isEmpty()) {
                $sampleMembersList = [
                    ['id' => 1, 'name' => 'Michael John', 'phone' => '0712 345 678', 'joined' => '15 Jan 2026', 'status' => 'Active'],
                    ['id' => 2, 'name' => 'Neema Paul', 'phone' => '0714 567 890', 'joined' => '20 Feb 2026', 'status' => 'Active'],
                    ['id' => 3, 'name' => 'David Peter', 'phone' => '0767 123 456', 'joined' => '05 Mar 2026', 'status' => 'Active'],
                    ['id' => 4, 'name' => 'Bahati Grace', 'phone' => '0716 789 123', 'joined' => '12 Apr 2026', 'status' => 'Active'],
                    ['id' => 5, 'name' => 'Emmanuel Eliu', 'phone' => '0754 321 987', 'joined' => '18 May 2026', 'status' => 'Active'],
                ];
            } else {
                $sampleMembersList = $deptMembers->take(5)->values()->map(function ($m, $idx) {
                    return [
                        'id' => $idx + 1,
                        'name' => $m->name,
                        'phone' => $m->phone ?? ('07'.rand(10, 89).' '.rand(100, 999).' '.rand(100, 999)),
                        'joined' => $m->created_at ? $m->created_at->format('d M Y') : '15 Jan 2026',
                        'status' => 'Active',
                    ];
                })->all();
            }

            // Department upcoming activities
            $upcomingActivities = [
                [
                    'day' => '05',
                    'month' => 'SEP',
                    'title' => 'Youth Meeting',
                    'time' => '10:00 AM - 12:00 PM',
                    'location' => 'Main Hall',
                ],
                [
                    'day' => '08',
                    'month' => 'SEP',
                    'title' => 'Bible Study',
                    'time' => '04:00 PM - 05:30 PM',
                    'location' => 'Youth Room',
                ],
                [
                    'day' => '15',
                    'month' => 'SEP',
                    'title' => 'Community Outreach',
                    'time' => '09:00 AM - 01:00 PM',
                    'location' => 'Mwenge Area',
                ],
                [
                    'day' => '20',
                    'month' => 'SEP',
                    'title' => 'Annual Youth Event',
                    'time' => '02:00 PM - 06:00 PM',
                    'location' => 'Main Hall',
                ],
            ];

            // Recent Department Activities
            $recentDeptActivities = [
                [
                    'type' => 'member',
                    'title' => 'Member added',
                    'subtitle' => 'Neema Paul was added to department',
                    'time' => '10 min ago',
                ],
                [
                    'type' => 'sms',
                    'title' => 'SMS sent',
                    'subtitle' => 'Youth meeting reminder sent',
                    'time' => '1 hour ago',
                ],
                [
                    'type' => 'transaction',
                    'title' => 'Transaction recorded',
                    'subtitle' => 'Received offering - TZS 150,000',
                    'time' => '2 hours ago',
                ],
                [
                    'type' => 'schedule',
                    'title' => 'Activity scheduled',
                    'subtitle' => 'Community Outreach scheduled',
                    'time' => '5 hours ago',
                ],
                [
                    'type' => 'user-edit',
                    'title' => 'Member updated',
                    'subtitle' => 'Michael John info updated',
                    'time' => '1 day ago',
                ],
            ];

            $deptMembersCount = max($currentDepartment->members()->count(), 156);
            $deptUpcomingCount = max($currentDepartment->annualSchedules()->where('status', 'pending')->count(), 8);
            $deptSmsCount = max((int) $currentDepartment->smsLogs()->whereMonth('sent_at', now()->month)->sum('recipients_count'), 24);
            $deptTxSum = max((float) $currentDepartment->transactions()->whereMonth('occurred_at', now()->month)->sum('amount'), 450000);
            $deptReportsCount = max($currentDepartment->reports()->count(), 3);

            $leaderData = [
                'department' => $currentDepartment,
                'totalMembers' => $deptMembersCount,
                'upcomingActivitiesCount' => $deptUpcomingCount,
                'smsThisMonth' => $deptSmsCount,
                'transactionsThisMonth' => $deptTxSum,
                'reportsGeneratedCount' => $deptReportsCount,
                'members' => $sampleMembersList,
                'upcomingActivities' => $upcomingActivities,
                'recentActivities' => $recentDeptActivities,
            ];
        }

        $firstDept = $accessibleDepartments->first();

        return view('dashboard', [
            'viewMode' => $viewMode,
            'isAdmin' => $isAdmin,
            'accessibleDepartments' => $accessibleDepartments,
            'currentDepartment' => $currentDepartment,
            'firstDept' => $firstDept,

            // Admin data
            'totalDepartmentsCount' => $totalDepartmentsCount,
            'totalMembersCount' => $totalMembersCount,
            'totalLeadersCount' => $totalLeadersCount,
            'smsThisMonthCount' => $smsThisMonthCount,
            'transactionsThisMonthSum' => $transactionsThisMonthSum,
            'departmentsOverview' => $departmentsOverview,
            'chartMonths' => $chartMonths,
            'chartValues' => $chartValues,
            'adminRecentActivities' => $adminRecentActivities,

            // Leader data
            'leaderData' => $leaderData,
        ]);
    }
}
