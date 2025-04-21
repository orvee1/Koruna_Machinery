<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id', 'product_name', 'supplier_name', 'buy_value', 'quantity', 'amount', 'sell_value', 'total_profit'
    ];

    /**
     * Define relationship with Branch model.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Automatically calculate amount and total profit on creation.
     * This method is triggered during model creation to handle calculations.
     */
    protected static function booted()
    {
        static::creating(function ($partStock) {
            // Calculate the total amount and total profit
            $partStock->amount = $partStock->buy_value * $partStock->quantity;
            $partStock->total_profit = ($partStock->sell_value - $partStock->buy_value) * $partStock->quantity;

            // Automatically set the created_at date (ignore time)
            if (!$partStock->created_at) {
                $partStock->created_at = now()->toDateString();  // Only set the date part, not the time
            }
        });
    }

    /**
     * Scope a query to filter PartStock by date.
     * This will return the part stocks created on a specific date.
     */
    public function scopeFilterByDate($query, $date)
    {
        return $query->whereDate('created_at', $date);  // Use whereDate to filter by date only (ignores time)
    }
}
