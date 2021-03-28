<?php

namespace App\Models;

use App\Models\Seller;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'product_id',
        'quantity',
        'buyer'
    ];

    public function seller() {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function product() {
        return $this->hasOne(Product::class, 'product_id');
    }
}
