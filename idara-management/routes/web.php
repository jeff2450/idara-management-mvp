<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AnnualScheduleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DepartmentMemberController;
use App\Http\Controllers\DepartmentProgressController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {

    Route::get('/dashibodi', [DashboardController::class, 'index'])->name('dashboard');

    // Idara (Departments) - orodha inachujwa kiotomatiki ndani ya controller:
    // Admin anaona idara zote, kiongozi/mwanachama anaona idara zake tu.
    Route::resource('idara', DepartmentController::class)
        ->parameters(['idara' => 'department'])
        ->except(['create', 'store', 'edit', 'update', 'destroy'])
        ->names('departments');

    // Uundaji/uhariri/ufutaji wa idara - Admin pekee (angalia DepartmentPolicy)
    Route::middleware('role:admin')->group(function () {
        Route::get('/idara/unda', [DepartmentController::class, 'create'])->name('departments.create');
        Route::post('/idara', [DepartmentController::class, 'store'])->name('departments.store');
        Route::get('/idara/{department}/hariri', [DepartmentController::class, 'edit'])->name('departments.edit');
        Route::put('/idara/{department}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/idara/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
    });

    // Kuongeza/kuondoa kiongozi au mwanachama kwenye idara, na Awamu ya 3
    // (ratiba, shughuli, maendeleo) - vyote vinahitaji 'department.access'
    // kuhakikisha mtumiaji anahusika na idara hii - angalia EnsureDepartmentAccess.
    Route::middleware('department.access')->group(function () {
        Route::post('/idara/{department}/wanachama', [DepartmentMemberController::class, 'store'])
            ->name('departments.members.store');
        Route::delete('/idara/{department}/wanachama/{user}', [DepartmentMemberController::class, 'destroy'])
            ->name('departments.members.destroy');

        // Awamu ya 3 - prd.md §5.3
        Route::get('/idara/{department}/ratiba', [AnnualScheduleController::class, 'index'])->name('schedules.index');
        Route::post('/idara/{department}/ratiba', [AnnualScheduleController::class, 'store'])->name('schedules.store');
        Route::put('/idara/{department}/ratiba/{schedule}', [AnnualScheduleController::class, 'update'])->name('schedules.update');
        Route::delete('/idara/{department}/ratiba/{schedule}', [AnnualScheduleController::class, 'destroy'])->name('schedules.destroy');

        Route::post('/idara/{department}/shughuli', [ActivityLogController::class, 'store'])->name('activity-logs.store');

        Route::get('/idara/{department}/maendeleo', [DepartmentProgressController::class, 'show'])->name('departments.progress');
    });
});
