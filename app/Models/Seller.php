<?php

namespace App\Models;

use App\Models\Payment;
use App\Models\Product;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use CloudinaryLabs\CloudinaryLaravel\MediaAlly;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Seller extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, MediaAlly;

    protected $guard = 'seller';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'barangay',
        'city',
        'province',
        'zip_code',
        'role_id',
        'image',
        'phone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }

    public function products() {
        return $this->hasMany(Product::class, 'seller_id')->orderByDesc("created_at");
    }

    public function orders() {
        return $this->belongsToMany(Order::class);
    }

    public function role() {
        return $this->belongsTo(Role::class, 'role_id');
    }   
}
