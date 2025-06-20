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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('receipt')->nullable();
            $table->string('status')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2);
            $table->string('discount_type');
            $table->string('paid_methods');
            $table->decimal('grand_total', 10, 2);
            $table->decimal('paid_amount', 10, 2);
            $table->decimal('change', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
