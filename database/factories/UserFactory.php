<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $image_urls = [
            "https://res.cloudinary.com/bryllemutia/image/upload/v1620284808/users/kwzarax3qrwpf1snzs0h.jpg",
            "https://res.cloudinary.com/bryllemutia/image/upload/v1620284935/users/e4nvl4beqfibhixtntay.jpg",
            "https://res.cloudinary.com/bryllemutia/image/upload/v1620284964/users/epqcjh8lbpge978mm1zc.jpg",
            "https://res.cloudinary.com/bryllemutia/image/upload/v1620284991/users/gzk2pvvy3ffjylwxarx8.jpg",
            "https://res.cloudinary.com/bryllemutia/image/upload/v1620285045/users/cqzbe1ss9owlribjrhy9.jpg"
        ];

        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'image' => Arr::random($image_urls),
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'barangay' => $this->faker->streetName,
            'province' => $this->faker->state,
            'zip_code' => $this->faker->postcode,
            'role_id' => 1,    // 1 = buyer, 2 = seller
            'phone' => $this->faker->phoneNumber,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }
}
