<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartStockSalePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'partstock_sale_id',
        'paid_amount',
        'payment_date',
    ];

    public function partStockSale()
    {
        return $this->belongsTo(PartStockSale::class);
    }
}
