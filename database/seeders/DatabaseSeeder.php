<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
        ]);
        \App\Models\Company::factory(50)->create();
        \App\Models\User::factory(50)->create();
        \App\Models\Category::factory(5)->create();
        \App\Models\Discount::factory(50)->create();
        \App\Models\Inventory::factory(50)->create();
        \App\Models\ProductColor::factory(100)->create();
        \App\Models\ProductSize::factory(100)->create();
        \App\Models\Product::factory(50)->create();
        \App\Models\Order::factory(100)->create();
        \App\Models\OrderDetail::factory(200)->create();
        \App\Models\Payment::factory(100)->create();

    }
}
