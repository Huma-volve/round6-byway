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
             'balance'  => 0,
        ]);

        // Instructors
        User::factory(20)->create(['role' => 'instructor'])->each(function ($user) {
            $user->update([
                'balance' => fake()->randomFloat(2, 100, 2000), // random balance
            ]);
            InstructorProfile::factory()->create(['user_id' => $user->id]);
        });

        // Students
        User::factory(100)->create(['role' => 'student',  'balance' => 0])->each(function ($user) {
            StudentProfile::factory()->create(['user_id' => $user->id]);
        });
    }
}
