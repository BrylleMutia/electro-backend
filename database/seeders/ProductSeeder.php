<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::factory()->count(5)->create();
        Product::factory()->count(10)->create();

        // manually implement relationships (for pivot table)
        $categories = Category::all();
        Product::all()->each(function ($product) use ($categories) {
            $product->categories()->attach(
                $categories->random(2)->pluck('id')->toArray()
            );
        });

        Review::factory()->count(50)->create();
    }
}
