<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;


class ProductSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'stock_id',
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

    /**
     * ✅ **স্টক এন্ট্রি অ্যাড হওয়া মাত্রই এর কোয়ান্টিটি ও প্রফিট আপডেট হবে**
     */
   protected static function booted()
    {
        // ✅ সেল ক্রিয়েটের সময় total_amount এবং due_amount হিসাব
        static::creating(function (ProductSale $sale) {
            $sale->total_amount = $sale->quantity * $sale->unit_price;
            $sale->due_amount = $sale->total_amount - ($sale->paid_amount ?? 0);
        });

        // ✅ সেল আপডেটের সময়ও same হিসাব
        static::updating(function (ProductSale $sale) {
            $sale->total_amount = $sale->quantity * $sale->unit_price;
            $sale->due_amount = $sale->total_amount - ($sale->paid_amount ?? 0);
        });

        // ✅ সেল হওয়ার পর স্টকের প্রফিট ও কোয়ান্টিটি কমানো
        static::created(function (ProductSale $sale) {
        $stock = Stock::find($sale->stock_id);
        if (!$stock) return;

        $stock->quantity -= $sale->quantity;

        $profitPerUnit = $sale->unit_price - $stock->buying_price;
        $totalProfit = $profitPerUnit * $sale->quantity;
        $stock->total_profit += $totalProfit;

        // 👉 save() না করে নিচের line ব্যবহার করুন
        $stock->updateQuietly([
            'quantity' => max($stock->quantity, 0),
            'total_profit' => $stock->total_profit,
        ]);
    });


        // ✅ সেল ডিলিট হলে স্টক quantity ও profit ফেরত
        static::deleted(function (ProductSale $sale) {
            $stock = Stock::find($sale->stock_id);
            if (!$stock) return;

            $stock->quantity += $sale->quantity;

            $profitPerUnit = $sale->unit_price - $stock->buying_price;
            $totalProfit = $profitPerUnit * $sale->quantity;
            $stock->total_profit -= $totalProfit;

            if ($stock->total_profit < 0) $stock->total_profit = 0;

            $stock->save();
        });
    }

    // Scopes for filtering
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

    // ✅ **Relationships**
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
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
}
