<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DepartmentMemberController;
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

    // Kuongeza/kuondoa kiongozi au mwanachama kwenye idara.
    // 'department.access' inahakikisha kiongozi anaweza kubadilisha idara yake pekee;
    // Admin (aliye na role 'admin') anaruhusiwa kwa idara zote - angalia EnsureDepartmentAccess.
    Route::middleware('department.access')->group(function () {
        Route::post('/idara/{department}/wanachama', [DepartmentMemberController::class, 'store'])
            ->name('departments.members.store');
        Route::delete('/idara/{department}/wanachama/{user}', [DepartmentMemberController::class, 'destroy'])
            ->name('departments.members.destroy');
    });
});
