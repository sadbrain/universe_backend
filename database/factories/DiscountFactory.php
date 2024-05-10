<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DiscountFactory extends Factory
{
    protected $model = \App\Models\Discount::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'price' => $this->faker->numberBetween(1, 99), // Giá ngẫu nhiên, bạn có thể thay đổi phù hợp với nhu cầu
            'start_date' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d H:i:s'), // Định dạng ngày tháng để tránh lỗi SQL
            'end_date' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d H:i:s'), // Định dạng ngày tháng để tránh lỗi SQL
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'), // Định dạng ngày tháng để tránh lỗi SQL
            'updated_at' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'), // Định dạng ngày tháng để tránh lỗi SQL
        ];
    }
}
