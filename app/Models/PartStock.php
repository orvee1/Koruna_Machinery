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
        'buying_price',
        'quantity',
        'total_amount',
        'deposit_amount',
        'due_amount',
        'sell_value',
        'total_profit',
        'purchase_date'
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

    // public function paidAmount()
    // {
    //     return $this->payments()->sum('paid_amount');
    // }

    // public function remainingBalance()
    // {
    //     return $this->due_amount - $this->paidAmount();
    // }

    protected static function booted()
    {
        static::creating(function (PartStock $partStock) {
            $partStock->total_amount = $partStock->buying_price * $partStock->quantity;
            $depositAmount = $partStock->deposit_amount ?? 0;
            $partStock->due_amount = max($partStock->total_amount - $depositAmount, 0);
        });

        static::updating(function (PartStock $partStock) {
            $partStock->total_amount = $partStock->buying_price * $partStock->quantity;
            $depositAmount = $partStock->deposit_amount ?? 0;
            $partStock->due_amount = max($partStock->total_amount - $depositAmount, 0);
        });

        static::created(function (PartStock $partStock) {
            \App\Models\ProductList::create([
                'branch_id'     => $partStock->branch_id,
                'product_name'  => $partStock->product_name,
                'supplier_name' => $partStock->supplier_name,
                'buying_price'  => $partStock->buying_price,
                'quantity'      => $partStock->quantity,
                'total_amount'  => $partStock->total_amount,
                'purchase_date' => $partStock->purchase_date,
                'branch_name'   => $partStock->branch->name ?? '—',
            ]);
        });
    }
}
