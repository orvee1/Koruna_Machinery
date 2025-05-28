<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
     use HasFactory;

    protected $fillable = [
        'customer_id', 'branch_id', 'seller_id',
        'total_amount', 'paid_amount', 'due_amount', 'payment_status'
    ];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function seller() {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function productSales() {
        return $this->hasMany(ProductSale::class);
    }

    public function partStockSales() {
        return $this->hasMany(PartStockSale::class);
    }
}
