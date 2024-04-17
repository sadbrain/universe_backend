<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
class ProductFactory extends Factory
{
    protected $model = \App\Models\Product::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */ 
    public function definition()
    {
        $name = $this->faker->sentence(3); // Tạo tên ngẫu nhiên
        $slug = Str::slug($name); // Tạo slug từ tên

        return [
            'name' => $name,
            'slug' => $slug,
            'thumbnail' => $this->faker->imageUrl(), // Ảnh ngẫu nhiên, bạn có thể thay đổi phù hợp với nhu cầu
            'description' => $this->faker->paragraph(), // Mô tả ngẫu nhiên
            'price' => $this->faker->randomFloat(2, 10, 1000), // Giá ngẫu nhiên
            'rating' => $this->faker->randomFloat(1, 0, 5), // Đánh giá ngẫu nhiên
            'favorites' => $this->faker->numberBetween(0, 1000), // Số lượt yêu thích ngẫu nhiên
            'category_id' => $this->faker->numberBetween(1, 5), // ID danh mục ngẫu nhiên từ 1 đến 50
            'inventory_id' => $this->faker->numberBetween(1, 50), // ID inventory ngẫu nhiên từ 1 đến 50
            'discount_id' => $this->faker->numberBetween(1, 50),
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'), // Thời gian tạo ngẫu nhiên trong 2 năm qua đến hiện tại
            'updated_at' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'), // ID discount ngẫu nhiên từ 1 đến 50
        ];
    }
}
