<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= bcrypt('password'),
            'api_token' => Str::random(60),

            'department_id' => fake()->numberBetween(1, 3),
            'role_id' => fake()->numberBetween(1, 2),
            'verified' => fake()->boolean(85),
            'blocked' => fake()->boolean(5),

            'remember_token' => Str::random(10),
        ];
    }
}
