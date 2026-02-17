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
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'profile_setup' => true,
            'is_verified' => true,
            'email_verified_at' => now(),
            'verified_at' => now(),
            'phone' => '0123456789',
            'gender' => 'male',
        ]);

        // user three
        \App\Models\User::create([
             'name' => 'User Two',
             'email' => 'user@gmail.com',
             'password' => bcrypt('password'),
             'profile_setup' => false,
             'is_verified' => true,
             'email_verified_at' => now(),
             'verified_at' => now(),
             'phone' => '0123456788',
             'gender' => 'female',
        ]);

        \App\Models\User::create([
             'name' => 'User Three',
             'email' => 'user2@gmail.com',
             'password' => bcrypt('password'),
             'profile_setup' => true,
             'is_verified' => true,
             'email_verified_at' => now(),
             'verified_at' => now(),
             'phone' => '0123456787',
             'gender' => 'male',
        ]);
    }
}
