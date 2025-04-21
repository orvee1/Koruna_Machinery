<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('part_stock_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_stock_id')->constrained()->onDelete('cascade');
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->date('payment_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_stock_payments');
    }
};
