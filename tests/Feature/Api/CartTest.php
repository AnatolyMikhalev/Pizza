<?php

namespace Tests\Feature\Api;

use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CartTest extends TestCase
{

    //use RefreshDatabase;
    use DatabaseTransactions;

    /** @test */
    public function test_index_user_can_see_hisown_cart()
    {
        $this->withoutExceptionHandling();

        $user = \App\Models\User::find(5);

        $this->actingAs($user);

        $res = $this->get('api/cart');

        $res->assertStatus(200);

        $products = Cart::where('user_id', $user->id)->with('Products')->get();

        foreach ($products as $product) {
            //dd(gettype($order->products));
            $res->assertJsonFragment([
                'id' => $product->id,
                'user_id' => $product->user_id,
                'products' => $product->products->toArray(),
            ]);
        }
    }

    /** @test */
    public function test_index_user_can_see_only_hisown_cart()
    {
        $this->withoutExceptionHandling();

        // Создаём пользователей
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        //Наполняем корзины для пользователей
        $cart1 = Cart::factory()->create(['user_id' => $user1->id]);
        $cart2 = Cart::factory()->create(['user_id' => $user2->id]);

        //Создаём продукты и прикрепляем их к корзинам
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        //Создаём заказаные продукты в промежуточной таблице cart_products
        CartProduct::factory()->create(['cart_id' => $cart1->id, 'product_id' => $product1->id]);
        CartProduct::factory()->create(['cart_id' => $cart2->id, 'product_id' => $product2->id]);

        //Аутентификация пользователя1
        $this->actingAs($user1);

        $res = $this->get('api/cart');

        $res->assertStatus(200);

        $res->assertJsonCount(1);

        //Проверяем что пользователь видит ТОЛЬКО свои заказы
        $res->assertJsonFragment(['id' => $cart1->id]);
        $res->assertJsonMissing(['id' => $cart2->id]);
    }

    /** @test */
    public function test_index_unauthorized_user_can_not_see_cart_401_expected()
    {
        // Создаём пользователей
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        //Наполняем корзины для пользователей
        $cart1 = Cart::factory()->create(['user_id' => $user1->id]);
        $cart2 = Cart::factory()->create(['user_id' => $user2->id]);

        //Создаём продукты и прикрепляем их к корзинам
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        //Создаём заказаные продукты в промежуточной таблице cart_products
        CartProduct::factory()->create(['cart_id' => $cart1->id, 'product_id' => $product1->id]);
        CartProduct::factory()->create(['cart_id' => $cart2->id, 'product_id' => $product2->id]);

        $res = $this->json('GET', 'api/cart'); //Запрос проходит

        $res->assertStatus(401);
    }

    /** @test */
    public function test_store_cart_can_be_stored_by_auth_user()
    {
        $this->withoutExceptionHandling();

        $user = \App\Models\User::factory()->create();

        $this->actingAs($user);

        $data = [
            "products" => [
                [
                    "product_id" => 1,
                    "quantity" => 3
                ],
                [
                    "product_id" => 2,
                    "quantity" => 2
                ]
            ]
        ];

        $res = $this->json('POST', 'api/cart', $data);

        $res->assertStatus(201);

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id
        ]);

        foreach ($data['products'] as $product) {
            $this->assertDatabaseHas('cart_products', [
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
            ]);
        }
    }

    /** @test */
    public function test_store_cart_can_be_stored_by_only_auth_user_401_expected()
    {
        $data = [
            "products" => [
                [
                    "product_id" => 1,
                    "quantity" => 3
                ],
                [
                    "product_id" => 2,
                    "quantity" => 2
                ]
            ]
        ];

        $res = $this->json('POST', 'api/cart', $data);

        $res->assertStatus(401);
    }

    /** @test */
    public function test_store_invalid_data_422_expected()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user);

        $data = [
            "products" => [
                [
                    "product_id" => 1,
                    "quantity" => 0
                ],
                [
                    "product_id" => 2,
                    "quantity" => 0
                ]
            ]
        ];

        $res = $this->json('POST', 'api/cart', $data);

        $res->assertStatus(422);
        $res->assertJsonValidationErrors('products.0.quantity');
        $res->assertJsonValidationErrors('products.1.quantity');
    }


    /** @test */
    public function test_store_user_can_not_add_more_than_10_pizzas_or_20_beverages_422_expected()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user);

        $pizza = Product::factory()->create([
            'type' => 'Pizza',
        ]);
        $pizza2 = Product::factory()->create([
            'type' => 'Pizza',
        ]);
        $beverage = Product::factory()->create([
            'type' => 'Beverage',
        ]);

        $data = [
            "products" => [
                [
                    "product_id" => $pizza->id,
                    "quantity" => 4
                ],
                [
                    "product_id" => $pizza2->id,
                    "quantity" => 7
                ],
                [
                    "product_id" => $beverage->id,
                    "quantity" => 21
                ]
            ]
        ];

        $res = $this->json('POST', 'api/cart', $data);

        $res->assertStatus(422);

        $res->assertJsonFragment([
            'You cannot order more than 10 Pizza products.',
        ]);

        $res->assertJsonFragment([
            'You cannot order more than 20 Beverage products.',
        ]);
    }

    /** @test */
    public function test_destroy_user_can_delete_cart()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user);

        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $res = $this->json('DELETE','api/cart');

        $res->assertStatus(204);

        $this->assertDatabaseMissing('carts', ['id' => $cart->id]);
    }
}
