<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'buying_price',
        'selling_price',
        'stock_quantity',
        'branch_id',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    
    public function adjustStockQuantity($quantity)
    {
        $this->stock_quantity -= $quantity;
        $this->save();
    }

    public function restoreStockQuantity($quantity)
    {
        $this->stock_quantity += $quantity;
        $this->save();
    }

}