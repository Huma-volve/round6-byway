<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Course, Lesson, Enrollment, LessonProgress, Review, Wishlist, User};

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // // Create 50 courses
        // Course::factory(50)->create()->each(function ($course) {
        //     // Lessons
        //     $lessons = Lesson::factory(10)->create(['course_id' => $course->id]);
        //     $course->update([
        //         'lessons_count' => $lessons->count(),
        //         'total_minutes' => $lessons->sum('duration_minutes'),
        //         'duration_hours' => ceil($lessons->sum('duration_minutes') / 60),
        //     ]);

        //     // Enrollments for random students
        //     $students = User::where('role', 'student')->inRandomOrder()->take(20)->get();
        //     foreach ($students as $student) {
        //         $enrollment = Enrollment::factory()->create([
        //             'course_id' => $course->id,
        //             'student_id' => $student->id,
        //         ]);

        //         foreach ($lessons as $lesson) {
        //             LessonProgress::factory()->create([
        //                 'enrollment_id' => $enrollment->id,
        //                 'lesson_id'     => $lesson->id,
        //             ]);
        //         }

        //         // Reviews
        //         if (rand(0, 1)) {
        //             Review::factory()->create([
        //                 'course_id' => $course->id,
        //                 'user_id' => $student->id
        //             ]);
        //         }

        //         // Wishlist
        //         if (rand(0, 1)) {
        //             Wishlist::factory()->create([
        //                 'course_id' => $course->id,
        //                 'user_id' => $student->id
        //             ]);
        //         }
        //     }
        // });


        // Create 50 courses
        Course::factory(50)->create()->each(function ($course) {
            // Lessons
            $lessons = Lesson::factory(10)->create(['course_id' => $course->id]);
            $course->update([
                'lessons_count' => $lessons->count(),
                'total_minutes' => $lessons->sum('duration_minutes'),
                'duration_hours' => ceil($lessons->sum('duration_minutes') / 60),
            ]);

            // Enrollments for random students
            $students = User::where('role', 'student')->inRandomOrder()->take(20)->get();

            foreach ($students as $student) {
                // ✅ Prevent duplicate enrollments
                $enrollment = Enrollment::updateOrCreate(
                    [
                        'course_id'  => $course->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'enrolled_at'        => now()->subDays(rand(1, 365)),
                        'progress_percentage' => rand(0, 100),
                    ]
                );

                // ✅ Lesson progress (use updateOrCreate too)
                foreach ($lessons as $lesson) {
                    LessonProgress::updateOrCreate(
                        [
                            'enrollment_id' => $enrollment->id,
                            'lesson_id'     => $lesson->id,
                        ],
                        [
                            'is_completed' => rand(0, 1), // ✅ only this field
                        ]
                    );
                }

                // ✅ Reviews
                if (rand(0, 1)) {
                    Review::updateOrCreate(
                        [
                            'course_id' => $course->id,
                            'user_id'   => $student->id,
                        ],
                        [
                            'rating'  => rand(3, 5),
                            'comment' => fake()->sentence(10),
                        ]
                    );
                }

                // ✅ Wishlist
                if (rand(0, 1)) {
                    Wishlist::updateOrCreate(
                        [
                            'course_id' => $course->id,
                            'user_id'   => $student->id,
                        ]
                    );
                }
            }
        });
    }
}
