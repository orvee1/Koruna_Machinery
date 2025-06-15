<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'product_name',
        'supplier_name',
        'buying_price',
        'selling_price',
        'quantity',
        'total_amount',
        'deposit_amount',
        'due_amount',
        'purchase_date',
        'total_profit',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function payments()
    {
        return $this->hasMany(ProductPayment::class, 'stock_id', 'id');
    }

    protected static function booted()
    {
        static::creating(function (Stock $stock) {
        $stock->total_amount = $stock->buying_price * $stock->quantity;
        $depositAmount = $stock->deposit_amount ?? 0;
        $stock->due_amount = max($stock->total_amount - $depositAmount, 0);
    });
        static::updating(function (Stock $stock) {
        $stock->total_amount = $stock->buying_price * $stock->quantity;
        $depositAmount = $stock->deposit_amount ?? 0;
    });


          static::created(function (Stock $stock) {
            \App\Models\ProductList::create([
                'branch_id' => $stock->branch_id,
                'product_name' => $stock->product_name,
                'supplier_name' => $stock->supplier_name,
                'buying_price' => $stock->buying_price,
                'quantity' => $stock->quantity,
                'total_amount' => $stock->total_amount,
                'purchase_date' => $stock->purchase_date,
                'branch_name' => $stock->branch->name ?? '—',
            ]);
        });
        
    }
}
