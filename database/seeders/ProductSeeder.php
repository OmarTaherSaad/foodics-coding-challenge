<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create challenge's main product
        Product::factory()->create(['name' => 'Burger']);
        // Other dummy products
        Product::factory()->create(['name' => 'Cheeseburger']);
    }
}
