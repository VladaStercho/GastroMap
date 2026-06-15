<?php

namespace Database\Seeders;

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



        User::updateOrCreate(
            ['email' => 'admin@gastromap.com'],
            [
                'name' => 'Головний Адмін',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'avatar_url' => 'https://api.dicebear.com/7.x/bottts/svg?seed=admin',
            ]
        );


        User::updateOrCreate(
            ['email' => 'owner@gastromap.com'],
            [
                'name' => 'Олексій (Власник кав’ярні)',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'avatar_url' => 'https://api.dicebear.com/7.x/bottts/svg?seed=owner',
            ]
        );


        User::updateOrCreate(
            ['email' => 'guest@gastromap.com'],
            [
                'name' => 'Іван Тестер',
                'password' => Hash::make('password123'),
                'role' => 'guest',
                'avatar_url' => 'https://api.dicebear.com/7.x/bottts/svg?seed=guest',
            ]
        );


        $this->call(EstablishmentSeeder::class);
    }
}
