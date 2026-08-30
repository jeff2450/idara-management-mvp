<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnnualScheduleRequest;
use App\Http\Requests\UpdateAnnualScheduleRequest;
use App\Models\AnnualSchedule;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Ratiba ya Mwaka ya idara - prd.md §5.3. Route zake ni nested chini ya
 * {department} na zinalindwa mara mbili, kama DepartmentMemberController:
 * middleware 'department.access' (routes/web.php) + Policy
 * (AnnualSchedulePolicy) ndani ya Form Requests.
 */
class AnnualScheduleController extends Controller
{
    public function index(Department $department): View
    {
        $this->authorize('viewAny', AnnualSchedule::class);

        $schedules = $department->annualSchedules()
            ->orderByDesc('planned_year')
            ->orderBy('planned_month')
            ->paginate(20);

        return view('schedules.index', compact('department', 'schedules'));
    }

    public function store(StoreAnnualScheduleRequest $request, Department $department): RedirectResponse
    {
        $department->annualSchedules()->create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('schedules.index', $department)
            ->with('status', 'Kipengele cha ratiba kimeongezwa.');
    }

    public function update(UpdateAnnualScheduleRequest $request, Department $department, AnnualSchedule $schedule): RedirectResponse
    {
        $schedule->update($request->validated());

        return redirect()
            ->route('schedules.index', $department)
            ->with('status', 'Ratiba imesasishwa.');
    }

    public function destroy(Department $department, AnnualSchedule $schedule): RedirectResponse
    {
        $this->authorize('delete', $schedule);

        $schedule->delete();

        return redirect()
            ->route('schedules.index', $department)
            ->with('status', 'Kipengele cha ratiba kimefutwa.');
    }
}
