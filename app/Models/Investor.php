<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investor extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'total_investment', 'balance', 'status'];

    // Relationship with investment histories
    public function investmentHistories()
    {
        return $this->hasMany(InvestmentHistory::class);
    }

    // Relationship with sales
    public function sale()
    {
        return $this->hasMany(Sale::class);
    }

    // Relationship with deposit histories
    public function depositHistories()
    {
        return $this->hasMany(DepositHistory::class);
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
