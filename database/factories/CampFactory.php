<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Camp>
 */
class CampFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->words(2, true);

        return [
            'title' => ucwords($title),
            'slug' => Str::slug($title),
            'description' => fake()->sentence(),
            'price' => fake()->randomElement([140000, 280000]),
        ];
    }
}
