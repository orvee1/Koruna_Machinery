<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSalePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_sale_id',
        'paid_amount',
        'payment_date',
    ];

    public function productSale()
    {
        return $this->belongsTo(ProductSale::class);
    }
}
