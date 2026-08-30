<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\AnnualSchedule;
use App\Models\Department;
use App\Models\Report;
use App\Policies\ActivityLogPolicy;
use App\Policies\AnnualSchedulePolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\ReportPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Laravel hugundua hii kiotomatiki kwa convention (Department ->
        // DepartmentPolicy), lakini tunaisajili wazi hapa kwa uwazi zaidi.
        Gate::policy(Department::class, DepartmentPolicy::class);

        // Awamu ya 3 (prd.md §5.3) - Ratiba, Shughuli, na Ripoti.
        Gate::policy(AnnualSchedule::class, AnnualSchedulePolicy::class);
        Gate::policy(ActivityLog::class, ActivityLogPolicy::class);
        Gate::policy(Report::class, ReportPolicy::class);
    }
}
