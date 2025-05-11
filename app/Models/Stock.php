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
        'quantity',
        'total_amount',
        'deposit_amount',
        'due_amount',
        'purchase_date',
        'total_profit',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function productSales()
    {
        return $this->hasMany(ProductSale::class);
    }

    protected static function booted()
    {
        /**
         * ✅ **স্টক তৈরি হলে প্রোডাক্ট সিঙ্ক হবে**
         */
        static::created(function (Stock $stock) {
            $product = Product::firstOrNew([
                'name' => $stock->product_name,
                'branch_id' => $stock->branch_id,
            ]);

            $product->buying_price = $stock->buying_price;
            $product->last_purchase_date = $stock->purchase_date;

            // ✅ **স্টক থেকে প্রোডাক্টের কোয়ান্টিটি বাড়ানো হচ্ছে**
            $product->stock_quantity += $stock->quantity;
            $product->save();
        });

        /**
         * ✅ **স্টক আপডেট হলে প্রোডাক্টেও আপডেট হবে**
         */
        static::updated(function (Stock $stock) {
            $product = Product::firstOrNew([
                'name' => $stock->product_name,
                'branch_id' => $stock->branch_id,
            ]);

            // ✅ **প্রথমে পুরনো কোয়ান্টিটি বাদ দেওয়া হচ্ছে**
            $originalQuantity = $product->stock_quantity - $stock->getOriginal('quantity');
            $product->stock_quantity = $originalQuantity + $stock->quantity;

            // ✅ **প্রাইস এবং তারিখ আপডেট হচ্ছে**
            $product->buying_price = $stock->buying_price;
            $product->last_purchase_date = $stock->purchase_date;

            // ✅ **স্টক 0 হলে প্রোডাক্ট মুছে ফেলা হবে**
            if ($product->stock_quantity <= 0) {
                $product->delete();
            } else {
                $product->save();
            }
        });

        /**
         * ✅ **স্টক ডিলিট হলে প্রোডাক্ট আপডেট হবে**
         */
        static::deleted(function (Stock $stock) {
            $product = Product::firstOrNew([
                'name' => $stock->product_name,
                'branch_id' => $stock->branch_id,
            ]);

            // ✅ **প্রোডাক্টের স্টক থেকে বাদ দেওয়া হচ্ছে**
            $product->stock_quantity -= $stock->quantity;

            if ($product->stock_quantity <= 0) {
                $product->delete();
            } else {
                $product->save();
            }
        });
    }
}
