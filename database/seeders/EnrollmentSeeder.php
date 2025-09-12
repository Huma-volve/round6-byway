<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::where('role', 'student')->get();
        $courses = Course::pluck('id')->toArray();

        foreach ($students as $student) {
            // pick between 1 and 5 random UNIQUE courses
            $enrolledCourses = collect($courses)->random(rand(1, min(5, count($courses))))->unique();

            foreach ($enrolledCourses as $courseId) {
                Enrollment::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'course_id'  => $courseId,
                    ],
                    [
                        'enrolled_at' => now()->subDays(rand(1, 365)),
                        'progress_percentage' => rand(0, 100),
                    ]
                );
            }
        }
    }
}
