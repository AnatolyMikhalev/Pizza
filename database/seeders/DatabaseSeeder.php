<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(2)->withAdminRole()->create();
        User::factory(7)->create();
        Product::factory(22)->withPizzaType()->create();

//        Order::factory(12)
//            ->hasAttached(
//                Product::factory()->count(5),
//                ['quantity' => rand(1,3)]
//            )
//            ->create();
//
        Order::factory(12)->create()->each(function ($order) {
            $products = Product::factory()->count(5)->create();
            foreach ($products as $product) {
                $order->products()->attach($product->id, ['quantity' => rand(1, 3)]);
            }
        });


        //OrderProduct::factory(12)->create();
    }
}
