<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'buying_price', 'selling_price', 'stock_quantity', 'branch_id','total_purchase_amount', 'paid_amount'];

    // Relationship with branch
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Relationship with sales
    public function sale()
    {
        return $this->hasMany(Sale::class);
    }

    // Relationship with stocks
    public function stock()
    {
        return $this->hasMany(Stock::class);
    }

    // Relationship with part stocks
    public function partStock()
    {
        return $this->hasMany(PartStock::class);
    }
    
    // Method to adjust stock for the product
    //     public function adjustStockQuantity($quantity)
    // {
    //     if ($this->stock_quantity >= $quantity) {
    //         $this->stock_quantity -= $quantity;
    //         $this->save();
    //     } else {
    //         // Handle the case where there's not enough stock
    //         throw new \Exception("Not enough stock available.");
    //     }
    // }

    //     public function totalStockAvailable()
    // {
    //     $partStock = $this->partStock->sum('quantity');
    //     return $this->stock_quantity + $partStock;
    // }

    public function calculateTotalPurchaseAmount($buying_price, $stock_quantity)
    {
        // Total cost of the purchased products (quantity * purchase price)
        $this->total_purchase_amount = $this->$buying_price * $stock_quantity;
        $this->save();
    }

    public function updatePayment($amount)
    {
        $this->paid_amount += $amount;
        $this->save();
    }

    public function remainingBalance()
    {
        return $this->total_purchase_amount - $this->paid_amount;
    }

}
