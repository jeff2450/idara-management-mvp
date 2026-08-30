<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Awamu ya 3 (prd.md §5.3 / architecture.md §2.7) - zalisha ripoti za mwaka
// za idara zote kila mwisho wa mwaka. Kwenye shared/cPanel hosting isiyo na
// queue worker ya kudumu, hakikisha cron ya server inapiga
// `php artisan schedule:run` kila dakika - angalia stacks.md §5.
Schedule::command('report:generate --period=yearly')->yearlyOn(12, 31, '23:30');
