<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $limit = request()->query('limit');

        if($limit > 0) {
            // get products with a certain limit
            $products = Product::with('categories:id,name', 'offer', 'seller')->take($limit)->orderBy('created_at', 'desc')->get();
        } else {
            // get all
            $products = Product::with('categories:id,name', 'offer', 'seller')->orderBy('created_at', 'desc')->get();
        }


        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = Product::create($request->all());

        // // store all product images
        // $images_path = [];
        // foreach ($request->allFiles('product_image') as $product_image) {
        //     // for storing files on local/public directories
        //     // $image_path = $product_image->store('product_images');

        //     // upload image to cloudinary cloud storage
        //     // then get url to save to db
        //     $image_url = cloudinary()->upload($product_image->getRealPath())->getSecurePath();

        //     array_push($images_path, $image_url);
        // }
        
        // // $images_path = $request->file('product_image')->store('product_images');
        
        // $product->product_image = $images_path;

        if ($product->save()) {
            return response()->json($product, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::with('categories:id,name', 'offer', 'seller')->findOrFail($id);
        return response()->json($product, 200);
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
        $product = Product::findOrFail($id);
        $product->update($request->all());

        if ($product->save()) return response()->json(["status" => "updated", "product" => $product], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->delete($product)) return response()->json(["status" => "deleted", "product" => $product], 200);
    }

    /**
     * Search for a specific product name
     * 
     * @param string $name
     * @return \Illuminate\Http\Response
     */

    public function search($name)
    {
        $product = Product::where('product_name', 'like', '%' . $name . '%')->get();

        return response()->json($product, 200);
    }
}
