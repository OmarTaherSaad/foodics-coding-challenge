<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ingredient>
 */
class IngredientFactory extends Factory
{
    protected $model = \App\Models\Ingredient::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'stock_in_grams' => $this->faker->numberBetween(1000, 20000), // 1kg to 20kg
            'restock_needed' => false,
            'notified_for_restock_sent' => false,
        ];
    }
}
