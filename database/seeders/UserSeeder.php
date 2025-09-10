<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\{User, InstructorProfile, StudentProfile};

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::factory()->create([
            'email' => 'admin@example.com',
            'role'  => 'admin',
        ]);

        // Instructors
        User::factory(20)->create(['role' => 'instructor'])->each(function ($user) {
            InstructorProfile::factory()->create(['user_id' => $user->id]);
        });

        // Students
        User::factory(100)->create(['role' => 'student'])->each(function ($user) {
            StudentProfile::factory()->create(['user_id' => $user->id]);
        });
    }
}
