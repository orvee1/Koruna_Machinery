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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('product_name');            
            $table->string('supplier_name');
            $table->decimal('buying_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->integer('quantity')->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->decimal('due_amount', 10, 2);
            $table->date('purchase_date')->nullable();
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
