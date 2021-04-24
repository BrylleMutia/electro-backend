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

    public function categories() {
        return $this->belongsToMany(Category::class, 'category_id');
    }

    public function orders() {
        return $this->belongsToMany(Order::class, 'order_id');
    }

    public function seller() {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function offer() {
        return $this->belongsTo(Offer::class, 'offer_id');
    }

}
