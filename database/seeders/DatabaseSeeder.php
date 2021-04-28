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

        Seller::factory()->count(5)->create();
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
