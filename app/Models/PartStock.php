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
        return $this->hasMany(PartStockPayment::class, 'part_stock_id', 'id');
    }

    protected static function booted()
    {
            static::creating(function (PartStock $partStock) {
           
            $partStock->total_amount = $partStock->buying_price * $partStock->quantity;
            $depositAmount = $partStock->deposit_amount ?? 0;
            $partStock->due_amount = max($partStock->amount - $depositAmount, 0);
        });
            static::updating(function (PartStock $partStock) {
            $partStock->total_amount = $partStock->buying_price * $partStock->quantity;
            $depositAmount = $partStock->deposit_amount ?? 0;
            $partStock->due_amount = max($partStock->amount - $depositAmount, 0);
        });

          static::created(function (PartStock $partStock) {
            // ✅ ProductList এ ডেটা কপি হচ্ছে
            \App\Models\ProductList::create([
                'branch_id' => $partStock->branch_id,
                'product_name' => $partStock->product_name,
                'supplier_name' => $partStock->supplier_name,
                'buy_value' => $partStock->buy_value,
                'quantity' => $partStock->quantity,
                'amount' => $partStock->amount,
                'purchase_date' => $partStock->purchase_date,
                'branch_name' => $partStock->branch->name ?? '—',
            ]);
        });
    }
}
