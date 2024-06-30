<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use App\Exceptions\OrderProcessingException;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Handles the creation of a new order.
     *
     * @param StoreOrderRequest $request
     * @return JsonResponse
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            // Process the order and get the created order instance
            $order = $this->orderService->processOrder($request->validated());

            // Return a success response with the created order and success flag
            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order' => new OrderResource($order)
            ], 201);
        } catch (OrderProcessingException $e) {
            // Return an error response with success flag set to false if order processing fails
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
