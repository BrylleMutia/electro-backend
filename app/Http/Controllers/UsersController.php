<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
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
            'address' => [],
            'barangay' => ['string', 'required'],
            'city' => ['required', 'string'],
            'province' => ['required', 'string'],
            'zip_code' => ['required', 'string'],
            'role_id' => [],
            'phone' => ['required', 'string', 'min:11', 'max:13']
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'address' => $fields['address'],
            'barangay' => $fields['barangay'],
            'city' => $fields['city'],
            'province' => $fields['province'],
            'zip_code' => $fields['zip_code'],
            'role_id' => 1,  // 1 = buyer, 2 = seller
            'phone' => $fields['phone'],
            'password' => Hash::make($fields['password'])   // encrypt password
        ]);

        // user image
        if ($request->hasFile('image')) {
            $image_path = $request->file('image')->storeOnCloudinary('users')->getSecurePath();
            $user->image = $image_path;
        }

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
            return response()->json(['message' => 'The provided credentials are invalid.', 'errors' => ['error' => 'The provided credentials are invalid.']], 404);
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
     * Add a new purchase record (using stripe)
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function purchase(Request $request)
    {
        $user = User::findOrFail($request->id);

        try {
            // create stripe customer
            // so that user details will also be sent to stripe
            $user->createOrGetStripeCustomer();

            // * DOCS: https://laravel.com/docs/8.x/billing#single-charges
            $payment = $user->charge(
                $request->input('amount') . "00",   // include cents (lowest denominator needed here)
                $request->input('payment_method_id')
            );

            // get additional payment details for order table
            $payment = $payment->asStripePaymentIntent();

            // create order
            $order = Order::create([
                'user_id' => $user->id,
                'transaction_id' => $payment->charges->data[0]->id,
                'total' => $payment->charges->data[0]->amount
            ]);

            // attach order to pivot table (products / sellers)
            $sellers = [];
            foreach (json_decode($request->input('cart'), true) as $item) {
                $order->products()->attach($item['product']['id'], ['quantity' => $item['quantity']]);

                // *check if seller_id is already recorded for this order
                // to avoid duplicates
                if (!in_array($item['product']['seller_id'], $sellers)) {
                    array_push($sellers, $item['product']['seller_id']);
                }
            }

            // attach involved sellers for current order
            $order->sellers()->sync($sellers);

            // return order with products (lazy-loaded)
            $order->load('products');

            return $order;
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Get all orders from current user
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function orders(Request $request)
    {
        $user_orders = User::with("orders.products", "orders.status")->findOrFail(auth()->user()->id)->orders;

        return $user_orders;
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = User::findOrFail(auth()->user()->id);

        // TODO: NEED VALIDATION HERE 
        $user->update($request->all());

        // update user image
        if ($request->hasFile('image')) {
            $image_path = $request->file('image')->storeOnCloudinary('users')->getSecurePath();
            $user->image = $image_path;
        }

        if ($user->save()) return response()->json($user, 200);
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
