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
        Order::factory(12)->create();
        Product::factory(22)->create();
        OrderProduct::factory(12)->create();
    }
}
