<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Faker\Factory as Faker;

class InstructorNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $faker = Faker::create();

        $baseUrl = 'http://127.0.0.1:8000/api/instructor';

        // Notification templates
        $notificationTemplates = [
            [
                'body' => "You received a new review on 'UI UX Design'",
                'icon' => 'fa fa-star',
                'url'  => '/courses/3/reviews',
            ],
            [
                'body' => "Mohamed Ali enrolled in your course 'Design Thinking'",
                'icon' => 'fa fa-user-plus',
                'url'  => '/courses/15/enrollments',
            ],
            [
                'body' => "Student Sara Ahmed submitted her assignment for 'Lesson 4'",
                'icon' => 'fa fa-file-alt',
                'url'  => '/courses/12/assignments/4',
            ],
            [
                'body' => "Ahmed Khaled completed your course 'PHP for Beginners'",
                'icon' => 'fa fa-graduation-cap',
                'url'  => '/courses/18/completions',
            ],
            [
                'body' => "You received a new message from Omar El-Sayed",
                'icon' => 'fa fa-envelope',
                'url'  => '/messages/omar-el-sayed',
            ],
        ];

        // Create 5 instructors
        for ($i = 1; $i <= 5; $i++) {
            $instructor = User::create([
                'first_name'       => "Instructor",
                'last_name'        => "$i",
                'username'         => "instructor$i",
                'email'            => "instructor$i@example.com",
                'password'         => Hash::make('123456789'),
                'role'             => 'instructor',
                'status'           => 'active', // All instructors active
                'email_verified_at'=> now(),
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            // Create 7 to 10 notifications for each instructor
            $notificationCount = rand(7, 10);

            for ($j = 0; $j < $notificationCount; $j++) {
                $template = $faker->randomElement($notificationTemplates);

                DB::table('notifications')->insert([
                    'id'              => Str::uuid(),
                    'type'            => 'App\Notifications\GenericNotification',
                    'notifiable_id'   => $instructor->id,
                    'notifiable_type' => User::class,
                    'data' => json_encode([
                        'body' => $template['body'],
                        'icon' => $template['icon'],
                        'url'  => $baseUrl . $template['url'], // absolute URL
                    ]),
                    'read_at'    => null,
                    'created_at' => $faker->dateTimeBetween('-24 hours', 'now'),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
