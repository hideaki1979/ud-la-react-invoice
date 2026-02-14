<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::factory()->count(30)->create()->each(function (Order $order) {
            $productCount = rand(1, 3);
            $productIds = collect(range(1, 10))->random($productCount);

            foreach ($productIds as $productId) {
                $order->products()->attach($productId, [
                    'quantity' => rand(1, 20),
                ]);
            }
        });
    }
}
