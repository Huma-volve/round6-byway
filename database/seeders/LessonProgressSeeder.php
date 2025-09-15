<?php



namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;

class LessonProgressSeeder extends Seeder
{
//     /**
//      * Run the database seeds.
//      */
    public function run(): void
    {
        $enrollments = Enrollment::all();

        foreach ($enrollments as $enrollment) {
            $lessons = Lesson::where('course_id', $enrollment->course_id)->get();

            foreach ($lessons as $lesson) {
                LessonProgress::factory()->create([
                    'enrollment_id' => $enrollment->id,
                    'lesson_id'     => $lesson->id,
                ]);
            }
        }
    }
}
