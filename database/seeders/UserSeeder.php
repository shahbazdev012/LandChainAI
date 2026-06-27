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
        $users = [
            ['Registry Admin', 'admin@admin.com', 'admin'],
            ['Registration Officer', 'officer@landchain.test', 'officer'],
            ['Data Entry Clerk', 'dataentry@landchain.test', 'data_entry'],
        ];

        foreach ($users as [$name, $email, $role]) {
            $user = User::firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'email_verified_at' => now(), 'password' => Hash::make('password123')],
            );
            $user->syncRoles([$role]);
        }
    }
}
