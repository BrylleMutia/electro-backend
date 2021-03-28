<?php

namespace App\Models;

use App\Models\Seller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'price',
        'product_image',
        'description',
        'category_id',
        'seller_id',
    ];

    protected $casts = [
        'product_image' => 'array'
    ];

    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function seller() {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

}
