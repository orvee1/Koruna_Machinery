<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'customer_id', 'seller_id', 'total_amount', 'paid_amount', 'due_amount', 'payment_status'
    ];

  
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

 
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

   
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
