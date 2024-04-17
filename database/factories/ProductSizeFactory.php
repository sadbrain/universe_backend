<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductSizeFactory extends Factory
{
    protected $model = \App\Models\ProductSize::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['S', 'M', 'L', 'XL', 'XXL', "34", "35", "36","37","38","39","40"]), // Kích thước ngẫu nhiên
            'quantity' => $this->faker->numberBetween(0, 100), // Số lượng ngẫu nhiên
            'inventory_id' => $this->faker->numberBetween(1, 50),
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'), // Thời gian tạo ngẫu nhiên trong 2 năm qua đến hiện tại
            'updated_at' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'), // ID inventory ngẫu nhiên từ 1 đến 50
        ];
    }
}
