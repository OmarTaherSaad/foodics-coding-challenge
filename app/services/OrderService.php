<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\OrderProcessingException;
use App\Models\Ingredient;

class OrderService
{
    /**
     * Processes an order and updates stock levels.
     *
     * @param array $orderData
     * @return Order
     * @throws OrderProcessingException
     */
    public function processOrder(array $orderData): Order
    {
        try {
            return DB::transaction(function () use ($orderData) {
                // Create a new order
                $order = Order::create();

                // Attach products to the order and update ingredient stock
                foreach ($orderData['products'] as $productOrder) {
                    $this->attachProductToOrder($productOrder, $order);
                }

                return $order;
            });
        } catch (ProductNotFoundException | InsufficientStockException $e) {
            // Re-throw specific exceptions as a general order processing exception
            throw new OrderProcessingException($e->getMessage());
        } catch (\Exception $e) {
            // Handle unexpected exceptions
            throw new OrderProcessingException("An unexpected error occurred.");
        }
    }

    /**
     * Attaches a product to an order and updates ingredient stock levels.
     *
     * @param array $productOrder
     * @param Order $order
     * @throws ProductNotFoundException
     * @throws InsufficientStockException
     */
    protected function attachProductToOrder(array $productOrder, Order $order): void
    {
        // Find the product and its ingredients
        $product = Product::with('ingredients')->find($productOrder['product_id']);

        if (!$product) {
            throw new ProductNotFoundException($productOrder['product_id']);
        }

        // Attach the product to the order with the specified quantity
        $order->products()->attach($product->id, ['quantity' => $productOrder['quantity']]);

        // Update the stock levels of the ingredients used in the product
        foreach ($product->ingredients as $ingredient) {
            $this->updateIngredientStock($ingredient, $productOrder['quantity']);
        }
    }

    /**
     * Updates the stock level of an ingredient and sends a notification if necessary.
     *
     * @param $ingredient
     * @param int $quantity
     * @throws InsufficientStockException
     */
    protected function updateIngredientStock(Ingredient $ingredient, int $quantity): void
    {
        // Calculate the required amount of the ingredient
        $requiredAmount = $ingredient->pivot->quantity_in_grams * $quantity;

        if ($ingredient->stock_in_grams < $requiredAmount) {
            throw new InsufficientStockException($ingredient->name);
        }

        // Decrement the stock by the required amount
        $ingredient->decrement('stock_in_grams', $requiredAmount);

        // Check and send low stock notification
        $ingredient->checkAndNotifyLowStock();
    }
}
