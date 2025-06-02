<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillPayment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'bill_id',
        'paid_amount',
        'payment_date',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
