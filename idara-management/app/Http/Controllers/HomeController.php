<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AnnualSchedule;
use App\Models\Department;
use App\Models\DepartmentTransaction;
use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

/**
 * HomeController - Public church progress homepage.
 *
 * All data is read directly from the database using withoutGlobalScopes()
 * so that the public homepage bypasses the department-access restrictions
 * that apply inside the authenticated management system.
 *
 * No fallback / hardcoded values are used. If data has not been entered yet
 * in the system, the view renders a clear "no data" empty state.
 */
class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $year = (int) $request->query('year', now()->year);
        if ($year < 2020 || $year > 2035) {
            $year = now()->year;
        }

        // ─────────────────────────────────────────────────────────────
        // 1. DEPARTMENTS (base query, no scope filters)
        // ─────────────────────────────────────────────────────────────
        $departments = Department::withoutGlobalScopes()->orderBy('id')->get();

        // ─────────────────────────────────────────────────────────────
        // 2. ANNUAL SCHEDULES for the selected year
        // ─────────────────────────────────────────────────────────────
        $allSchedules = AnnualSchedule::withoutGlobalScopes()
            ->where('planned_year', $year)
            ->get();

        // ─────────────────────────────────────────────────────────────
        // 3. GLOBAL KPI COUNTS — straight from DB, no overrides
        // ─────────────────────────────────────────────────────────────
        $totalDeptsCount           = $departments->count();
        $totalMembersCount         = User::withoutGlobalScopes()->count();
        $totalLeadersCount         = User::role('idara_leader')->count();
        $totalRecordedActivities   = ActivityLog::withoutGlobalScopes()->count();
        $totalSmsThisYear          = (int) SmsLog::withoutGlobalScopes()
                                            ->whereYear('sent_at', $year)
                                            ->sum('recipients_count');
        $totalTransactionsThisYear = (float) DepartmentTransaction::withoutGlobalScopes()
                                            ->whereYear('occurred_at', $year)
                                            ->sum('amount');

        // Overall schedule progress
        $overallTotalGoals      = $allSchedules->count();
        $overallCompletedGoals  = $allSchedules->where('status', 'completed')->count();
        $overallProgressPercent = $overallTotalGoals > 0
            ? (int) round(($overallCompletedGoals / $overallTotalGoals) * 100)
            : 0;

        // ─────────────────────────────────────────────────────────────
        // 4. PER-DEPARTMENT PROGRESS DATA
        // ─────────────────────────────────────────────────────────────
        $departmentsData = $departments->map(function (Department $dept) use ($year) {
            // Schedules for this dept & year
            $schedules = AnnualSchedule::withoutGlobalScopes()
                ->where('department_id', $dept->id)
                ->where('planned_year', $year)
                ->orderBy('planned_month')
                ->get();

            $totalSchedules     = $schedules->count();
            $completedSchedules = $schedules->where('status', 'completed')->count();
            $progressPercent    = $totalSchedules > 0
                ? (int) round(($completedSchedules / $totalSchedules) * 100)
                : 0;

            // Leader & member counts from DB
            $leader       = $dept->leaders()->withoutGlobalScopes()->first();
            $membersCount = $dept->users()->withoutGlobalScopes()->count();

            // 5 most recent activity logs for this dept
            $recentActivities = ActivityLog::withoutGlobalScopes()
                ->where('department_id', $dept->id)
                ->latest('occurred_at')
                ->limit(5)
                ->get();

            // Format schedules for JSON / modal
            $schedulesList = $schedules->map(fn ($s) => [
                'id'            => $s->id,
                'title'         => $s->title,
                'description'   => $s->description ?? '',
                'planned_month' => $s->planned_month,
                'month_name'    => $this->monthName($s->planned_month),
                'status'        => $s->status,
            ])->values()->all();

            // Format activities for JSON / modal
            $activitiesList = $recentActivities->map(fn ($a) => [
                'id'          => $a->id,
                'title'       => $a->title,
                'description' => $a->description ?? '',
                'occurred_at' => $a->occurred_at instanceof Carbon
                    ? $a->occurred_at->format('d M Y')
                    : (string) $a->occurred_at,
            ])->values()->all();

            return [
                'id'                  => $dept->id,
                'name'                => $dept->name,
                'slug'                => $dept->slug,
                'description'         => $dept->description ?? '',
                'is_sensitive'        => (bool) $dept->is_sensitive,
                'leader_name'         => $leader?->name ?? '—',
                'members_count'       => $membersCount,
                'total_schedules'     => $totalSchedules,
                'completed_schedules' => $completedSchedules,
                'progress_percent'    => $progressPercent,
                'schedules'           => $schedulesList,
                'activities'          => $activitiesList,
            ];
        });

        // ─────────────────────────────────────────────────────────────
        // 5. QUARTERLY BREAKDOWN — derived from real annual_schedules
        // ─────────────────────────────────────────────────────────────
        $currentMonth = now()->month;
        $currentYear  = now()->year;

        $quarterDefs = [
            'q1' => ['label' => 'Q1 — Januari hadi Machi',     'period' => 'Januari – Machi',     'months' => [1, 2, 3]],
            'q2' => ['label' => 'Q2 — Aprili hadi Juni',       'period' => 'Aprili – Juni',       'months' => [4, 5, 6]],
            'q3' => ['label' => 'Q3 — Julai hadi Septemba',    'period' => 'Julai – Septemba',    'months' => [7, 8, 9]],
            'q4' => ['label' => 'Q4 — Oktoba hadi Desemba',    'period' => 'Oktoba – Desemba',    'months' => [10, 11, 12]],
        ];

        $quarterlyProgress = [];
        foreach ($quarterDefs as $key => $def) {
            $qSchedules = $allSchedules->whereIn('planned_month', $def['months']);
            $qTotal     = $qSchedules->count();
            $qCompleted = $qSchedules->where('status', 'completed')->count();
            $qPercent   = $qTotal > 0 ? (int) round(($qCompleted / $qTotal) * 100) : 0;

            $lastMonth  = max($def['months']);
            $firstMonth = min($def['months']);

            // Status derived from current date relative to the quarter
            if ($year < $currentYear || ($year === $currentYear && $lastMonth < $currentMonth)) {
                $status      = 'Imekamilika';
                $statusColor = 'emerald';
            } elseif ($year === $currentYear && $firstMonth <= $currentMonth) {
                $status      = 'Inaendelea';
                $statusColor = 'indigo';
            } else {
                $status      = 'Inakuja';
                $statusColor = 'amber';
            }

            // Use real schedule titles as milestone items (completed first, then pending)
            $milestones = $qSchedules
                ->sortByDesc(fn ($s) => $s->status === 'completed' ? 1 : 0)
                ->take(3)
                ->pluck('title')
                ->values()
                ->all();

            $quarterlyProgress[$key] = [
                'label'        => $def['label'],
                'period'       => $def['period'],
                'percent'      => $qPercent,
                'status'       => $status,
                'status_color' => $statusColor,
                'total'        => $qTotal,
                'completed'    => $qCompleted,
                'milestones'   => $milestones,
            ];
        }

        // ─────────────────────────────────────────────────────────────
        // 6. UPCOMING EVENTS — pending schedules from this month forward
        // ─────────────────────────────────────────────────────────────
        $upcomingEvents = AnnualSchedule::withoutGlobalScopes()
            ->with(['department' => fn ($q) => $q->withoutGlobalScopes()])
            ->where('planned_year', $year)
            ->where('status', 'pending')
            ->where('planned_month', '>=', ($year === now()->year ? now()->month : 1))
            ->orderBy('planned_month')
            ->limit(8)
            ->get()
            ->map(fn ($s) => [
                'id'              => $s->id,
                'title'           => $s->title,
                'description'     => $s->description ?? '',
                'month_num'       => $s->planned_month,
                'month_name'      => $this->monthName($s->planned_month),
                'month_short'     => $this->monthShort($s->planned_month),
                'department_name' => $s->department?->name ?? '—',
                'status'          => $s->status,
            ])
            ->values();

        // ─────────────────────────────────────────────────────────────
        // 7. RECENT ACTIVITIES FEED — latest activity_logs across all depts
        // ─────────────────────────────────────────────────────────────
        $recentActivities = ActivityLog::withoutGlobalScopes()
            ->with([
                'department' => fn ($q) => $q->withoutGlobalScopes(),
                'recorder',
            ])
            ->latest('occurred_at')
            ->limit(6)
            ->get()
            ->map(fn ($a) => [
                'id'              => $a->id,
                'title'           => $a->title,
                'description'     => $a->description ?? '',
                'department_name' => $a->department?->name ?? '—',
                'recorded_by'     => $a->recorder?->name ?? '—',
                'occurred_at'     => $a->occurred_at instanceof Carbon
                    ? $a->occurred_at->format('d M Y')
                    : (string) $a->occurred_at,
            ])
            ->values();

        // ─────────────────────────────────────────────────────────────
        // 8. RECENTLY COMPLETED SCHEDULES (for "milestones achieved" block)
        // ─────────────────────────────────────────────────────────────
        $completedMilestones = AnnualSchedule::withoutGlobalScopes()
            ->with(['department' => fn ($q) => $q->withoutGlobalScopes()])
            ->where('planned_year', $year)
            ->where('status', 'completed')
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get()
            ->map(fn ($s) => [
                'id'              => $s->id,
                'title'           => $s->title,
                'department_name' => $s->department?->name ?? '—',
                'month_name'      => $this->monthName($s->planned_month),
            ])
            ->values();

        // ─────────────────────────────────────────────────────────────
        // 9. WEEKLY SCHEDULE — static structured church timetable
        //    (This is a fixed operating schedule, not stored in DB)
        // ─────────────────────────────────────────────────────────────
        $weeklySchedule = [
            [
                'day' => 'Jumapili',
                'services' => [
                    ['name' => 'Ibada ya Kwanza', 'time' => 'Saa 01:00 – 03:30', 'venue' => 'Ukumbi Mkuu'],
                    ['name' => 'Ibada ya Pili & Sunday School', 'time' => 'Saa 04:00 – 07:00', 'venue' => 'Ukumbi Mkuu & Madarasa'],
                    ['name' => 'Ushirika wa Vijana', 'time' => 'Saa 08:30 – 10:30', 'venue' => 'Ukumbi wa Vijana'],
                ],
            ],
            [
                'day' => 'Jumatano',
                'services' => [
                    ['name' => 'Maombi & Kujifunza Neno', 'time' => 'Saa 11:30 – 01:30', 'venue' => 'Ukumbi Mkuu'],
                ],
            ],
            [
                'day' => 'Ijumaa',
                'services' => [
                    ['name' => 'Mkesha wa Maombi / Vikundi vya Nyumba', 'time' => 'Saa 03:00 – 09:00', 'venue' => 'Matawi / Ukumbi Mkuu'],
                ],
            ],
            [
                'day' => 'Jumamosi',
                'services' => [
                    ['name' => 'Mazoezi ya Kwaya & Usafi wa Kanisa', 'time' => 'Saa 08:00 – 12:00', 'venue' => 'Ukumbi Mkuu'],
                ],
            ],
        ];

        return view('home', compact(
            'year',
            // KPIs
            'totalDeptsCount',
            'totalMembersCount',
            'totalLeadersCount',
            'totalRecordedActivities',
            'totalSmsThisYear',
            'totalTransactionsThisYear',
            'overallTotalGoals',
            'overallCompletedGoals',
            'overallProgressPercent',
            // Departments
            'departmentsData',
            // Quarterly
            'quarterlyProgress',
            // Events & Activities
            'upcomingEvents',
            'recentActivities',
            'completedMilestones',
            // Static
            'weeklySchedule'
        ));
    }

    // ─────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────

    private function monthName(int $m): string
    {
        return [
            1 => 'Januari',  2 => 'Februari', 3 => 'Machi',
            4 => 'Aprili',   5 => 'Mei',       6 => 'Juni',
            7 => 'Julai',    8 => 'Agosti',    9 => 'Septemba',
            10 => 'Oktoba',  11 => 'Novemba',  12 => 'Desemba',
        ][$m] ?? "Mwezi {$m}";
    }

    private function monthShort(int $m): string
    {
        return [
            1 => 'JAN',  2 => 'FEB',  3 => 'MAC',
            4 => 'APR',  5 => 'MEI',  6 => 'JUN',
            7 => 'JUL',  8 => 'AGO',  9 => 'SEP',
            10 => 'OKT', 11 => 'NOV', 12 => 'DES',
        ][$m] ?? "M{$m}";
    }
}
