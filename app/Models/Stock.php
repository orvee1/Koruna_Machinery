<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'supplier_name', 'buying_price', 'quantity', 'total_amount', 'deposit_amount', 'due_amount', 'purchase_date'
    ];

   
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

   
    public function branch()
    {
        return $this->hasOneThrough(Branch::class, Product::class, 'id', 'id', 'product_id', 'branch_id');
    }

    public function decreaseStock($quantity)
    {
        $this->quantity -= $quantity;
        $this->save();
    }

    public function increaseStock($quantity)
    {
        $this->quantity += $quantity;
        $this->save();
    }
}
