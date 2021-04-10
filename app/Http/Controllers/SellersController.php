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
            'location' => ['required']
        ]);

        $user = Seller::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'location' => $fields['location'],
            'password' => Hash::make($fields['password'])   // encrypt password
        ]);

        if ($user->save()) {
            $token = $this->login($request);
            return response()->json(["user" => $user, "token" => $token->original], 200);
            // return response()->json($user, 200);
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

        $user = Seller::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'The provided credentials are invalid.']);
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
        $seller = Seller::findOrFail($id);
        $seller['products'] = $seller->products;
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
