<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         User::create([
            'first_name' => 'Admin',
            'last_name'  => 'User',
            'username'   => 'admin',
            'email'      => 'admin@example.com',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
        ]);

        // 10 random users
        User::factory(10)->create();
    }
}
