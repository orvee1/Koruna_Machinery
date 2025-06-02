<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'branch_id',
        'seller_id',
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_status',
        'product_details',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'product_details' => 'array',
    ];

    protected static function booted()
{
    // When bill is created → reduce quantity & add profit
    static::created(function (Bill $bill) {
          $details = $bill->product_details ?? [];

        foreach ($details as $item) {
            $type = $item['type'];
            $id = $item['id'];
            $quantity = $item['quantity'];
            $unitPrice = $item['unit_price'];

            if ($type === 'product') {
                $stock = \App\Models\Stock::find($id);
                if ($stock) {
                    $stock->quantity = max(0, $stock->quantity - $quantity);
                    $stock->total_amount = $stock->buying_price * $stock->quantity;
                    $stock->total_profit += ($unitPrice - $stock->buying_price) * $quantity;
                    $stock->saveQuietly();
                }
            } elseif ($type === 'partstock') {
                $part = \App\Models\PartStock::find($id);
                if ($part) {
                    $part->quantity = max(0, $part->quantity - $quantity);
                    $part->total_amount = $part->buying_price * $part->quantity;
                    $part->total_profit += ($unitPrice - $part->buying_price) * $quantity;
                    $part->saveQuietly();
                }
            }
        }
    });

    // When bill is deleted → restore quantity & revert profit
    static::deleting(function (Bill $bill) {
          $details = $bill->product_details ?? [];

        foreach ($details as $item) {
            $type = $item['type'];
            $id = $item['id'];
            $quantity = $item['quantity'];
            $unitPrice = $item['unit_price'];

            if ($type === 'product') {
                $stock = \App\Models\Stock::find($id);
                if ($stock) {
                    $stock->quantity += $quantity;
                    $stock->total_amount = $stock->buying_price * $stock->quantity;
                    $stock->total_profit = max(0, $stock->total_profit - (($unitPrice - $stock->buying_price) * $quantity));
                    $stock->saveQuietly();
                }
            } elseif ($type === 'partstock') {
                $part = \App\Models\PartStock::find($id);
                if ($part) {
                    $part->quantity += $quantity;
                    $part->total_amount = $part->buying_price * $part->quantity;
                    $part->total_profit = max(0, $part->total_profit - (($unitPrice - $part->buying_price) * $quantity));
                    $part->saveQuietly();
                }
            }
        }
    });
}


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function payments()
    {
        return $this->hasMany(BillPayment::class);
    }
}
