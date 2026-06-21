<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Create the admin user
        $user = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'remember_token' => Str::random(10),
            ]
        );

        // Assign the 'admin' role to the created user
        $user->assignRole($adminRole);

        // Create the test role
        $testRole = Role::firstOrCreate(['name' => 'test']);

        // Create the test user
        $testUser = User::firstOrCreate(
            ['email' => 'test@test.com'],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'remember_token' => Str::random(10),
            ]
        );

        // Assign the 'test' role to the created user
        $testUser->assignRole($testRole);
    }
}
