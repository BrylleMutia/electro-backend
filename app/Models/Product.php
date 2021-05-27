<?php

namespace App\Models;

use App\Models\Offer;
use App\Models\Order;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Model;
use CloudinaryLabs\CloudinaryLaravel\MediaAlly;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, MediaAlly;

    protected $fillable = [
        'product_name',
        'price',
        'slug',
        'product_image',
        'description',
        'category_id',
        'seller_id',
        'offer_id'
    ];

    protected $casts = [
        'product_image' => 'array'
    ];

    // protected $with = ['categories', 'seller', 'offer', 'orders'];

    public function categories() {
        // take note of the pivot table assignment
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function orders() {
        return $this->belongsToMany(Order::class, 'order_product');
    }

    public function seller() {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function offer() {
        return $this->belongsTo(Offer::class, 'offer_id');
    }

    public function reviews() {
        return $this->hasMany(Review::class, 'product_id')->orderByDesc('updated_at');
    }

}
