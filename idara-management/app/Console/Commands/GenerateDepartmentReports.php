<?php

namespace App\Console\Commands;

use App\Models\Department;
use App\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * `php artisan report:generate --period=yearly` - angalia architecture.md
 * §2.7 na §4.B (Mtiririko wa Data > "Kuzalisha ripoti ya mwaka"). Kwa MVP ya
 * Awamu ya 3 tunatumia `annual_schedules` + `activity_logs` pekee.
 *
 * NOTE: `department_transactions` (Awamu ya 2, angalia prd.md §5.2) haijajengwa
 * bado kwenye codebase hii - ukiijenga baadaye, ongeza query yake hapa kabla
 * ya kurender PDF, na uongeze jumla ya miamala kwenye view ya ripoti.
 */
class GenerateDepartmentReports extends Command
{
    protected $signature = 'report:generate {--period=yearly} {--year=}';

    protected $description = 'Zalisha ripoti ya PDF ya kila idara kwa kipindi husika.';

    public function handle(): int
    {
        $year = (int) ($this->option('year') ?: now()->year);
        $period = (string) $year; // panua hapa kwa robo mwaka (quarterly) baadaye

        // withoutGlobalScopes: command hii inaendeshwa na cron (hakuna auth),
        // hivyo DepartmentVisibilityScope isingeonyesha chochote kama tungeacha
        // scope ikitumika bila mtumiaji aliyeingia - ni sawa kuizima hapa maana
        // ripoti kiuhalisia zinahitaji idara ZOTE, siyo za mtumiaji fulani.
        Department::withoutGlobalScopes()->chunk(20, function ($departments) use ($year, $period) {
            foreach ($departments as $department) {
                $this->generateForDepartment($department, $year, $period);
            }
        });

        $this->info('Ripoti zote zimezalishwa.');

        return self::SUCCESS;
    }

    protected function generateForDepartment(Department $department, int $year, string $period): void
    {
        $schedules = $department->annualSchedules()
            ->where('planned_year', $year)
            ->get();

        $activityLogs = $department->activityLogs()
            ->whereYear('occurred_at', $year)
            ->orderBy('occurred_at')
            ->get();

        $totalScheduled = $schedules->count();
        $completed = $schedules->where('status', 'completed')->count();

        $pdf = Pdf::loadView('reports.pdf', [
            'department' => $department,
            'year' => $year,
            'schedules' => $schedules,
            'activityLogs' => $activityLogs,
            'completionRate' => $totalScheduled > 0 ? round($completed / $totalScheduled * 100) : null,
        ]);

        $path = "reports/{$department->slug}-{$period}.pdf";
        Storage::disk('local')->put($path, $pdf->output());

        Report::updateOrCreate(
            ['department_id' => $department->id, 'period' => $period],
            ['file_path' => $path, 'generated_at' => now()]
        );

        $this->line("  - {$department->name}: {$path}");
    }
}
