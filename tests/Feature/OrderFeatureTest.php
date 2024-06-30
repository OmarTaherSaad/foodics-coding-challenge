<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Ingredient;
use App\Notifications\IngredientLowStockNotification;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OrderFeatureTest extends TestCase
{

    public function testOrderCreation()
    {
        $product = Product::first();

        $response = $this->postJson('/api/orders', [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ]
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Order placed successfully',
            ]);

        // Assert that an order with the given ID exists in the database
        $this->assertDatabaseHas('orders', [
            'id' => $response->json('order.id')
        ]);

        // Assert that the stock of the ingredient has been updated
        $product->ingredients->each(function ($ingredient) {
            $this->assertDatabaseHas('ingredients', [
                'id' => $ingredient->id,
                'stock_in_grams' => $ingredient->default_stock_in_grams - $ingredient->pivot->quantity_in_grams,
            ]);
        });
    }

    public function testOrderCreationFailsForNonExistingProduct()
    {
        $response = $this->postJson('/api/orders', [
            'products' => [
                ['product_id' => 'non-existing-id', 'quantity' => 'invalid-quantity'],
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['products.0.product_id', 'products.0.quantity']);
    }

    public function testOrderCreationFailsForInsufficientStock()
    {
        $product = Product::first();
        // Update the stock of the first ingredient to a value less than the required amount
        $ingredient = $product->ingredients->first();
        $ingredient->stock_in_grams = $ingredient->pivot->quantity_in_grams - 1;
        $ingredient->save();

        $response = $this->postJson('/api/orders', [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ]
        ]);

        $response->assertStatus(400)
            ->assertJsonFragment([
                'success' => false,
            ])
            ->assertJsonStructure([
                'success',
                'error',
            ]);
    }

    public function testOrderCreationFailsForInvalidData()
    {
        $response = $this->postJson('/api/orders', [
            'products' => [
                ['product_id' => 'invalid-id', 'quantity' => 'invalid-quantity'],
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['products.0.product_id', 'products.0.quantity']);
    }

    public function testLowStockNotification()
    {
        Notification::fake();

        $product = Product::factory()->create();
        $ingredient = Ingredient::factory()->create(['stock_in_grams' => 150, 'default_stock_in_grams' => 200]);

        $product->ingredients()->attach($ingredient->id, ['quantity_in_grams' => 51]);


        $response = $this->postJson('/api/orders', [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ]
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Order placed successfully',
            ]);

        // Assert that a notification was sent
        Notification::assertSentOnDemand(IngredientLowStockNotification::class);

        // Assert the notification_sent flag is set to true
        $this->assertDatabaseHas('ingredients', [
            'id' => $ingredient->id,
            'restock_needed' => true,
            'notified_for_restock_sent' => true,
        ]);
    }

    public function testMultipleProductsOrder()
    {
        $product1 = Product::first();
        $product2 = Product::factory()->create();
        $ingredient1 = Ingredient::first();
        $ingredient2 = Ingredient::factory()->create(['stock_in_grams' => 5000, 'default_stock_in_grams' => 5000]);

        $product2->ingredients()->attach($ingredient2->id, ['quantity_in_grams' => 100]);

        $response = $this->postJson('/api/orders', [
            'products' => [
                ['product_id' => $product1->id, 'quantity' => 1],
                ['product_id' => $product2->id, 'quantity' => 2],
            ]
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Order placed successfully',
            ]);

        // Assert that an order with a UUID has been created
        $this->assertDatabaseHas('orders', [
            'id' => $response->json('order.id')
        ]);

        // Additional assertions to check related tables
        $this->assertDatabaseHas('ingredient_product', [
            'ingredient_id' => $ingredient1->id,
            'product_id' => $product1->id,
            'quantity_in_grams' => 150
        ]);

        $this->assertDatabaseHas('ingredient_product', [
            'ingredient_id' => $ingredient2->id,
            'product_id' => $product2->id,
            'quantity_in_grams' => 100
        ]);

        $this->assertDatabaseHas('ingredients', [
            'id' => $ingredient1->id,
            'stock_in_grams' => 19850
        ]);

        $this->assertDatabaseHas('ingredients', [
            'id' => $ingredient2->id,
            'stock_in_grams' => 4800
        ]);
    }
}
