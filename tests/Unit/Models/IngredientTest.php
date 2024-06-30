<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Ingredient;
use App\Models\Product;
use App\Notifications\IngredientLowStockNotification;
use Illuminate\Support\Facades\Notification;

class IngredientTest extends TestCase
{
    use RefreshDatabase;

    public function testIngredientProductRelationship()
    {
        $ingredient = Ingredient::factory()->create();
        $product = Product::factory()->create();

        $ingredient->products()->attach($product->id, ['quantity_in_grams' => 100]);

        $this->assertInstanceOf(Product::class, $ingredient->products->first());
        $this->assertEquals(100, $ingredient->products->first()->pivot->quantity_in_grams);
    }

    public function testCheckAndNotifyLowStock()
    {
        // Fake the notification
        Notification::fake();

        $ingredient = Ingredient::factory()->create(['stock_in_grams' => 50, 'default_stock_in_grams' => 200]);

        $ingredient->checkAndNotifyLowStock();

        $this->assertTrue($ingredient->restock_needed);
        $this->assertTrue($ingredient->notified_for_restock_sent);
        Notification::assertSentOnDemand(IngredientLowStockNotification::class);
    }
}
