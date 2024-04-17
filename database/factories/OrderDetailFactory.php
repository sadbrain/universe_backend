<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderDetailFactory extends Factory
{
    protected $model = \App\Models\OrderDetail::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'color' => $this->faker->colorName,
            'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL']),
            'quantity' => $this->faker->numberBetween(1, 20),
            'price' => $this->faker->randomFloat(2, 10, 100),
            'product_id' => $this->faker->numberBetween(1, 20),
            'order_id' => $this->faker->numberBetween(1, 100),
        ];
    }
}
