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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $seller = Seller::with('products')->findOrFail($id);
        return $seller;
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
