<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductList extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'product_name',
        'supplier_name',
        'buying_price',
        'quantity',
        'total_amount',
        'purchase_date',
        'branch_name',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
