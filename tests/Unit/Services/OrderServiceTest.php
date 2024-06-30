<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Order;
use App\Models\Product;
use App\Models\Ingredient;
use App\Services\OrderService;
use App\Exceptions\OrderProcessingException;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = $this->app->make(OrderService::class);
    }

    public function testProcessOrder()
    {
        $product = Product::factory()->create();
        $ingredient = Ingredient::factory()->create(['stock_in_grams' => 20000, 'default_stock_in_grams' => 20000]);
        $product->ingredients()->attach($ingredient->id, ['quantity_in_grams' => 150]);

        $orderData = [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ]
        ];

        $order = $this->orderService->processOrder($orderData);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertDatabaseHas('orders', ['id' => $order->id]);
        $this->assertDatabaseHas('ingredient_product', ['ingredient_id' => $ingredient->id, 'product_id' => $product->id, 'quantity_in_grams' => 150]);
        $this->assertDatabaseHas('ingredients', ['id' => $ingredient->id, 'stock_in_grams' => 19850]);
    }

    public function testProcessOrderThrowsProductNotFoundException()
    {
        $this->expectException(OrderProcessingException::class);
        $this->expectExceptionMessageMatches('/Product not found/');

        $orderData = [
            'products' => [
                ['product_id' => 'non-existing-id', 'quantity' => 1],
            ]
        ];

        $this->orderService->processOrder($orderData);
    }

    public function testProcessOrderThrowsInsufficientStockException()
    {
        $product = Product::factory()->create();
        $ingredient = Ingredient::factory()->create(['stock_in_grams' => 100, 'default_stock_in_grams' => 100]);
        $product->ingredients()->attach($ingredient->id, ['quantity_in_grams' => 150]);

        $orderData = [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ]
        ];

        $this->expectException(OrderProcessingException::class);
        $this->expectExceptionMessageMatches('/Insufficient stock/');

        $this->orderService->processOrder($orderData);
    }
}
