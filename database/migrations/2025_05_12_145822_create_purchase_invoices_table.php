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
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('faktur_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_id')->constrained()->cascadeOnDelete();
            $table->string('no_surat_pesan')->nullable();
            $table->string('no_faktur');
            $table->date('tanggal');
            $table->date('tgl_penerimaan')->nullable();
            // $table->enum('jenis_faktur', ['tunai', 'kredit']);
            $table->string('jenis_faktur')->nullable();
            // $table->string('gudang');
            $table->enum('jenis_pembayaran', ['Tunai', 'Kredit']);
            $table->integer('tempo_bayar')->nullable();
            $table->date('jatuh_tempo')->nullable();
            $table->decimal('discount', 10, 2)->default(0);
            $table->string('status')->default('process');
            $table->decimal('ppn', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoices');
    }
};
