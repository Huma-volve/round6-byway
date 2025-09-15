<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Review;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            UserSeeder::class,
            InstructorProfileSeeder::class,
            StudentProfileSeeder::class,
            CategorySeeder::class,
            CourseSeeder::class,
            LessonSeeder::class,
            EnrollmentSeeder::class,
            PlatformSettingsSeeder::class,
            // LessonProgressSeeder::class,
            // ReviewSeeder::class,
            // WishlistSeeder::class,
            OrderSeeder::class,
            // OrderItemSeeder::class,
            // PaymentSeeder::class,
        ]);
    }
}
