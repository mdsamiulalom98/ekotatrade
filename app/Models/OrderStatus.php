<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function orders(){
        return $this->hasMany(Order::class, 'order_status')->select('id','order_status', 'amount')->where('is_trashed', NULL);
    }
}
