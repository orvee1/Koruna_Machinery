<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'district', 'customer_id', 'branch_id'];

    // Relationship with Branch
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Generate a unique customer_id based on branch's first two letters
    public static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (!$customer->customer_id) {
                // Get the branch's first two letters
                $branchCode = optional($customer->branch)->code;
                $branchPrefix = substr($branchCode, 0, 2); // First two letters of the branch name

                // Get the latest customer_id for that branch
                $lastCustomer = self::where('branch_id', $customer->branch_id)
                    ->latest()
                    ->first();

                // Generate the customer_id (e.g., 'BR0001')
                $lastNumber = $lastCustomer ? (int) substr($lastCustomer->customer_id, 2) : 0;
                $newCustomerId = $branchPrefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

                $customer->customer_id = $newCustomerId;
            }
        });
    }
}
