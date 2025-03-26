<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositHistory extends Model
{
    use HasFactory;

    protected $fillable = ['investor_id', 'amount', 'payment_method', 'payment_date'];

    // Relationship with investor
    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }
}