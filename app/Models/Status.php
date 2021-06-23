<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = 'status';

    protected $fillable = [
        'name'
    ];

    protected const IS_PENDING = 1;
    protected const IS_SHIPPED = 2;
    protected const IS_DELIVERED = 3;

    public function order() {
        return $this->hasMany(Order::class, 'status_id');
    }
}
