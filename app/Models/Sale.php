<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'customer_id', 'quantity', 'total_amount', 'paid_amount', 'due_amount', 'payment_status', 'investor_id'];

    // Relationship with product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relationship with customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relationship with seller (user)
    // public function seller()
    // {
    //     return $this->belongsTo(User::class, 'seller_id');
    // }

    // Relationship with investor
    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }
}