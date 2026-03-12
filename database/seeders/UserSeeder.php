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
            ['email' => 'user1@gmail.com'],
            [
                'name' => 'User O`ne',
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
                'name' => 'User Two',
                'password' => bcrypt('password'),
                'profile_setup' => true,
                'is_verified' => true,
                'email_verified_at' => now(),
                'verified_at' => now(),
                'phone' => '0123456787',
                'gender' => 'male',
            ]
        );
        // user three
        \App\Models\User::updateOrCreate(
            ['email' => 'user3@gmail.com'],
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
        // user three
        \App\Models\User::updateOrCreate(
            ['email' => 'user4@gmail.com'],
            [
                'name' => 'User Four',
                'password' => bcrypt('password'),
                'profile_setup' => true,
                'is_verified' => true,
                'email_verified_at' => now(),
                'verified_at' => now(),
                'phone' => '0123456787',
                'gender' => 'male',
            ]
        );
        // user three
        \App\Models\User::updateOrCreate(
            ['email' => 'user5@gmail.com'],
            [
                'name' => 'User Five',
                'password' => bcrypt('password'),
                'profile_setup' => true,
                'is_verified' => true,
                'email_verified_at' => now(),
                'verified_at' => now(),
                'phone' => '0123456787',
                'gender' => 'male',
            ]
        );
        // user three
        \App\Models\User::updateOrCreate(
            ['email' => 'user6@gmail.com'],
            [
                'name' => 'User Six',
                'password' => bcrypt('password'),
                'profile_setup' => true,
                'is_verified' => true,
                'email_verified_at' => now(),
                'verified_at' => now(),
                'phone' => '0123456787',
                'gender' => 'male',
            ]
        );
        // user three
        \App\Models\User::updateOrCreate(
            ['email' => 'user7@gmail.com'],
            [
                'name' => 'User Seven',
                'password' => bcrypt('password'),
                'profile_setup' => true,
                'is_verified' => true,
                'email_verified_at' => now(),
                'verified_at' => now(),
                'phone' => '0123456787',
                'gender' => 'male',
            ]
        );
        // user three
        \App\Models\User::updateOrCreate(
            ['email' => 'user8@gmail.com'],
            [
                'name' => 'User Eight',
                'password' => bcrypt('password'),
                'profile_setup' => true,
                'is_verified' => true,
                'email_verified_at' => now(),
                'verified_at' => now(),
                'phone' => '0123456787',
                'gender' => 'male',
            ]
        );
        // user three
        \App\Models\User::updateOrCreate(
            ['email' => 'user9@gmail.com'],
            [
                'name' => 'User Nine',
                'password' => bcrypt('password'),
                'profile_setup' => true,
                'is_verified' => true,
                'email_verified_at' => now(),
                'verified_at' => now(),
                'phone' => '0123456787',
                'gender' => 'male',
            ]
        );
        // user three
        \App\Models\User::updateOrCreate(
            ['email' => 'user10@gmail.com'],
            [
                'name' => 'User Ten',
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
