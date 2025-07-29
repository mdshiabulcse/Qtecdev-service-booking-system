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
            'email' => 'admin@shiabul.com',
            'password' => Hash::make('12345678'),
            'is_admin' => true,
        ]);


        User::create([
            'name' => 'Regular User',
            'email' => 'user@shiabul.com',
            'password' => Hash::make('12345678'),
            'is_admin' => false,
        ]);


        Service::create([
            'name' => 'Concert Ticket',
            'description' => 'General admission ticket for music concert',
            'price' => 800.00,
            'status' => true,
        ]);

        Service::create([
            'name' => 'VIP Concert Ticket',
            'description' => 'VIP access with premium seating and perks',
            'price' => 2500.00,
            'status' => true,
        ]);

        Service::create([
            'name' => 'Sports Event Ticket',
            'description' => 'Ticket for premier league football match',
            'price' => 1500.00,
            'status' => true,
        ]);

        Service::create([
            'name' => 'Standard Room',
            'description' => 'Overnight stay in standard room with breakfast',
            'price' => 2500.00,
            'status' => true,
        ]);

        Service::create([
            'name' => 'Deluxe Suite',
            'description' => 'Overnight stay in deluxe suite with all amenities',
            'price' => 5000.00,
            'status' => true,
        ]);

        Service::create([
            'name' => 'Presidential Suite',
            'description' => 'Luxury overnight stay in presidential suite',
            'price' => 12000.00,
            'status' => true,
        ]);
    }
}
