<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'feedback'
    ];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
