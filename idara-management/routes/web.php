<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AnnualScheduleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DepartmentMemberController;
use App\Http\Controllers\DepartmentProgressController;
use App\Http\Controllers\DepartmentTransactionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\LetterTemplateController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SmsController;
use App\Models\Department;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {

    Route::get('/dashibodi', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', fn () => redirect()->route('dashboard'));

    // Idara (Departments) - orodha inachujwa kiotomatiki ndani ya controller:
    // Admin & Leaders wanaona idara zote, mwanachama anaona idara zake tu.
    Route::resource('idara', DepartmentController::class)
        ->parameters(['idara' => 'department'])
        ->except(['create', 'store', 'edit', 'update', 'destroy'])
        ->names('departments');

    // Aliases to avoid 404 on common English / Swahili URLs
    Route::get('/departments', fn () => redirect()->route('departments.index'));
    Route::get('/departments/create', fn () => redirect()->route('departments.create'));
    Route::get('/idara/create', fn () => redirect()->route('departments.create'));
    Route::get('/departments/{department}', fn (Department $department) => redirect()->route('departments.show', $department));
    Route::get('/departments/{department}/edit', fn (Department $department) => redirect()->route('departments.edit', $department));
    Route::get('/idara/{department}/edit', fn (Department $department) => redirect()->route('departments.edit', $department));

    // Global navigation quick-redirects for root-level routes
    Route::get('/wanachama', function () {
        $dept = auth()->user()->departments()->first() ?? Department::withoutGlobalScopes()->first();
        return $dept ? redirect()->route('departments.show', $dept) : redirect()->route('dashboard');
    })->name('members.redirect');
    Route::get('/members', fn () => redirect()->route('members.redirect'));

    Route::get('/sms', function () {
        $dept = auth()->user()->departments()->first() ?? Department::withoutGlobalScopes()->first();
        return $dept ? redirect()->route('departments.sms.index', $dept) : redirect()->route('dashboard');
    })->name('sms.redirect');

    Route::get('/barua', function () {
        $dept = auth()->user()->departments()->first() ?? Department::withoutGlobalScopes()->first();
        return $dept ? redirect()->route('departments.letters.index', $dept) : redirect()->route('dashboard');
    })->name('letters.redirect');
    Route::get('/letters', fn () => redirect()->route('letters.redirect'));

    Route::get('/ripoti', function () {
        $dept = auth()->user()->departments()->first() ?? Department::withoutGlobalScopes()->first();
        return $dept ? redirect()->route('departments.reports.index', $dept) : redirect()->route('dashboard');
    })->name('reports.redirect');
    Route::get('/reports', fn () => redirect()->route('reports.redirect'));

    Route::get('/miamala', function () {
        $dept = auth()->user()->departments()->first() ?? Department::withoutGlobalScopes()->first();
        return $dept ? redirect()->route('departments.transactions.index', $dept) : redirect()->route('dashboard');
    })->name('transactions.redirect');
    Route::get('/transactions', fn () => redirect()->route('transactions.redirect'));

    Route::get('/ratiba', function () {
        $dept = auth()->user()->departments()->first() ?? Department::withoutGlobalScopes()->first();
        return $dept ? redirect()->route('schedules.index', $dept) : redirect()->route('dashboard');
    })->name('schedules.redirect');
    Route::get('/schedules', fn () => redirect()->route('schedules.redirect'));

    Route::get('/maendeleo', function () {
        $dept = auth()->user()->departments()->first() ?? Department::withoutGlobalScopes()->first();
        return $dept ? redirect()->route('departments.progress', $dept) : redirect()->route('dashboard');
    })->name('progress.redirect');
    Route::get('/progress', fn () => redirect()->route('progress.redirect'));

    // Uundaji/uhariri/ufutaji wa idara na templates - Admin na Leaders
    Route::middleware('role:admin|idara_leader')->group(function () {
        Route::get('/idara/unda', [DepartmentController::class, 'create'])->name('departments.create');
        Route::post('/idara', [DepartmentController::class, 'store'])->name('departments.store');
        Route::get('/idara/{department}/hariri', [DepartmentController::class, 'edit'])->name('departments.edit');
        Route::put('/idara/{department}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/idara/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

        Route::resource('barua-templates', LetterTemplateController::class)
            ->parameters(['barua-templates' => 'letterTemplate'])
            ->except(['show'])
            ->names('letter-templates');
    });

    // Moduli zote za idara zinahitaji 'department.access' kuhakikisha mtumiaji
    // anahusika na idara hii (au ni Admin / Leader) - angalia EnsureDepartmentAccess.
    Route::middleware('department.access')->group(function () {
        // Wanachama
        Route::get('/idara/{department}/wanachama/import', [DepartmentMemberController::class, 'importForm'])
            ->name('departments.members.import.form');
        Route::post('/idara/{department}/wanachama/import', [DepartmentMemberController::class, 'import'])
            ->name('departments.members.import');
        Route::get('/idara/{department}/wanachama/template', [DepartmentMemberController::class, 'downloadTemplate'])
            ->name('departments.members.template');
        Route::post('/idara/{department}/wanachama', [DepartmentMemberController::class, 'store'])
            ->name('departments.members.store');
        Route::delete('/idara/{department}/wanachama/{user}', [DepartmentMemberController::class, 'destroy'])
            ->name('departments.members.destroy');

        // SMS
        Route::get('/idara/{department}/sms', [SmsController::class, 'index'])->name('departments.sms.index');
        Route::get('/idara/{department}/sms/tuma', [SmsController::class, 'create'])->name('departments.sms.create');
        Route::post('/idara/{department}/sms', [SmsController::class, 'store'])->name('departments.sms.store');

        // Barua
        Route::get('/idara/{department}/barua', [LetterController::class, 'index'])->name('departments.letters.index');
        Route::get('/idara/{department}/barua/unda', [LetterController::class, 'create'])->name('departments.letters.create');
        Route::post('/idara/{department}/barua', [LetterController::class, 'store'])->name('departments.letters.store');
        Route::get('/idara/{department}/barua/{letter}/pakua', [LetterController::class, 'download'])->name('departments.letters.download');

        // Miamala ya Idara (Fedha)
        Route::get('/idara/{department}/miamala', [DepartmentTransactionController::class, 'index'])->name('departments.transactions.index');
        Route::get('/idara/{department}/miamala/unda', [DepartmentTransactionController::class, 'create'])->name('departments.transactions.create');
        Route::post('/idara/{department}/miamala', [DepartmentTransactionController::class, 'store'])->name('departments.transactions.store');
        Route::get('/idara/{department}/miamala/{transaction}/hariri', [DepartmentTransactionController::class, 'edit'])->name('departments.transactions.edit');
        Route::put('/idara/{department}/miamala/{transaction}', [DepartmentTransactionController::class, 'update'])->name('departments.transactions.update');
        Route::delete('/idara/{department}/miamala/{transaction}', [DepartmentTransactionController::class, 'destroy'])->name('departments.transactions.destroy');

        // Ratiba ya Mwaka (Supports both schedules.* and departments.schedules.* names)
        Route::get('/idara/{department}/ratiba', [AnnualScheduleController::class, 'index'])->name('schedules.index');
        Route::post('/idara/{department}/ratiba', [AnnualScheduleController::class, 'store'])->name('schedules.store');
        Route::put('/idara/{department}/ratiba/{schedule}', [AnnualScheduleController::class, 'update'])->name('schedules.update');
        Route::delete('/idara/{department}/ratiba/{schedule}', [AnnualScheduleController::class, 'destroy'])->name('schedules.destroy');

        // Aliases for departments.schedules.*
        Route::name('departments.')->group(function () {
            Route::get('/idara/{department}/ratiba/alias-index', [AnnualScheduleController::class, 'index'])->name('schedules.index');
            Route::post('/idara/{department}/ratiba/alias-store', [AnnualScheduleController::class, 'store'])->name('schedules.store');
            Route::put('/idara/{department}/ratiba/{schedule}/alias-update', [AnnualScheduleController::class, 'update'])->name('schedules.update');
            Route::delete('/idara/{department}/ratiba/{schedule}/alias-destroy', [AnnualScheduleController::class, 'destroy'])->name('schedules.destroy');
        });

        Route::post('/idara/{department}/shughuli', [ActivityLogController::class, 'store'])->name('activity-logs.store');

        Route::get('/idara/{department}/maendeleo', [DepartmentProgressController::class, 'show'])->name('departments.progress');

        Route::get('/idara/{department}/ripoti', [ReportController::class, 'index'])->name('departments.reports.index');
        Route::post('/idara/{department}/ripoti/zalisha', [ReportController::class, 'generate'])->name('departments.reports.generate');
        Route::get('/idara/{department}/ripoti/{report}/pakua', [ReportController::class, 'download'])->name('departments.reports.download');
    });
});
