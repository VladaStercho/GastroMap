<?php

namespace Database\Factories;

use App\Models\Establishment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EstablishmentFactory extends Factory
{
    protected $model = Establishment::class;

    public function definition(): array
    {
        $cities = [
            'Ужгород'  => [48.6212, 22.2978, ['вулиця Корзо', 'набережна Незалежності', 'площа Петефі', 'вулиця Духновича', 'вулиця Волошина']],
            'Мукачево' => [48.4415, 22.7212, ['вулиця Миру', 'площа Кирила і Мефодія', 'вулиця Возз’єднання', 'вулиця Ярослава Мудрого']],
            'Хуст'     => [48.1794, 23.2982, ['вулиця Замкова', 'вулиця Карпатської Січі', 'вулиця Івана Франка', 'майдан Незалежності']],
        ];

        $cityName = array_rand($cities);
        [$baseLat, $baseLng, $streets] = $cities[$cityName];

        $type = $this->faker->randomElement(['cafe', 'restaurant', 'pub']);

        $prefix = ['cafe' => 'Кав’ярня', 'restaurant' => 'Ресторан', 'pub' => 'Паб'][$type];

        $titles = [
            'cafe'       => ['Aroma', 'Затишок', 'Теплиця', 'Verde', 'Сонях', 'Кавова Зерня', 'Панорама', 'Дольче'],
            'restaurant' => ['Старе Місто', 'Едем', 'Карпатська Хата', 'Трапезна', 'Білий Олень', 'Аркада', 'Гостинна', 'Фльорес'],
            'pub'        => ['Стара Бочка', 'Хмільний Двір', 'Грог', 'Корчма', 'Дубова Діжка', 'Бровар', 'Опришки', 'Магнат'],
        ];

        $title = $this->faker->randomElement($titles[$type]);

        $descriptions = [
            'cafe'       => 'Авторська кава, свіжа випічка та затишна атмосфера в серці міста.',
            'restaurant' => 'Європейська та закарпатська кухня зі страв із сезонних продуктів.',
            'pub'        => 'Крафтове пиво, жива музика та смачні закуски у дружній компанії.',
        ];

        $check = match ($type) {
            'cafe'       => $this->faker->numberBetween(80, 250),
            'restaurant' => $this->faker->numberBetween(300, 700),
            'pub'        => $this->faker->numberBetween(200, 450),
        };

        return [
            'name' => $prefix . ' "' . $title . '"',
            'search_keywords' => Str::lower($title . ', ' . $prefix . ', ' . $cityName),
            'type' => $type,
            'phone' => '+38050' . $this->faker->numberBetween(1000000, 9999999),
            'description' => $descriptions[$type],
            'address' => $this->faker->randomElement($streets) . ', ' . $this->faker->numberBetween(1, 80) . ', ' . $cityName,
            'city' => $cityName,
            'latitude' => round($baseLat + $this->faker->randomFloat(4, -0.012, 0.012), 6),
            'longitude' => round($baseLng + $this->faker->randomFloat(4, -0.012, 0.012), 6),
            'average_check' => $check,
            'has_wifi' => $this->faker->boolean(75),
            'has_terrace' => $this->faker->boolean(60),
            'is_pet_friendly' => $this->faker->boolean(45),
            'laptop_friendly' => $this->faker->boolean(50),
            'is_approved' => true,
            'photos' => null,
        ];
    }
}
