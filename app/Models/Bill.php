<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'branch_id',
        'seller_id',
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_status',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function productSales()
    {
        return $this->hasMany(ProductSale::class, 'bill_id');
    }

    public function partStockSales()
    {
        return $this->hasMany(PartStockSale::class, 'bill_id');
    }

    public function payments()
    {
        return $this->hasMany(BillPayment::class);
    }
}
