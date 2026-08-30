<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\View\View;

/**
 * Dashibodi ya Maendeleo ya Idara - prd.md §5.3 "Dashibodi ya maendeleo ya
 * idara (progress overview)". Inaonyesha: asilimia ya ratiba ya mwaka
 * iliyotekelezwa, shughuli za hivi karibuni, na ripoti ya mwisho iliyozalishwa.
 */
class DepartmentProgressController extends Controller
{
    public function show(Department $department): View
    {
        $this->authorize('view', $department);

        $year = now()->year;

        $schedules = $department->annualSchedules()
            ->where('planned_year', $year)
            ->orderBy('planned_month')
            ->get();

        $completed = $schedules->where('status', 'completed')->count();
        $completionRate = $schedules->isNotEmpty()
            ? (int) round($completed / $schedules->count() * 100)
            : null;

        $recentActivity = $department->activityLogs()
            ->latest('occurred_at')
            ->limit(10)
            ->get();

        $latestReport = $department->reports()
            ->latest('generated_at')
            ->first();

        return view('departments.progress', compact(
            'department', 'year', 'schedules', 'completionRate', 'recentActivity', 'latestReport'
        ));
    }
}
