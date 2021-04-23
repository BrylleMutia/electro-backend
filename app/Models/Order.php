<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_id',
        'total'
    ];

    protected $casts = [
        'items' => 'array'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function products() {
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }
}
