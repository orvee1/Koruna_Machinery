<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PartstockSale extends Model
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
        static::creating(function (PartstockSale $sale) {
            $sale->total_amount = $sale->quantity * $sale->unit_price;
            $sale->due_amount = $sale->total_amount - ($sale->paid_amount ?? 0);
        });

        static::updating(function (PartstockSale $sale) {
            $sale->total_amount = $sale->quantity * $sale->unit_price;
            $sale->due_amount = $sale->total_amount - ($sale->paid_amount ?? 0);
        });

        // Sale Created → Reduce stock quantity & increase profit
        static::created(function (PartstockSale $sale) {
            $partStock = PartStock::find($sale->part_stock_id);
            if (!$partStock) return;

            $partStock->quantity -= $sale->quantity;

            $profitPerUnit = $sale->unit_price - $partStock->buying_price;
            $totalProfit = $profitPerUnit * $sale->quantity;

            $partStock->updateQuietly([
                'quantity' => max($partStock->quantity, 0),
                'total_profit' => $partStock->total_profit + $totalProfit,
            ]);
        });

        // Sale Deleted → Restore stock & reduce profit
        static::deleted(function (PartstockSale $sale) {
            $partStock = PartStock::find($sale->part_stock_id);
            if (!$partStock) return;

            $partStock->quantity += $sale->quantity;

            $profitPerUnit = $sale->unit_price - $partStock->buying_price;
            $totalProfit = $profitPerUnit * $sale->quantity;

            $partStock->updateQuietly([
                'quantity' => $partStock->quantity,
                'total_profit' => max($partStock->total_profit - $totalProfit, 0),
            ]);
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
}
