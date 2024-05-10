<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = \App\Models\Payment::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'payment_status' => $this->faker->randomElement(['Approved', 'Pending', 'ApprovedForDelayedPayment', 'Rejected']),
            'session_id' => $this->faker->uuid,
            'payment_intent_id' => $this->faker->uuid,
            'payment_date' => $this->faker->dateTimeThisYear(),
            'payment_due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'user_id' => $this->faker->numberBetween(1, 15),
            'order_id' => $this->faker->numberBetween(1, 100),
        ];
    }
}
