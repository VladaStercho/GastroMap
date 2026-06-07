<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Створюємо тестових користувачів для різних ролей з ТЗ

        // Створюємо Адміністратора (для модерації закладів та відгуків)
        User::updateOrCreate(
            ['email' => 'admin@gastromap.com'],
            [
                'name' => 'Головний Адмін',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'avatar_url' => 'https://api.dicebear.com/7.x/bottts/svg?seed=admin',
            ]
        );

        // Створюємо Власника закладу (який зможе редагувати свій профіль/меню)
        User::updateOrCreate(
            ['email' => 'owner@gastromap.com'],
            [
                'name' => 'Олексій (Власник кав’ярні)',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'avatar_url' => 'https://api.dicebear.com/7.x/bottts/svg?seed=owner',
            ]
        );

        // Створюємо звичайного користувача/гостя (який залишає відгуки та додає в обране)
        User::updateOrCreate(
            ['email' => 'guest@gastromap.com'],
            [
                'name' => 'Іван Тестер',
                'password' => Hash::make('password123'),
                'role' => 'guest',
                'avatar_url' => 'https://api.dicebear.com/7.x/bottts/svg?seed=guest',
            ]
        );

        // 2. Викликаємо сідер для наповнення закладів (кав'ярень, ресторанів)
        $this->call(EstablishmentSeeder::class);
    }
}
