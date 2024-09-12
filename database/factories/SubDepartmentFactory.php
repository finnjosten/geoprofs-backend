<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubDepartment>
 */
class SubDepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $word = fake()->word();
        return [
            'name' => $word,
            'slug' => $word,
            'department_slug' => fake()->word(),
        ];
    }
}
