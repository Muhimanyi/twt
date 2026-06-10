<?php

namespace Database\Factories;

use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProvinceFactory extends Factory
{
    protected $model = Province::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->city(),
            'address' => fake()->address(),
            'postal_code' => fake()->postcode(),
            'contacts_email' => fake()->email(),
            'contacts_phone' => fake()->phoneNumber(),
            'language_lingala' => false,
            'language_kikongo' => false,
            'language_kiluba' => false,
            'language_kiswahili' => false,
            'creation_date' => now(),
        ];
    }
}
