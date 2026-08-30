<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\AnnualSchedule;
use App\Models\Department;
use App\Models\DepartmentTransaction;
use App\Models\Letter;
use App\Models\LetterTemplate;
use App\Models\Report;
use App\Models\SmsLog;
use App\Policies\ActivityLogPolicy;
use App\Policies\AnnualSchedulePolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\LetterPolicy;
use App\Policies\LetterTemplatePolicy;
use App\Policies\ReportPolicy;
use App\Policies\SmsLogPolicy;
use App\Policies\TransactionPolicy;
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
        // Usajili wa sera (Policies)
        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(DepartmentTransaction::class, TransactionPolicy::class);
        Gate::policy(LetterTemplate::class, LetterTemplatePolicy::class);
        Gate::policy(Letter::class, LetterPolicy::class);
        Gate::policy(SmsLog::class, SmsLogPolicy::class);

        // Awamu ya 3 (prd.md §5.3) - Ratiba, Shughuli, na Ripoti.
        Gate::policy(AnnualSchedule::class, AnnualSchedulePolicy::class);
        Gate::policy(ActivityLog::class, ActivityLogPolicy::class);
        Gate::policy(Report::class, ReportPolicy::class);
    }
}
