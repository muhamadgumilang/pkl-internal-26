<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
     protected $fillable = ['cart_id', 'product_id', 'quantity'];

     protected $appends = ['subtotal'];

     public function cart()
     {
        return $this->belongsTo(Cart::class);
     }

     public function product()
     {
         return $this->belongsTo(Product::class);
     }

    //  Hitung subtotal item
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->product->price;
     }
}