<?php

namespace Tests\Feature\Api;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrderTest extends TestCase
{

    //use RefreshDatabase;
    use DatabaseTransactions;

    /**
     * Указывает, следует ли запускать наполнитель по умолчанию перед каждым тестом.
     *
     * @var bool
     */
    //protected bool $seed = true;

    /** @test */
    public function test_index_user_can_see_hisown_orders()
    {
        $this->withoutExceptionHandling();

        $user = \App\Models\User::find(5);

        $this->actingAs($user);

        $res = $this->get('api/orders');

        $res->assertStatus(200);

        $orders = Order::where('user_id', $user->id)->with('Products')->get();

        foreach ($orders as $order) {
            //dd(gettype($order->products));
            $res->assertJsonFragment([
                'id' => $order->id,
                'user_id' => $order->user_id,
                'address' => $order->address,
                'delivered' => $order->delivered,
                'created_at' => $order->created_at,
                'products' => $order->products->toArray(),
            ]);
        }
    }

    /** @test */
    public function test_index_user_can_see_only_hisown_orders()
    {
        $this->withoutExceptionHandling();

        // Создаём пользователей
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        //Создаём заказы для пользователей
        $order1 = Order::factory()->create(['user_id' => $user1->id]);
        $order2 = Order::factory()->create(['user_id' => $user2->id]);

        //Создаём продукты и прикрепляем их к заказам
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        //Создаём заказаные продукты в промежуточной таблице order_products
        OrderProduct::factory()->create(['order_id' => $order1->id, 'product_id' => $product1->id]);
        OrderProduct::factory()->create(['order_id' => $order2->id, 'product_id' => $product2->id]);

        //Аутентификация пользователя1
        $this->actingAs($user1);

        $res = $this->get('api/orders');

        $res->assertStatus(200);

        $res->assertJsonCount(1);

        //Проверяем что пользователь видит ТОЛЬКО свои заказы
        $res->assertJsonFragment(['id' => $order1->id]);
        $res->assertJsonMissing(['id' => $order2->id]);
    }

    /** @test */
    public function test_index_unauthorized_user_can_not_see_orders_401_expected()
    {
        // Создаём пользователей
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        //Создаём заказы для пользователей
        $order1 = Order::factory()->create(['user_id' => $user1->id]);
        $order2 = Order::factory()->create(['user_id' => $user2->id]);

        //Создаём продукты и прикрепляем их к заказам
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        //Создаём заказаные продукты в промежуточной таблице order_products
        OrderProduct::factory()->create(['order_id' => $order1->id, 'product_id' => $product1->id]);
        OrderProduct::factory()->create(['order_id' => $order2->id, 'product_id' => $product2->id]);

        $res = $this->json('GET', 'api/orders'); //Запрос проходит

        $res->assertStatus(401);
    }

    /** @test */
    public function test_index_only_admin_can_see_all_orders_401_expected()
    {
        //$this->withoutExceptionHandling();

        $user = \App\Models\User::factory()->create();

        $this->actingAs($user);

        $res = $this->get('api/admin/orders');

        $res->assertStatus(401);

        $orders = Order::all()->toArray();

        // dd($orders);

//        foreach ($orders as $order) {
//            $res->assertJsonFragment([
//                'id' => $order->id,
//                'user_id' => $order->user_id,
//                'address' => $order->address,
//                'delivered' => $order->delivered,
//                'created_at' => $order->created_at->toJSON(),
//                'products' => $order->products->map(function ($product) use ($order) {
//                    return [
//                        'id' => $product->id,
//                        'name' => $product->name,
//                        'type' => $product->type,
//                        'price' => $product->price,
//                        'created_at' => $product->created_at->toJSON(),
//                        'updated_at' => $product->updated_at->toJSON(),
//                        'pivot' => [
//                            'order_id' => $order->id,
//                            'product_id' => $product->id,
//                        ],
//                    ];
//                })->toArray(),
//            ]);
//        }
    }

    /** @test */
    public function test_store_order_can_be_stored_by_auth_user()
    {
        $this->withoutExceptionHandling();

        $user = \App\Models\User::factory()->create();

        $this->actingAs($user);

        $data = [
            "address" => "111 Main St",
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

        $res = $this->json('POST', 'api/orders', $data);

        $res->assertStatus(201);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'address' => '111 Main St'
        ]);
    }

    /** @test */
    public function test_store_order_can_be_stored_by_only_auth_user_401_expected()
    {
        $data = [
            "address" => "222 Main St",
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

        $res = $this->json('POST', 'api/orders', $data);

        $res->assertStatus(401);
    }

    /** @test */
    public function test_store_invalid_data_422_expected()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user);

        $data = [
            "address" => "",
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

        $res = $this->json('POST', 'api/orders', $data);

        $res->assertStatus(422);
        $res->assertJsonValidationErrors('address');
        $res->assertJsonValidationErrors('products.0.quantity');
        $res->assertJsonValidationErrors('products.1.quantity');
    }

    /** @test */
    public function test_show_user_can_see_hisown_order()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user);

        $order = Order::factory()->create(['user_id' => $user->id]);

        $res = $this->json('GET','api/orders/' . $order->id);

        $res->assertStatus(200);
    }

    /** @test */
    public function test_show_user_can_not_see_stranger_order_403_expected()
    {
        $user1 = \App\Models\User::factory()->create();
        $user2 = \App\Models\User::factory()->create();

        $this->actingAs($user1);

        $order2 = Order::factory()->create(['user_id' => $user2->id]);

        $res = $this->json('GET','api/orders/' . $order2->id);


        $res->assertStatus(403);
    }

    /** @test */
    public function test_update_admin_can_change_status()
    {
        $user = \App\Models\User::factory()->withAdminRole()->create();

        $this->actingAs($user);

        $order = Order::factory()->create();

        $data = [
            'delivered' => 1,
        ];

        $res = $this->json('PUT','api/admin/orders/' . $order->id, $data);

        $res->assertStatus(200);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'delivered' => 1,
        ]);
    }

    /** @test */
    public function test_update_user_can_not_change_status_403_expected()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user);

        $order = Order::factory()->create();

        $data = [
            'delivered' => 1,
        ];

        $res = $this->json('PUT','api/admin/orders/' . $order->id, $data);

        $res->assertStatus(401);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'delivered' => 0,
        ]);
    }

    /** @test */
    public function test_destroy_admin_can_delete_order()
    {
        $user = \App\Models\User::factory()->withAdminRole()->create();

        $this->actingAs($user);

        $order = Order::factory()->create();

        $res = $this->json('DELETE','api/admin/orders/' . $order->id);

        $res->assertStatus(204);

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    /** @test */
    public function test_destroy_user_can_not_delete_order_403_expected()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user);

        $order = Order::factory()->create();

        dump($order->toArray());

        $res = $this->json('DELETE','api/admin/orders/' . $order->id);

        $res->assertStatus(401);

        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }
}
