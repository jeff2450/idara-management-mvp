<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityLogRequest;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;

/**
 * Kurekodi shughuli halisi dhidi ya ratiba (au huru) - prd.md §4.6. Ukirekodi
 * dhidi ya kipengele cha ratiba, kipengele hicho kinawekwa 'completed'
 * kiotomatiki - hii ndiyo inayolisha asilimia kwenye dashibodi ya maendeleo.
 */
class ActivityLogController extends Controller
{
    public function store(StoreActivityLogRequest $request, Department $department): RedirectResponse
    {
        $data = $request->validated();

        $department->activityLogs()->create([
            ...$data,
            'recorded_by' => $request->user()->id,
        ]);

        if (! empty($data['annual_schedule_id'])) {
            $department->annualSchedules()
                ->whereKey($data['annual_schedule_id'])
                ->first()
                ?->markCompleted();
        }

        return redirect()
            ->route('departments.progress', $department)
            ->with('status', 'Shughuli imerekodiwa.');
    }
}
