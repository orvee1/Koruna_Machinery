<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'product_name',
        'supplier_name',
        'buying_price',
        'selling_price',
        'quantity',
        'total_amount',
        'deposit_amount',
        'due_amount',
        'purchase_date',
    ];

    // Branch relation
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Model event: stock এন্ট্রি তৈরি হলে product তৈরি/আপডেট
    protected static function booted()
    {
        static::created(function (Stock $stock) {
            $product = Product::firstOrNew([
                'name'      => $stock->product_name,
                'branch_id' => $stock->branch_id,
            ]);

            $product->buying_price       = $stock->buying_price;
            $product->selling_price      = $stock->selling_price;
            $product->last_purchase_date = $stock->purchase_date;
            $product->stock_quantity     = ($product->exists ? $product->stock_quantity : 0)
                                           + $stock->quantity;

            $product->save();
        });
    }

        public function payments()
    {
        return $this->hasMany(ProductPayment::class);
    }

}
