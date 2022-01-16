<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'picture'=> $this->faker->imageUrl($width = 640, $height = 480, 'technics'),
            'price' => $this->faker->randomFloat($nbMaxDecimals = 2, $min = 20, $max = 10000),
            'status' => $this->faker->randomElement($array = array ('active','inactive')),
        ];
    }
}
