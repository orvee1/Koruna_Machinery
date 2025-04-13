<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPayment extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'paid_amount', 'payment_date'];

    // Relationship with product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // public function updatePayment($amount)
    // {
    //     $this->paid_amount += $amount;
    //     $this->save();
    // }

    // public function remainingBalance()
    // {
    //     return $this->total_purchase_amount - $this->paid_amount;
    // }
}
