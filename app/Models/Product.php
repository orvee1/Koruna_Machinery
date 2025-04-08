<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'buying_price', 'selling_price', 'stock_quantity', 'branch_id'];

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
    public function adjustStockQuantity($quantity)
    {
        $this->stock_quantity -= $quantity;
        $this->save();
    }
}
