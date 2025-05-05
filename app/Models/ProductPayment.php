<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPayment extends Model
{
    use HasFactory;

    protected $fillable = ['stock_id', 'paid_amount', 'payment_date'];

    // Relationship with product
    public function product()
    {
        return $this->belongsTo(Stock::class);
    }

}
