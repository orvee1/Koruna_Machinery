<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'supplier_name', 'buying_price', 'quantity', 'total_amount', 'deposit_amount', 'due_amount', 'purchase_date'];

    // Relationship with product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relationship with branch
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
