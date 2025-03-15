<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'customer_id', 'seller_id', 'total_amount', 'paid_amount', 'due_amount', 'payment_status'
    ];

  
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

    public function adjustStocOnSale($quantity)
    {
        $product = $this->product;
        $stock = Stock::where('product_id', $product->id)->first();
        if($stock)
        {
            $stock->decreaseStock($quantity);
        }
        $product->adjustStockQuantity($quantity);
    }

    public function adjustStockOnReturn($quantity)
    {
        $product = $this->product;
        $stock = Stock::where('product_id', $product->id)->first();
        if($stock)
        {
            $stock->increaseStock($quantity);
        }
        $product->restoreStockQuantity($quantity);
    }
}
