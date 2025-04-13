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

    public function payments()
    {
        return $this->hasMany(ProductPayment::class);
    }
    

    public function calculateTotalPurchaseAmount($buying_price, $stock_quantity)
    {
        $this->buying_price = $buying_price;
        $this->stock_quantity = $stock_quantity;
        $this->total_purchase_amount = $buying_price * $stock_quantity;
        $this->save();
    }

    public function remainingBalance()
    {
        return $this->total_purchase_amount - $this->paid_amount;
    }

}
