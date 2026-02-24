<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SalesFlowTest extends TestCase
{
    // use RefreshDatabase; // Commented out to avoid wiping existing DB if using persistent one. 
    // Ideally use a separate testing DB. For now, we will assume DB is usable or use standard DB transactions.
    // Given the environment, I'll avoid RefreshDatabase to be safe and just create data.

    public function test_pos_page_loads()
    {
        $user = User::factory()->create();
        $user->assignRole('admin'); // Ensure admin access

        $response = $this->actingAs($user)->get(route('pos.index'));

        $response->assertStatus(200);
        $response->assertViewIs('pos.index');
    }

    public function test_can_create_sale()
    {
        $user = User::factory()->create();
        $client = Client::create([
            'name' => 'Test Client',
            'email' => 'testclient@example.com' . rand(1, 1000),
            'document_type' => 'DNI',
            'document_number' => '12345678',
            'address' => '123 Test St'
        ]);

        // Ensure we have a product with stock
        $product = Product::first();
        if (!$product) {
            $product = Product::create([
                'name' => 'Test Product',
                'code' => 'TEST001',
                'price' => 100,
                'stock' => 50,
                'status' => true,
                'measurement_unit_id' => 1
            ]);
        }
        $initialStock = $product->stock;

        // Ensure payment method
        $method = PaymentMethod::first();

        $cart = [
            [
                'id' => $product->id,
                'quantity' => 2,
                'price' => 100 // Matching product price
            ]
        ];

        $payments = [
            [
                'method_id' => $method->id,
                'amount' => 200 // 2 * 100
            ]
        ];

        $response = $this->actingAs($user)->postJson(route('pos.store'), [
            'cart' => $cart,
            'client_id' => $client->id,
            'payments' => $payments
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Venta registrada correctamente'
            ]);

        // Verify Database
        $this->assertDatabaseHas('sales', [
            'client_id' => $client->id,
            'total' => 200
        ]);

        $this->assertDatabaseHas('sale_details', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        // Verify Stock Deduction
        $product->refresh();
        $this->assertEquals($initialStock - 2, $product->stock);
    }
}
