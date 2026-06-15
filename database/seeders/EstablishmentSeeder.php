<?php

namespace Database\Seeders;

use App\Models\Establishment;
use Illuminate\Database\Seeder;

class EstablishmentSeeder extends Seeder
{
    public function run(): void
    {

        Establishment::truncate();


        Establishment::create([
            'name' => 'Кав’ярня "Riverside"',
            'search_keywords' => 'riverside, ріверсайд, ріксайд, риверсайд, рівер, сайд, набережна, кавʼярня біля річки', // Усі варіанти для Укр пошуку
            'type' => 'cafe',
            'phone' => '+380501112233',
            'description' => 'Кава свіжого обсмаження на набережній Ужа.',
            'address' => 'Набережна Незалежності, 6, Ужгород',
            'city' => 'Ужгород',
            'latitude' => 48.6212,
            'longitude' => 22.2978,
            'average_check' => 150,
            'has_wifi' => true,
            'has_terrace' => true,
            'is_pet_friendly' => true,
            'laptop_friendly' => true,
            'is_approved' => true,
        ]);


        Establishment::create([
            'name' => 'Ресторан "Daily"',
            'search_keywords' => 'daily, дейлі, дайлі, деілі, даілі, дейли, дейлі комбо', // Варіанти написання кирилицею
            'type' => 'restaurant',
            'phone' => '+380313144455',
            'description' => 'Чудова європейська кухня в самому центрі міста.',
            'address' => 'вулиця Возз’єднання, 1, Мукачево',
            'city' => 'Мукачево',
            'latitude' => 48.4415,
            'longitude' => 22.7212,
            'average_check' => 450,
            'has_wifi' => true,
            'has_terrace' => true,
            'is_pet_friendly' => false,
            'laptop_friendly' => false,
            'is_approved' => true,
        ]);


        Establishment::create([
            'name' => 'Паб "Хустський Замок"',
            'search_keywords' => 'хустський замок, замок, паб замок, хуст замок, пиво замок',
            'type' => 'pub',
            'phone' => '+380677778899',
            'description' => 'Крафтове пиво та закарпатські закуски біля підніжжя замкової гори.',
            'address' => 'вулиця Замкова, 12, Хуст',
            'city' => 'Хуст',
            'latitude' => 48.1794,
            'longitude' => 23.2982,
            'average_check' => 300,
            'has_wifi' => false,
            'has_terrace' => true,
            'is_pet_friendly' => true,
            'laptop_friendly' => false,
            'is_approved' => true,
        ]);

        Establishment::factory()->count(12)->create();
    }
}
