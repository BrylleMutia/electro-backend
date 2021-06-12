<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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
        // for implementation of relationships
        $seller = Seller::all()->random();
        $offer = Offer::all()->random();

        // for product name, combine random company name with product as suffix
        $productSuffixes = ['Phone', 'Laptop', 'Speakers', 'Tablet', 'Headphones', 'TV'];
        $name = $this->faker->company . ' ' . Arr::random($productSuffixes);

        $image_urls = [
            "https://res.cloudinary.com/bryllemutia/image/upload/v1619247909/goocvsyf9t88phyl5uqc.jpg",
            "https://res.cloudinary.com/bryllemutia/image/upload/v1619247961/f1sgjipnaaklr7wkimwh.jpg",
            "https://res.cloudinary.com/bryllemutia/image/upload/v1619247992/asrdjk0zljwkyiosulgx.jpg",
            "https://res.cloudinary.com/bryllemutia/image/upload/v1619248012/ts70dsrtd8ptm0agwbc5.png",
            "https://res.cloudinary.com/bryllemutia/image/upload/v1619248028/twk3hvjro29xmgelp6wu.jpg",
            "https://res.cloudinary.com/bryllemutia/image/upload/v1619248045/mt806opxjoyuorfh3ui4.webp"    
        ];

        return [
             'product_name' => $name,
             'slug' => Str::slug($name),
             'description' => $this->faker->realText(300),
             'price' => $this->faker->numberBetween(200, 10000),
             'product_image' => Arr::random($image_urls),
             'seller_id' => $seller->id,
             'offer_id' => $offer->id
        ];
    }
}
