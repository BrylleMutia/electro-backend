<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'email' => ['required'],
            'password' => ['required'],
            'location' => ['required']
        ]);

        // validate for duplication
        $user = User::where('email', $request->email)->first();
        if($user) return response()->json(['error' => "Email is already registered."], 400);

        $user = User::create([
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
        $request->validate([
            'email' => ['required'],
            'password' => ['required']
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'The provided credentials are invalid.']);
        }

        return response($user->createToken($user->name)->plainTextToken);
    }

    
    // get and verify current user details using token
    public function verify() 
    {
        return auth()->user();
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
    public function addToCart($id) {
        $user = User::findOrFail($id);

        $user->update()
    }
}
