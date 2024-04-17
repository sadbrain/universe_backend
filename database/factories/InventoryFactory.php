<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryFactory extends Factory
{
    protected $model = \App\Models\Inventory::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'quantity' => $this->faker->numberBetween(1, 100), // Số lượng tồn ngẫu nhiên, bạn có thể thay đổi phù hợp với nhu cầu
            'quantity_sold' => $this->faker->numberBetween(0, 50),
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'), // Thời gian tạo ngẫu nhiên trong 2 năm qua đến hiện tại
            'updated_at' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'), // Số lượng đã bán ngẫu nhiên, bạn có thể thay đổi phù hợp với nhu cầu
        ];
    }
}
