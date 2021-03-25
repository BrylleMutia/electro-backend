<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'price',
        'category_id',
        'seller_id'
    ];

    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function seller() {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

}
