<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'system',
                'password' => bcrypt('1266Droma@'),
                'role' => ['superadmin'],
                'email_verified_at' => now(),
            ]
        );
    }
}
