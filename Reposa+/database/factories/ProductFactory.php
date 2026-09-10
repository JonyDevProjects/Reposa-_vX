<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Almohada '.$this->faker->words(2, true),
            'material' => $this->faker->randomElement(['Viscoelástica', 'Látex', 'Pluma', 'Sintético']),
            'firmness' => $this->faker->randomElement(['Alta', 'Media', 'Baja']),
            'dimensions' => $this->faker->randomElement(['70x40 cm', '90x40 cm', '135x40 cm']),
            'price' => $this->faker->randomFloat(2, 20, 150),
            'stock' => $this->faker->numberBetween(0, 50),
            'description' => $this->faker->paragraph(),
        ];
    }
}
