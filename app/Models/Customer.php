<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'district',
        'customer_id',
        'branch_id',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public static function generateCustomerId($branchName)
    {
       $branch = Branch::where('name', $branchName)->first();

       $prefix = $branch ? $branch->code : 'UN';

       $lastCustomer = self::where('customer_id', 'Like', $prefix . '%')->latest()->first();

       $nextId = $lastCustomer ? ((int) substr($lastCustomer->customer_id, strlen($prefix))) + 1 : 1;

       return $prefix . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }
}
