<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
    }

    public function test(Request $request)
    {
        return response()->json(["message" => $request->all()]);
    }

    /**
     * Register a new user.
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
            'email' => ['required', 'unique:App\Models\User,email', 'email'],
            'password' => ['required', 'string', 'confirmed'],  // confirmed = need password_confirmation field
            'location' => ['required']
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'location' => $fields['location'],
            'password' => Hash::make($fields['password'])   // encrypt password
        ]);

        if ($user->save()) {
            // automatically login registered user
            return response()->json($this->login($request)->original, 200);
        };
    }

    /**
     * Login to registered User account
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

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json(['error' => 'The provided credentials are invalid.'], 404);
        }

        return response()->json(["user" => $user, "token" => $user->createToken($user->name)->plainTextToken]);
    }

    /**
     * Get user details using token
     */
    public function verify()
    {
        return auth()->user();
    }

    /**
     * Logout authenticated user / delete auth token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return ['message' => 'Logged out'];
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
        $user = User::findOrFail($id);
        $user->update($request->all());

        if ($user->save()) return response()->json(["status" => "updated", "user" => $user], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->delete()) return response()->json(["status" => "deleted", "user" => $user], 200);
    }


    // add item to user's cart
    // public function addToCart($id) {
    //     $user = User::findOrFail($id);

    //     $user->update()
    // }
}
