<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SellersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sellers = Seller::all();
        return response()->json($sellers, 200);
    }

    /**
     * Register a new seller.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // MANUAL VALIDATION
        // $validator = Validator::make($request->all(), [
        //     'name' => ['required'],
        //     'email' => ['required', 'unique:App\Models\User,email', 'email'],
        //     'password' => ['required'],
        //     'location' => ['required']
        // ], [
        //     'required' => 'The :attribute field is required.',
        //     'unique' => 'The :attribute is already registered.'
        // ]);

        // if($validator->fails()) {
        //     return response()->json(['error' => $validator->errors()], 400);
        // }


        // include Accept-application/json header to work properly
        $fields = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'unique:App\Models\Seller,email', 'email'],
            'password' => ['required', 'string', 'confirmed'],
            'address' => [],
            'barangay' => ['string', 'required'],
            'city' => ['required', 'string'],
            'province' => ['required', 'string'],
            'zip_code' => ['required', 'string'],
            'role_id' => [],
            'phone' => ['required', 'string', 'min:11', 'max:13']
        ]);

        $user = Seller::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'address' => $fields['address'],
            'barangay' => $fields['barangay'],
            'city' => $fields['city'],
            'province' => $fields['province'],
            'zip_code' => $fields['zip_code'],
            'role_id' => 2,     // 1 = buyer, 2 = seller
            'phone' => $fields['phone'],
            'password' => Hash::make($fields['password'])   // encrypt password
        ]);

        // seller image
        if ($request->hasFile('image')) {
            $image_path = $request->file('image')->storeOnCloudinary('sellers')->getSecurePath();
            $user->image = $image_path;
        }

        if ($user->save()) {
            return response()->json($this->login($request)->original, 200);
        };
    }

    /**
     * Login to registered seller account
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // validation
        $fields = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = Seller::with('role')->where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json(['message' => 'The provided credentials are invalid.', 'errors' => ['error' => 'The provided credentials are invalid.']], 404);
        }

        return response()->json(["user" => $user, "token" => $user->createToken($user->name)->plainTextToken]);
    }

    /**
     * Get user details using token
     */
    public function verify()
    {
        return auth()->guard('seller')->user();
    }

    /**
     * Logout authenticated user / delete auth token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        auth()->guard('seller')->user()->tokens()->delete();

        return ['message' => 'Logged out'];
    }


    /**
     * Display the specified resource.
     * 
     * @access PUBLIC
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $seller = Seller::with('products')->findOrFail($id);
        return $seller;
    }


    /**
     * FOR SELLER DASHBOARD! --------------
     * Display detailed information related to specified resource.
     * 
     * @access PRIVATE
     * @return \Illuminate\Http\Response
     */
    public function products(Request $request)
    {
        $seller_products = auth()->guard('seller')->user()->products->load("orders.user", "orders.status", "categories");
        return $seller_products;
    }

    /**
     * Get orders for current seller
     * 
     * @access PRIVATE
     * @return \Illuminate\Http\Response
     */
    public function orders()
    {
        // filter order to only return products owned by current seller
        $orders = auth()->guard('seller')->user()->orders->load(['user', 'status', 'products' => function ($query) {
            $query->where('seller_id', auth()->guard('seller')->user()->id);
        }]);

        return response()->json($orders, 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $seller = Seller::findOrFail(auth()->guard('seller')->user()->id);

        // ** NEED VALIDATION HERE **
        $seller->update($request->all());

        // update seller image
        if ($request->hasFile('image')) {
            $image_path = $request->file('image')->storeOnCloudinary('sellers')->getSecurePath();
            $seller->image = $image_path;
        }

        if ($seller->save()) return response()->json($seller, 200);
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
