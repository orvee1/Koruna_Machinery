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
        Schema::create('part_stocks', function (Blueprint $table) {
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->id();
            $table->string('product_name');
            $table->string('supplier_name');
            $table->decimal('buy_value', 10, 2);
            $table->integer('quantity')->default(0);
            $table->decimal('amount', 10, 2);
            $table->decimal('deposit_amount', 10, 2);
            $table->decimal('due_amount', 10, 2);
            $table->decimal('sell_value', 10, 2);
            $table->decimal('total_profit', 10, 2);
            $table->date('purchase_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_stocks');
    }
};
