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
        Schema::create('products', function (Blueprint $table) {
            $table->ulid('id')->primary();
            // $table->string('factory_name')->nullable();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('product_name')->nullable();
            $table->string('type');
            $table->string('product_code');
            $table->string('barcode');
            $table->string('unit');
            $table->integer('purchase_price');
            $table->integer('selling_price');
            $table->string('category');
            $table->string('shelf');
            $table->integer('stock');
            $table->integer('min_stock')->nullable();
            $table->enum('status', ['Dijual', 'Tidak Dijual'])->default('Dijual');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
