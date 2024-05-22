<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function test_anyone_can_to_see_products()
    {
        $this->withoutExceptionHandling();

        $res = $this->json('GET','api/products');

        $res->assertStatus(200);
    }

    /** @test */
    public function test_admin_can_stored_products()
    {
        $this->withoutExceptionHandling();

        $user = \App\Models\User::factory()->withAdminRole()->create();

        $this->actingAs($user);

        $data = [
            "name" => "Test Product",
            "price" => 10,
            "type" => "pizza"
        ];

        $res = $this->json('POST', 'api/admin/products', $data);

        $res->assertStatus(201);

        $this->assertDatabaseHas('products', [
            "name" => "Test Product",
            "price" => 10,
            "type" => "pizza"
        ]);
    }


    /** @test */
    public function test_show_product_returns_correct_data()
    {
        // Создаем продукт
        $product = Product::factory()->create([
            'name' => 'Test Product Pizza',
            'type' => 'Pizza',
            'price' => 99.99,
        ]);

        // Выполняем GET-запрос к маршруту show
        $res = $this->json('GET', "api/products/{$product->id}");

        // Проверяем статус ответа
        $res->assertStatus(200);

        // Проверяем структуру JSON
        $res->assertJson([
            'data' => [
                'id' => $product->id,
                'name' => 'Test Product Pizza',
                'type' => 'Pizza',
                'price' => '99.99',
            ],
        ]);
    }


    /** @test */
    public function test_show_product_returns_404_if_not_found()
    {
        // Выполняем GET-запрос к маршруту show с несуществующим ID
        $response = $this->getJson('/api/products/999999');

        // Проверяем статус ответа
        $response->assertStatus(404);
    }

}
