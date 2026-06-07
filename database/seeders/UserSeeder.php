<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'adminsuper@example.com'],
            [
                'name' => 'Super Admin',
                'role' => 'adminsuper',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'is_active' => true,
                'pin' => '123456',
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'role' => 'admin',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'is_active' => true,
                'pin' => null,
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'User Demo',
                'role' => 'user',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'is_active' => true,
                'pin' => null,
            ]
        );
    }
}
