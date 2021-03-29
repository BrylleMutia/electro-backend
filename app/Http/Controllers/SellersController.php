<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'unique:App\Models\User,email', 'email'],
            'password' => ['required'],
            'location' => ['required']
        ], [
            'required' => 'The :attribute field is required.',
            'unique' => 'The :attribute is already registered.'
        ]);

        if($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        

        $user = Seller::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'location' => $request->input('location'),
            'password' => Hash::make($request->input('password'))   // encrypt password
        ]);
        
        if ($user->save()) {
            $token = $this->login($request);
            return response()->json(["user" => $user, "token" => $token->original], 200);
            // return response()->json($user, 200);
        };
    }

    
    public function login(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required']
        ], [
            'required' => 'The :attribute field is required.',
        ]);

        if($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        

        $user = Seller::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'The provided credentials are invalid.']);
        }

        return response($user->createToken($user->name)->plainTextToken);
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
