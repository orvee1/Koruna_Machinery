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
        static::created(function (ProductSale $sale) {
            // ✅ **প্রথমেই স্টক খুঁজে বের করা হচ্ছে**
            $stock = Stock::find($sale->stock_id);

            if (!$stock) {
                // ✅ **স্টক খুঁজে না পেলে লগিং করা হচ্ছে**
                Log::error("Stock not found for Stock ID: {$sale->stock_id} in Branch ID: {$sale->branch_id}");
                return;
            }

            // ✅ **স্টকের পরিমাণ কমানো হচ্ছে**
            $stock->quantity -= $sale->quantity;

            // ✅ **প্রফিট হিসাব করে যোগ করা হচ্ছে**
            $profitPerUnit = $sale->unit_price - $stock->buying_price;
            $totalProfit = $profitPerUnit * $sale->quantity;
            $stock->total_profit += $totalProfit;

            // ✅ **স্টক 0 বা তার কম হলে ডিলিট হবে, অন্যথায় সেভ হবে**
            if ($stock->quantity <= 0) {
                $stock->delete();
            } else {
                $stock->save();
            }
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
