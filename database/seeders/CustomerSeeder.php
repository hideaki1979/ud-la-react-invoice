<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        Customer::factory()
            ->count(3)
            ->sequence(
                [
                    'name' => 'A商店',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'B商店',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'C商店',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            )->create();
    }
}
