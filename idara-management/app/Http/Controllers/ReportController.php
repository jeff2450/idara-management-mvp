<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Report;
use App\Services\Reports\DepartmentReportGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Angalia architecture.md §2.7. Uzalishaji wa kiotomatiki unafanywa na cron
 * (Console\Commands\GenerateDepartmentReports), lakini kiongozi anaweza pia
 * kubonyeza "Zalisha Sasa" hapa - njia zote mbili zinatumia
 * DepartmentReportGenerator moja (angalia darasa hilo).
 */
class ReportController extends Controller
{
    public function index(Department $department): View
    {
        $this->authorize('viewAny', [Report::class, $department]);

        $reports = $department->reports()->latest('generated_at')->paginate(15);

        return view('reports.index', compact('department', 'reports'));
    }

    public function generate(Request $request, Department $department, DepartmentReportGenerator $generator): RedirectResponse
    {
        $this->authorize('generate', [Report::class, $department]);

        $data = $request->validate([
            'period_type' => ['required', 'in:yearly,monthly'],
            'year' => ['required', 'digits:4'],
            'month' => ['nullable', 'integer', 'min:1', 'max:12', 'required_if:period_type,monthly'],
        ]);

        $period = $data['period_type'] === 'yearly'
            ? 'yearly:'.$data['year']
            : 'monthly:'.$data['year'].'-'.str_pad((string) $data['month'], 2, '0', STR_PAD_LEFT);

        $generator->generate($department, $period);

        return redirect()
            ->route('departments.reports.index', $department)
            ->with('status', 'Ripoti imezalishwa.');
    }

    public function download(Department $department, Report $report, DepartmentReportGenerator $generator): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->authorize('view', $report);

        abort_unless($report->department_id === $department->id, 404);

        if (! Storage::disk('local')->exists($report->file_path)) {
            $filename = 'reports/'.$department->id.'/'.Str::slug($report->period).'-'.Str::uuid().'.pdf';
            Storage::disk('local')->put($filename, $generator->renderPdf($department, $report->period));
            $report->update(['file_path' => $filename]);
        }

        $safePeriodName = str_replace([':', '/'], '-', $report->period);

        return Storage::disk('local')->download($report->file_path, 'ripoti-'.$safePeriodName.'.pdf');
    }
}
