<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::all()->random();
        $product = Product::all()->random();

        return [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => $this->faker->numberBetween(1, 5),
            'feedback' => $this->faker->realText(100)
        ];
    }
}
