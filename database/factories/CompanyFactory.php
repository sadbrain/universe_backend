<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = \App\Models\Company::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company, // Tên công ty ngẫu nhiên
            'phone_number' => $this->faker->phoneNumber, // Số điện thoại ngẫu nhiên
            'street_address' => $this->faker->streetAddress, // Địa chỉ ngẫu nhiên
            'district_address' => $this->faker->citySuffix, // Quận/Huyện ngẫu nhiên
            'city' => $this->faker->city, 
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'), // Thời gian tạo ngẫu nhiên trong 2 năm qua đến hiện tại
            'updated_at' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'),// Thành phố ngẫu nhiên
        ];
    }
}
