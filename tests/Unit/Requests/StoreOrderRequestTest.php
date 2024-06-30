<?php

namespace Tests\Unit\Requests;

use Tests\TestCase;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class StoreOrderRequestTest extends TestCase
{
    public function testValidationPasses()
    {
        $request = new StoreOrderRequest();

        $product = Product::factory()->create();

        $data = [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ]
        ];

        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }

    public function testValidationFails()
    {
        $request = new StoreOrderRequest();

        $data = [
            'products' => [
                ['product_id' => null, 'quantity' => 0]
            ]
        ];

        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
    }
}
