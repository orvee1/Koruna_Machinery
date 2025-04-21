<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartStockPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_stock_id', 'paid_amount', 'payment_date'
    ];

    public function partStock()
    {
        return $this->belongsTo(PartStock::class);
    }
}
