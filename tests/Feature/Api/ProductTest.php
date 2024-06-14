<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
//use Illuminate\Support\Facades\File;
//use phpDocumentor\Reflection\File;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function test_index_anyone_can_to_see_products()
    {
        $this->withoutExceptionHandling();

        $res = $this->json('GET','api/products');

        $res->assertStatus(200);
    }

    /** @test */
    public function test_store_admin_can_store_products()
    {
        $this->withoutExceptionHandling();

        $user = \App\Models\User::factory()->withAdminRole()->create();

        $this->actingAs($user);

        $data = [
            "name" => "Test Product",
            "price" => 10,
            "type" => "pizza",
        ];

        $res = $this->json('POST', 'api/admin/products', $data);

        $res->assertStatus(201);

        $this->assertDatabaseHas('products', [
            "name" => "Test Product",
            "price" => 10,
            "type" => "pizza",
        ]);
    }


    /** @test */
    public function test_store_admin_can_store_products_with_image()
    {
        $this->withoutExceptionHandling();

        Storage::fake('public');

        $file = File::create('my_image.jpg');

        $user = \App\Models\User::factory()->withAdminRole()->create();

        $this->actingAs($user);

        $data = [
            "name" => "Test Product",
            "price" => 10,
            "type" => "pizza",
            'image' => $file,
        ];

        $res = $this->json('POST', 'api/admin/products', $data);

        $res->assertStatus(201);

        dump($file->hashName());

        $this->assertDatabaseHas('products', [
            "name" => "Test Product",
            "price" => 10,
            "type" => "pizza",
            'image_url' => 'images/' . $file->hashName(),
        ]);
    }

    /** @test */
    public function test_store_user_can_not_store_products_401_expected()
    {
        $this->withoutExceptionHandling();

        $user = \App\Models\User::factory()->create();

        $this->actingAs($user);

        $data = [
            "name" => "Test Product",
            "price" => 10,
            "type" => "pizza"
        ];

        $res = $this->json('POST', 'api/admin/products', $data);

        $res->assertStatus(401);

        $this->assertDatabaseMissing('products', [
            "name" => "Test Product",
            "price" => 10,
            "type" => "pizza"
        ]);
    }

    /** @test */
    public function test_show_product_returns_correct_data()
    {
        $product = Product::factory()->create([
            'name' => 'Test Product Pizza',
            'type' => 'Pizza',
            'price' => 99.99,
        ]);

        $res = $this->json('GET', "api/products/{$product->id}");

        $res->assertStatus(200);

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
    public function test_show_product_returns_404_expected()
    {
        $response = $this->getJson('/api/products/99999999');

        $response->assertStatus(404);
    }

    public function test_update_admin_can_update_product()
    {
        Storage::fake('public');

        $file = File::create('my_image.jpg');

        $user = \App\Models\User::factory()->withAdminRole()->create();

        $this->actingAs($user);

        $product = Product::factory()->create();


        $data = [
            'name' => 'Test Product Pizza',
            'price' => 2222,
            'image' => $file,
        ];

        $res = $this->json('PUT','api/admin/products/' . $product->id, $data);

        $res->assertStatus(200);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Test Product Pizza',
            'price' => 2222,
            'image_url' => 'images/' . $file->hashName(),
        ]);
    }

    public function test_update_user_can_not_update_product_401_expected()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user);

        $product = Product::factory()->create();

        $data = [
            'name' => 'Test Product Pizza',
            'price' => 2222,
        ];

        $res = $this->json('PUT','api/admin/products/' . $product->id, $data);

        $res->assertStatus(401);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
            'name' => 'Test Product Pizza',
            'price' => 2222,
        ]);
    }

    public function test_destroy_admin_can_delete_product()
    {
        $user = \App\Models\User::factory()->withAdminRole()->create();

        $this->actingAs($user);

        $product = Product::factory()->create();

        $res = $this->json('DELETE','api/admin/products/' . $product->id);

        $res->assertStatus(204);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
    public function test_destroy_user_can_not_delete_product()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user);

        $product = Product::factory()->create();

        $res = $this->json('DELETE','api/admin/products/' . $product->id);

        $res->assertStatus(401);

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

}
