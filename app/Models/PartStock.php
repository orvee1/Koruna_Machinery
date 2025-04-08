<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name', 'buy_value', 'quantity', 'amount', 'sell_value', 'total_profit', 'product_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function branch()
    {
        return $this->belongsToThrough(Branch::class, Product::class);
    }
}
