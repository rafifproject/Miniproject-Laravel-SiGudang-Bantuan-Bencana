<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'sku' => fake()->unique()->bothify('SKU-####'),
            'name' => fake()->words(3, true),
            'unit' => fake()->randomElement(['pcs', 'karton', 'box', 'karung', 'paket', 'botol', 'unit', 'set']),
            'price' => fake()->randomFloat(2, 10000, 500000),
            'stock' => fake()->numberBetween(0, 500),
            'minimum_stock' => fake()->numberBetween(10, 50),
            'description' => fake()->sentence(),
            'is_public' => true,
        ];
    }
}
