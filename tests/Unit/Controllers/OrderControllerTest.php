<?php

namespace Tests\Unit\Controllers;

use App\Models\Order;
use App\Models\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Mockery;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testStoreOrder()
    {
        // Mock the OrderService
        $orderServiceMock = Mockery::mock(OrderService::class);
        $orderServiceMock->shouldReceive('processOrder')->once()->andReturn(Order::factory()->create());

        // Bind the mock to the container
        $this->app->instance(OrderService::class, $orderServiceMock);

        $product = Product::first();

        $requestData = [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ]
        ];

        // Simulate the POST request
        $response = $this->postJson('/api/orders', $requestData);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'Order placed successfully',
            'order' => ['id' => $response->json('order.id')]
        ]);
    }

    public function testStoreOrderFails()
    {
        // Mock the OrderService to throw an exception
        $orderServiceMock = Mockery::mock(OrderService::class);
        $orderServiceMock->shouldReceive('processOrder')->andThrow(new \Exception('Order processing failed'));

        // Bind the mock to the container
        $this->app->instance(OrderService::class, $orderServiceMock);

        $requestData = [
            'products' => [
                ['product_id' => 'uuid', 'quantity' => 1]
            ]
        ];

        // Simulate the POST request
        $response = $this->postJson('/api/orders', $requestData);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'products.0.product_id'
            ]
        ]);
    }
}
