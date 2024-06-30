<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create ingredients
        $beef = Ingredient::factory()->create(['name' => 'Beef', 'stock_in_grams' => 20000]); // 20kg
        $cheese = Ingredient::factory()->create(['name' => 'Cheese', 'stock_in_grams' => 5000]); // 5kg
        $onion = Ingredient::factory()->create(['name' => 'Onion', 'stock_in_grams' => 1000]); // 1kg

        // Attach ingredients to products
        $burger = Product::where('name', 'Burger')->first();
        $burger->ingredients()->attach([
            $beef->id => ['quantity_in_grams' => 150], // 150g Beef
            $cheese->id => ['quantity_in_grams' => 30], // 30g Cheese
            $onion->id => ['quantity_in_grams' => 20], // 20g Onion
        ]);

        $cheeseburger = Product::where('name', 'Cheeseburger')->first();
        $cheeseburger->ingredients()->attach([
            $beef->id => ['quantity_in_grams' => 150], // 150g Beef
            $cheese->id => ['quantity_in_grams' => 50], // 50g Cheese
            $onion->id => ['quantity_in_grams' => 20], // 20g Onion
        ]);
    }
}
