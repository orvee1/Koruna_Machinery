<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'product_name',
        'supplier_name',
        'buy_value',
        'quantity',
        'amount',
        'deposit_amount',
        'due_amount',
        'sell_value',
        'total_profit'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function partSales()
    {
        return $this->hasMany(PartstockSale::class, 'part_stock_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(PartStockPayment::class);
    }

    public function paidAmount()
    {
        return $this->payments->sum('paid_amount');
    }

    public function remainingBalance()
    {
        return $this->amount - $this->paidAmount();
    }

    protected static function booted()
    {
        static::creating(function (PartStock $stock) {
            $stock->amount = $stock->buy_value * $stock->quantity;
            $stock->total_profit = 0;
        });

        static::updating(function (PartStock $stock) {
            $stock->amount = $stock->buy_value * $stock->quantity;
            $stock->total_profit = $stock->total_profit ?? 0;
        });
    }
}
