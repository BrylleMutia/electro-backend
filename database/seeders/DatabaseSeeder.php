<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Role;
use App\Models\Seller;
use App\Models\User;
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

        // NEW OFFERS
        $offers = ['Flash Sale', 'Featured', 'Top Rated', 'Popular'];
        foreach($offers as $offer) {
            $newOffer = Offer::create([
                'offer_title' => $offer
            ]);

            $newOffer->save();
        }

        // CREATE ROLES
        $roles = ['buyer', 'seller'];
        foreach($roles as $role) {
            $newRole = Role::create([
                'role' => $role
            ]);

            $newRole->save();
        }


        // CREATE SELLERS
        $brands = ['Apple', 'Samsung', 'Dell', 'HP', 'Canon', 'Epson'];
        $logos = [
            "https://res.cloudinary.com/bryllemutia/image/upload/v1620286005/sellers/py3xjjhcibe7ldyplesf.jpg", // apple
            "https://res.cloudinary.com/bryllemutia/image/upload/v1620286062/sellers/c9xqbh9y1n51jbjbulal.jpg", // samsung
            "https://res.cloudinary.com/bryllemutia/image/upload/v1620286090/sellers/w9cmxj5gmep21wi1yt0h.jpg", // dell
            "https://res.cloudinary.com/bryllemutia/image/upload/v1620286129/sellers/cxohcsjanphl2vrv5ugg.jpg", // hp
            "https://res.cloudinary.com/bryllemutia/image/upload/v1620286152/sellers/p7eszfk4pgozgpfkz0mz.jpg", // canon
            "https://res.cloudinary.com/bryllemutia/image/upload/v1620286178/sellers/jn4vylzebgegzj4lnn3d.jpg", // epson
        ];

        foreach($brands as $index=>$brand) {
            $seller = Seller::factory()->make();
            $seller->name = $brand;
            $seller->image = $logos[$index];
            $seller->save();
        }

        

        User::factory()->count(5)->create();

        Category::factory()->count(5)->create();
        Product::factory()->count(10)->create();

        // manually implement relationships (for pivot table)
        $categories = Category::all();
        Product::all()->each(function ($product) use ($categories) {
            $product->categories()->attach(
                $categories->random(2)->pluck('id')->toArray()
            );
        });


    }
}
