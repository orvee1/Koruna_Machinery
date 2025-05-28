<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PartStockSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'part_stock_id',
        'customer_id',
        'seller_id',
        'quantity',
        'unit_price',
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_status',
        'investor_id',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];

     protected static function booted()
{
    // ✅ সেল তৈরি হলে total_amount ও due_amount হিসাব হবে
    static::creating(function (PartStockSale $sale) {
        $sale->total_amount = $sale->quantity * $sale->unit_price;
        $sale->due_amount = max($sale->total_amount - ($sale->paid_amount ?? 0), 0);
    });

    // ✅ সেল আপডেট হলেও হিসাব হবে (for safety)
    static::updating(function (PartStockSale $sale) {
        $sale->total_amount = $sale->quantity * $sale->unit_price;
        $sale->due_amount = max($sale->total_amount - ($sale->paid_amount ?? 0), 0);
    });

    // ✅ সেল তৈরি হলে PartStock quantity, total_amount, total_profit একসাথে আপডেট
    static::created(function (PartStockSale $sale) {
        $partStock = PartStock::find($sale->part_stock_id);
        if (!$partStock) return;

        $partStock->quantity -= $sale->quantity;

        $profitPerUnit = $sale->unit_price - $partStock->buying_price;
        $partStock->total_profit += $profitPerUnit * $sale->quantity;

        // 🔄 Total amount update
        $partStock->total_amount = $partStock->buying_price * $partStock->quantity;

        $partStock->save(); // সব একসাথে save
    });

    // ✅ সেল ডিলিট হলে quantity ও profit ফেরত, total_amount পুনঃহিসাব
    static::deleted(function (PartStockSale $sale) {
        $partStock = PartStock::find($sale->part_stock_id);
        if (!$partStock) return;

        $partStock->quantity += $sale->quantity;

        $profitPerUnit = $sale->unit_price - $partStock->buying_price;
        $partStock->total_profit -= $profitPerUnit * $sale->quantity;

        if ($partStock->total_profit < 0) {
            $partStock->total_profit = 0;
        }

        // 🔄 Total amount পুনঃহিসাব
        $partStock->total_amount = $partStock->buying_price * $partStock->quantity;

        $partStock->save();
    });
}



    // Scope Filters
    public function scopeForToday($query)
    {
        return $query->whereDate('created_at', now());
    }

    public function scopeForMonth($query, $month)
    {
        return $query->whereMonth('created_at', $month);
    }

    public function scopeForYear($query, $year)
    {
        return $query->whereYear('created_at', $year);
    }

    // Relationships
    public function branch()
     { 
        return $this->belongsTo(Branch::class);
     }

    public function partStock()
     { 
        return $this->belongsTo(PartStock::class);
     }
     
    public function customer()
     { 
        return $this->belongsTo(Customer::class);
     }

    public function seller()
     { 
        return $this->belongsTo(User::class, 'seller_id');
     }

    public function investor()
     { 
        return $this->belongsTo(Investor::class); 
     }

    public function payments()
     { 
        return $this->hasMany(PartStockSalePayment::class);
     }

     public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
