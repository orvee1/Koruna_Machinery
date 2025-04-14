<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name', 'buy_value', 'quantity', 'amount', 'sell_value', 'total_profit', 'product_id', 'branch_id'
    ];

    // Relationship with product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relationship with branch
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function calculateTotalPurchaseAmount($buy_value, $quantity)
    {
        $this->buy_value = $buy_value;
        $this->quantity = $quantity;
        $this->total_purchase_amount = $buy_value * $quantity;
        $this->save();
    }

    public function calculateAmountAndProfit()
    {
        // Calculate the total amount based on buying value and quantity
        $this->amount = $this->buy_value * $this->quantity;

        // Calculate the total profit based on selling value and buying value
        $this->total_profit = ($this->sell_value - $this->buy_value) * $this->quantity;

        // Save the values in the database
        $this->save();
    }
}
