<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($branch) {
            if (!$branch->code) {
                // Get the first two letters of the branch name
                $branchPrefix = strtoupper(substr($branch->name, 0, 2)); // Make sure the code is in uppercase

                // Get the last branch code with this prefix
                $lastBranch = self::where('code', 'like', $branchPrefix . '%')
                                  ->latest()
                                  ->first();

                // Get the next number for the code
                $lastNumber = $lastBranch ? (int) substr($lastBranch->code, 2) : 0;
                $newBranchCode = $branchPrefix . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);  // Example: "NY001", "NY002"

                // Assign the generated code to the branch
                $branch->code = $newBranchCode;
            }
        });
    }

    // One-to-many relationship with users
    public function user()
    {
        return $this->hasMany(User::class);
    }

    // One-to-many relationship with products
    public function product()
    {
        return $this->hasMany(Product::class);
    }

    // One-to-many relationship with customers
    public function customer()
    {
        return $this->hasMany(Customer::class);
    }
}
