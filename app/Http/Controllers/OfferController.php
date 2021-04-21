<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $offers = Offer::all();

        foreach($offers as $offer) {
            $offer['products'] = $offer->products;
        };

        return response()->json($offers, 200);
    }

    /**
     * Get all products within offer
     * 
     * @param string title
     */
    public function products($title) {
        $offer = Offer::where('offer_title', $title)->firstOrFail();

        return response()->json($offer->products, 200); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'offer_title' => ['required', 'unique:App\Models\Offer,offer_title']
        ]);

        $offer = Offer::create([
            'offer_title' => $fields['offer_title']
        ]);

        return response()->json($offer);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
