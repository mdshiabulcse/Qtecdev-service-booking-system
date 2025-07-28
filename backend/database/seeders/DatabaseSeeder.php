<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('123456'),
            'is_admin' => true,
        ]);


        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('123456'),
            'is_admin' => false,
        ]);


        Service::create([
            'name' => 'Haircut',
            'description' => 'Professional haircut service',
            'price' => 25.00,
            'status' => true,
        ]);

        Service::create([
            'name' => 'Massage',
            'description' => 'Relaxing full body massage',
            'price' => 60.00,
            'status' => true,
        ]);

        Service::create([
            'name' => 'Manicure',
            'description' => 'Hand care and nail treatment',
            'price' => 35.00,
            'status' => true,
        ]);
    }
}
