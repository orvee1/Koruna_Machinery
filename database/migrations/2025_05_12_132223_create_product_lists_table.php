<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('product_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('product_name');
            $table->string('supplier_name');
            $table->decimal('buying_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('total_amount', 10, 2);
            $table->date('purchase_date');
            $table->string('branch_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_lists');
    }
};
