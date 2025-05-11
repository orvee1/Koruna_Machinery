<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'buying_price',
        'selling_price',
        'stock_quantity',
        'branch_id',
        'last_purchase_date',
    ];

    // Branch relation
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Stock relation (optional, reverse via product_name)
    public function stocks()
    {
        return $this->hasMany(Stock::class, 'product_name', 'name');
    }

    protected static function booted()
    {
        static::updated(function (Product $product) {
            if ($product->stock_quantity <= 0) {
                $product->delete(); // প্রোডাক্টের স্টক শেষ হলে মুছে যাবে
            }
        });
    }

    public function scopeSearch($query, $term)
    {
        $term = "%{$term}%";
        $query->where(function($q) use($term) {
            $q->where('name', 'like', $term)
              ->orWhereHas('branch', function($q2) use($term) {
                  $q2->where('name', 'like', $term);
              });
        });
    }
}
