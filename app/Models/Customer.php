<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'district', 'customer_id', 'branch_id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (!$customer->customer_id) {
                $branchCode = optional($customer->branch)->code;
                $branchPrefix = substr($branchCode, 0, 2);

                $lastCustomer = self::where('branch_id', $customer->branch_id)
                    ->latest()
                    ->first();

                $lastNumber = $lastCustomer ? (int) substr($lastCustomer->customer_id, 2) : 0;
                $newCustomerId = $branchPrefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

                $customer->customer_id = $newCustomerId;
            }
        });
    }
}
