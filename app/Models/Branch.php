<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

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
