<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Carbon\Carbon;
class OrderFactory extends Factory
{
    
    

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $statusOptions = ['Approved', 'Processing', 'Shipped', 'Cancelled', 'Refunded'];
        $status = Arr::random($statusOptions);

        $startDate = Carbon::now()->subYear()->startOfMonth()->setMonth(10); // Ngày bắt đầu từ 1 tháng 10 năm trước
        $endDate = Carbon::now(); // Ngày kết thúc là ngày hiện tại
        return [
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
            'street_address' => $this->faker->streetAddress,
            'district_address' => $this->faker->citySuffix,
            'city' => $this->faker->city,
            'order_date' => $this->faker->dateTimeBetween($startDate, $endDate),
            'order_status' => "Shipped",
            'order_total' => $this->faker->randomFloat(2, 10, 1000),
            'shipping_date' => $this->faker->dateTimeThisMonth(),
            'tracking_number' => $this->faker->randomNumber(6),
            'carrier' => $this->faker->company, 
            'user_id' => $this->faker->numberBetween(1, 15),
        ];
    }
}
