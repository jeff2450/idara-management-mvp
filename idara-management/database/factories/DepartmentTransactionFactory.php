<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\DepartmentTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DepartmentTransaction>
 */
class DepartmentTransactionFactory extends Factory
{
    protected $model = DepartmentTransaction::class;

    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'type' => fake()->randomElement(['Michango', 'Zawadi', 'Manunuzi']),
            'amount' => fake()->randomFloat(2, 1000, 200000),
            'description' => fake()->sentence(),
            'recorded_by' => User::factory(),
            'occurred_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
