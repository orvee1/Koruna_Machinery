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
        static::creating(function ($sale) {
            $sale->total_amount = $sale->quantity * $sale->unit_price;
            $sale->due_amount = $sale->total_amount - $sale->paid_amount;
        });

        static::updating(function ($sale) {
            $sale->total_amount = $sale->quantity * $sale->unit_price;
            $sale->due_amount = $sale->total_amount - $sale->paid_amount;
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
}
