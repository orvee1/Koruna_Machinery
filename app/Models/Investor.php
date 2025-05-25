<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investor extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'total_investment', 'balance', 'status'];

    public function branches()
    {
        return $this->belongsTo(Branch::class);
    }

    public function partStocks()
    {
        return $this->belongsToMany(PartStock::class, 'investor_partstock', 'investor_id', 'partstock_id');
    }

    public function productSales()
    {
        return $this->hasMany(ProductSale::class);
    }

    public function partStockSales()
    {
        return $this->hasMany(PartStockSale::class);
    }
    
    // Relationship with investment histories
    public function investmentHistories()
    {
        return $this->hasMany(InvestmentHistory::class);
    }

    // Close the investor's panel when balance >= total investment
    public function closePanel()
    {
        if ($this->balance >= $this->total_investment) {
            $this->status = 'closed';
            $this->save();
        }
    }
}
