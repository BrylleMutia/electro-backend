<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Seller::factory()->count(5)->create();
        Product::factory()->count(10)->create();
        Category::factory()->count(5)->create();
        Offer::factory()->count(4)->create();

        // manually implement relationships 
        $sellers = Seller::all();
        $categories = Category::all();
        $offers = Offer::all();
        Product::all()->each(function ($product) use ($sellers, $categories, $offers) {
            $product->seller()->attach(
                $sellers->random(1)->pluck('id')
            );
            $product->categories()->attach(
                $categories->random(2)->pluck('id')->toArray()
            );
            $product->offer()->attach(
                $offers->random(1)->pluck('id')
            );
        });
    }
}
