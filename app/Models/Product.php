<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'buying_price', 'selling_price', 'stock_quantity', 'branch_id','total_purchase_amount', 'paid_amount', 'payment_date'];
   
    // Relationship with branch
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    protected static function booted()
    {
        static::creating(function ($product) {
            // Calculate the total amount and total profit
            $product->total_purchase_amount = $product->buying_price * $product->stock_quantity;

            // Automatically set the created_at date (ignore time)
            if (!$product->created_at) {
                $product->created_at = now()->toDateString();  // Only set the date part, not the time
            }
        });
    }

    public function scopeFilterByDate($query, $date)
    {
        return $query->whereDate('created_at', $date);  // Use whereDate to filter by date only (ignores time)
    }



    // Relationship with stocks
    public function stock()
    {
        return $this->hasMany(Stock::class);
    }

    public function payments()
    {
        return $this->hasMany(ProductPayment::class);
    }
    
    public function paidAmount()
    {
        return $this->payments->sum('paid_amount');
    }

    public function remainingBalance()
    {
        $totalPaid = $this->payments->sum('paid_amount');
        return $this->total_purchase_amount - $totalPaid;
    }

}
