<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public function orderdetails()
    {
        return $this->hasMany(OrderDetails::class, 'order_id');
    }
    public function orderdetail()
    {
        return $this->hasOne(OrderDetails::class, 'order_id');
    }
    public function product()
    {
        return $this->belongsTo(OrderDetails::class, 'id', 'order_id')->select('id','order_id','product_id');
    }
    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status');
    }
    public function shipping()
    {
        return $this->belongsTo(Shipping::class, 'id', 'order_id');
    }
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'id', 'order_id');
    }
    public function payments() {
        return $this->hasMany(Payment::class, 'order_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function ordernote()
    {
        return $this->hasOne(OrderNote::class, 'order_id')->latest();
    }
    public function ordernotes() {
        return $this->hasMany(OrderNote::class, 'order_id');
    }
}
