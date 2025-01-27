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

        $max_attendance = fake()->numberBetween(0,366);
        $used_attendance = fake()->numberBetween(0, $max_attendance);

        $department_slug = fake()->randomElement(['geoict', 'geodesy', 'relation-management', 'finance', 'hrm', 'ict']);
        if ($department_slug === 'geoict') {
            $subdepartment_slug = fake()->randomElement(['development', 'scanning', 'processing']);
        } else if($department_slug == 'geodesy') {
            $subdepartment_slug = fake()->randomElement(['preparation', 'measuring', 'document']);
        } else {
            $subdepartment_slug = null;
        }

        if ($subdepartment_slug != null) {
            $role_slug = fake()->randomElement(['medewerker', 'medewerker', 'medewerker', 'sub-manager']);
        } else if ($subdepartment_slug == null) {
            $role_slug = "manager";
        }

        return [
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),

            'role_slug' => $role_slug,
            'department_slug' => $department_slug,
            'subdepartment_slug' => $subdepartment_slug,
            'supervisor_id' => null,

            'verified' => fake()->boolean(85),
            'blocked' => 0,

            'first_name' => fake()->firstName(),
            'sure_name' => fake()->lastName(),
            'bsn' => fake()->unique()->numberBetween(100000000, 999999999),
            'date_of_service' => fake()->date(),

            'used_attendance' => $used_attendance,
            'max_attendance' => $max_attendance,

            'remember_token' => Str::random(10),
        ];
    }
}
