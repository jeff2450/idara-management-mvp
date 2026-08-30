<?php

namespace Database\Factories;

use App\Models\AnnualSchedule;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AnnualSchedule>
 */
class AnnualScheduleFactory extends Factory
{
    protected $model = AnnualSchedule::class;

    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->sentence(),
            'planned_year' => now()->year,
            'planned_month' => fake()->numberBetween(1, 12),
            'status' => 'pending',
        ];
    }
}
