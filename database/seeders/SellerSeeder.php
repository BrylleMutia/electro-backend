<?php

namespace Database\Seeders;

use App\Models\Seller;
use Illuminate\Database\Seeder;

class SellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /// CREATE SELLERS
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
    }
}
