<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'product_id',
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

    // Automatically calculate total and due
   protected static function booted()
{
    static::created(function (ProductSale $sale) {
        // প্রোডাক্ট খুঁজে বের করা
        $product = Product::find($sale->product_id);

        if ($product) {
            // 🔽 স্টক থেকে কোয়ান্টিটি কমানো
            $product->stock_quantity -= $sale->quantity;
            $product->save();
        }

        // 🔎 স্টক খুঁজে বের করা
        $stock = Stock::where('product_name', $product->name)
                      ->where('branch_id', $sale->branch_id)
                      ->first();

        if ($stock) {
            // 🔽 স্টক থেকে কোয়ান্টিটি কমানো
            $stock->quantity -= $sale->quantity;

            // 🔄 প্রফিট হিসাব করে যোগ করা
            $profitPerUnit = $sale->unit_price - $stock->buying_price;
            $totalProfit = $profitPerUnit * $sale->quantity;
            $stock->total_profit += $totalProfit;

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

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
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
