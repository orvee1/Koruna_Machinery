<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'total_profit',
    ];

    // 📌 **Relationship Definition**
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function productSales()
    {
        return $this->hasMany(ProductSale::class, 'stock_id', 'id');
    }

    /**
     * ✅ **booted Method**
     * Stock তৈরি বা আপডেট হলে অটোমেটিক্যালি Total Amount এবং Due Amount ক্যালকুলেট হবে।
     * ProductSale তৈরি বা ডিলিট হলে কোয়ান্টিটি ও প্রফিট হিসাব হবে।
     */
    protected static function booted()
    {
        /**
         * ✅ **Creating Stock** — Total Amount এবং Due Amount অটোমেটিক্যালি হিসাব হবে
         */
        static::creating(function (Stock $stock) {
        // ✅ টোটাল এমাউন্ট হিসাব
        $stock->total_amount = $stock->buying_price * $stock->quantity;
        $depositAmount = $stock->deposit_amount ?? 0;
        $stock->due_amount = max($stock->total_amount - $depositAmount, 0);
    });
        /**
         * ✅ **Updating Stock** — Total Amount এবং Due Amount অটোমেটিক্যালি হিসাব হবে
         */
        static::updating(function (Stock $stock) {
        $stock->total_amount = $stock->buying_price * $stock->quantity;
        $depositAmount = $stock->deposit_amount ?? 0;
        $stock->due_amount = max($stock->total_amount - $depositAmount, 0);
    });


          static::created(function (Stock $stock) {
            // ✅ ProductList এ ডেটা কপি হচ্ছে
            \App\Models\ProductList::create([
                'branch_id' => $stock->branch_id,
                'product_name' => $stock->product_name,
                'supplier_name' => $stock->supplier_name,
                'buying_price' => $stock->buying_price,
                'quantity' => $stock->quantity,
                'total_amount' => $stock->total_amount,
                'purchase_date' => $stock->purchase_date,
                'branch_name' => $stock->branch->name ?? '—',
            ]);
        });
        
    }
}
