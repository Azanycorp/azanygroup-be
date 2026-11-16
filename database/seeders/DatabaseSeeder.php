<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         User::updateOrCreate(
            ['email' => 'admin@azanygroup.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('Password@123'),
                'email_verified_at' => now(),
            ],
        );
    }
}
