<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'supplier_name', 'buying_price', 'quantity', 'total_amount', 'deposit_amount', 'due_amount', 'purchase_date'];

    // Relationship with product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relationship with branch
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    protected static function booted()
    {
        static::creating(function ($stock) {
            // Calculate the total amount and total profit
            $stock->total_amount = $stock->buying_price * $stock->quantity;
            $stock->due_amount = ($stock->total_amount - $stock->deposit_amount) ?: 0;

            // Automatically set the created_at date (ignore time)
            if (!$stock->created_at) {
                $stock->created_at = now()->toDateString();  // Only set the date part, not the time
            }
        });
    }

    public function scopeFilterByDate($query, $date)
    {
        return $query->whereDate('created_at', $date);  // Use whereDate to filter by date only (ignores time)
    }
}
