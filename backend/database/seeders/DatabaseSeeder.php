<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@school.com',
            'phone' => '+1234567890',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'John Teacher',
            'email' => 'teacher@school.com',
            'phone' => '+1234567891',
            'password' => Hash::make('teacher123'),
            'role' => 'teacher',
        ]);

        User::create([
            'name' => 'Jane Parent',
            'email' => 'parent@school.com',
            'phone' => '+1234567892',
            'password' => Hash::make('parent123'),
            'role' => 'parent',
        ]);
    }
}
