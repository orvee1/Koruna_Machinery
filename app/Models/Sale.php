<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'customer_id', 'quantity', 'total_amount', 'paid_amount', 'due_amount', 'payment_status', 'investor_id','branch_id'];

    // Relationship with product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relationship with customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relationship with seller (user)
    // public function seller()
    // {
    //     return $this->belongsTo(User::class, 'seller_id');
    // }

    // Relationship with investor
    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function adjustStockOnSale($quantity, $partStockId)
    {
        // Decrease part stock
        $partStock = PartStock::find($partStockId);
        if ($partStock && $partStock->quantity >= $quantity) {
            $partStock->quantity -= $quantity;
            $partStock->save();
        }

        // Adjust product stock
        $product = $this->product;
        if ($product) {
            $product->adjustStockQuantity($quantity);
        }
    }

    public function adjustStockOnReturn($quantity, $partStockId)
    {
        // Increase part stock
        $partStock = PartStock::find($partStockId);
        if ($partStock) {
            $partStock->quantity += $quantity;
            $partStock->save();
        }

        // Adjust product stock
        $product = $this->product;
        if ($product) {
            $product->stock_quantity += $quantity;
            $product->save();
        }
    }
}