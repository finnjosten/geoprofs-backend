<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttendanceStatus>
 */
class AttendanceStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $name = $this->faker->words(1, true);
        $slug = vlxSlugify($name);

        return [
            'slug' => $slug,
            'name' => $name,
            'description' => $this->faker->text(),
            'show_in_agenda' => $this->faker->boolean(20),
            'default' => false,
        ];
    }
}
