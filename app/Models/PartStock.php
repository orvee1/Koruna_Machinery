<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name', 'buy_value', 'quantity', 'amount', 'sell_value', 'total_profit'
    ];
}
