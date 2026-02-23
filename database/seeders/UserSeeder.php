<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // admin dashboard
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'profile_setup' => true,
                'is_admin' => true,
                'is_verified' => true,
                'email_verified_at' => now(),
                'verified_at' => now(),
                'phone' => '0123456789',
                'gender' => 'male',
            ]
        );

        // user two
        \App\Models\User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'User Two',
                'password' => bcrypt('password'),
                'profile_setup' => false,
                'is_verified' => true,
                'email_verified_at' => now(),
                'verified_at' => now(),
                'phone' => '0123456788',
                'gender' => 'female',
            ]
        );

        // user three
        \App\Models\User::updateOrCreate(
            ['email' => 'user2@gmail.com'],
            [
                'name' => 'User Three',
                'password' => bcrypt('password'),
                'profile_setup' => true,
                'is_verified' => true,
                'email_verified_at' => now(),
                'verified_at' => now(),
                'phone' => '0123456787',
                'gender' => 'male',
            ]
        );
    }
}
