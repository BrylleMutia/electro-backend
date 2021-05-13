<?php

namespace Database\Seeders;

use App\Models\Offer;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /// NEW OFFERS
        $offers = ['Flash Sale', 'Featured', 'Top Rated', 'Popular'];
        foreach($offers as $offer) {
            $newOffer = Offer::create([
                'offer_title' => $offer
            ]);

            $newOffer->save();
        }

    }
}
