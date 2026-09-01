<?php

namespace App\Services\Reports;

use App\Models\Department;
use App\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Angalia architecture.md §2.7 na §4.B - inaunganisha activity_logs +
 * department_transactions kuwa ripoti moja ya PDF kwa idara na kipindi
 * husika. Inatumiwa na Console\Commands\GenerateDepartmentReports (cron) NA
 * ReportController (kitufe cha "Zalisha Sasa" cha kiongozi) - logic moja
 * tu, njia mbili za kuiita.
 */
class DepartmentReportGenerator
{
    /**
     * @param  string  $period  'yearly:2026' au 'monthly:2026-08'
     */
    public function renderPdf(Department $department, string $period): string
    {
        [$start, $end, $label] = $this->resolvePeriod($period);

        $activityLogs = $department->activityLogs()
            ->with('schedule')
            ->whereBetween('occurred_at', [$start, $end])
            ->orderBy('occurred_at')
            ->get();

        $transactions = $department->transactions()
            ->whereBetween('occurred_at', [$start, $end])
            ->orderBy('occurred_at')
            ->get();

        $pdf = Pdf::loadView('pdf.report', [
            'department' => $department,
            'label' => $label,
            'activityLogs' => $activityLogs,
            'transactions' => $transactions,
            'totalAmount' => $transactions->sum('amount'),
        ]);

        return $pdf->output();
    }

    /**
     * @param  string  $period  'yearly:2026' au 'monthly:2026-08'
     */
    public function generate(Department $department, string $period): Report
    {
        $filename = 'reports/'.$department->id.'/'.Str::slug($period).'-'.Str::uuid().'.pdf';
        Storage::disk('local')->put($filename, $this->renderPdf($department, $period));

        return Report::create([
            'department_id' => $department->id,
            'period' => $period,
            'file_path' => $filename,
            'generated_at' => now(),
        ]);
    }

    /** @return array{0: Carbon, 1: Carbon, 2: string} */
    private function resolvePeriod(string $period): array
    {
        if (str_starts_with($period, 'monthly:')) {
            $date = Carbon::createFromFormat('Y-m', substr($period, 8))->startOfMonth();

            return [$date->copy()->startOfMonth(), $date->copy()->endOfMonth(), $date->translatedFormat('F Y')];
        }

        $year = (int) str_replace('yearly:', '', $period);
        $date = Carbon::create($year, 1, 1);

        return [$date->copy()->startOfYear(), $date->copy()->endOfYear(), (string) $year];
    }
}
