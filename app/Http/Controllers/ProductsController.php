<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

        if ($limit > 0) {
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
        // try {
        // $user = auth()->guard("seller")->user();
        // if (!$user) throw ValidationException::withMessages(["Unauthorized access"]);

        $fields = $request->validate([
            'product_name' => ['required', 'string', 'min:4', 'max:40'],
            'price' => ['required', 'numeric'],
            'description' => ['required', 'string'],
            'product_image' => ['required', 'image'],
            'seller_id' => ['required', 'numeric', "exists:sellers,id"],
            'offer_id' => ['exists:offers,id', 'nullable'],
            'categories' => ['required'],
            'categories.*' => ['required', 'exists:categories,id', 'numeric'],
        ]);

        // single image upload
        $images_path = $request->file('product_image')->storeOnCloudinary('products')->getSecurePath();

        $product = Product::create([
            'product_name' => $fields["product_name"],
            'price' => $fields["price"],
            'description' => $fields["description"],
            'slug' => Str::slug($fields['product_name']),
            'product_image' => $images_path,
            'seller_id' => $fields['seller_id'],
            'offer_id' => $fields['offer_id'],
        ]);

        // convert all array of id to integer first then attach to pivot
        $product->categories()->attach(array_map('intval', json_decode($fields["categories"])));

        // // multiple image upload
        // $images_path = [];
        // foreach ($request->allFiles('product_image') as $product_image) {
        //     // upload image to cloudinary cloud storage
        //     // then get url to save to db
        //     $image_url = cloudinary()->upload($product_image->getRealPath())->getSecurePath();
        //     array_push($images_path, $image_url);
        // }


        if ($product->save()) {
            return response()->json($product->load('categories:id,name', 'offer', 'seller'), 200);
        }
        // } catch (\Exception $e) {
        //     return response()->json(['message' => $e->getMessage()], 500);
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::with('categories:id,name', 'offer', 'seller', 'reviews.user')->findOrFail($id);
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

    /**
     * Add new product review
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function review(Request $request)
    {
        $fields = $request->validate([
            'product_id' => ['required', "numeric"],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'feedback' => ['required', 'string']
        ]);

        $review = Review::with("user")->updateOrCreate([
            'user_id' => auth()->user()->id,
            'product_id' => $fields['product_id'],
        ], [
            'rating' => $fields['rating'],
            'feedback' => $fields['feedback']
        ]);

        if ($review->save()) {
            $updated_reviews = Product::findOrFail($fields['product_id'])->reviews->load("user");
            return response()->json($updated_reviews, 200);
        }
    }
}
