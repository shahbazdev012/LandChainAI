<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Registry Admin',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
            ],
        );
        $admin->syncRoles(['admin']);

        $officer = User::firstOrCreate(
            ['email' => 'officer@landchain.test'],
            [
                'name' => 'Registration Officer',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
            ],
        );
        $officer->syncRoles(['officer']);
    }
}
