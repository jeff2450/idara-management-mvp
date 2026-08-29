<?php

namespace App\Providers;

use App\Models\Department;
use App\Policies\DepartmentPolicy;
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
    }
}
