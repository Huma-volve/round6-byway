<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Wishlist;

class WishlistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::where('role', 'student')->get();
        $courses = Course::pluck('id')->toArray();

        foreach ($students as $student) {
            $wishCourses = collect($courses)->random(rand(1, 3));
            foreach ($wishCourses as $courseId) {
                Wishlist::factory()->create([
                    'user_id'   => $student->id,
                    'course_id' => $courseId,
                ]);
            }
        }
    }
}
