<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Role::factory()->create([
            'name' => 'User',
            'slug' => 'user',
        ]);

        Role::factory()->create([
            'name' => 'Deparment Head',
            'slug' => 'deparment-head',
        ]);

        Role::factory()->create([
            'name' => 'Admin',
            'slug' => 'admin',
        ]);



        Department::factory()->create([
            'name' => 'IT',
            'slug' => 'it',
        ]);

        Department::factory()->create([
            'name' => 'Scout',
            'slug' => 'scout',
        ]);

        Department::factory()->create([
            'name' => 'Digital',
            'slug' => 'digital',
        ]);



        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory(4)->create();
    }
}
