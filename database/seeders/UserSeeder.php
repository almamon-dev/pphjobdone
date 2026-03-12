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

        // user three to ten
        $usersData = [
            ['name' => 'User One', 'email' => 'user1@gmail.com'],
            ['name' => 'User Two', 'email' => 'user2@gmail.com'],
            ['name' => 'User Three', 'email' => 'user3@gmail.com'],
            ['name' => 'User Four', 'email' => 'user4@gmail.com'],
            ['name' => 'User Five', 'email' => 'user5@gmail.com'],
            ['name' => 'User Six', 'email' => 'user6@gmail.com'],
            ['name' => 'User Seven', 'email' => 'user7@gmail.com'],
            ['name' => 'User Eight', 'email' => 'user8@gmail.com'],
            ['name' => 'User Nine', 'email' => 'user9@gmail.com'],
            ['name' => 'User Ten', 'email' => 'user10@gmail.com'],
        ];

        foreach ($usersData as $u) {
            \App\Models\User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => bcrypt('password'),
                    'profile_setup' => true,
                    'is_verified' => true,
                    'email_verified_at' => now(),
                    'verified_at' => now(), // Added verified_at
                    'phone' => '0123456787',
                    'gender' => 'male',
                ]
            );
        }
    }
}
