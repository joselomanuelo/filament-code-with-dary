<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->name(),
            'brand_id' => 1,
            'slug' => fake()->unique()->slug(),
            'sku' => fake()->unique()->userName(),
            'image' => fake()->url(),
            'quantity' => fake()->randomNumber(3),
            'price' => fake()->randomFloat(2,0, 100000),
            'published_at' => now()
        ];
    }
}
