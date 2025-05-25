<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentHistory extends Model
{
    use HasFactory;

    protected $fillable = ['investor_id', 'product_id', 'quantity', 'buying_price', 'total_cost'];

    // Relationship with investor
    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    // Relationship with product
   
}
