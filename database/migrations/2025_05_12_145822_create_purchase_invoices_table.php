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
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('no_surat_pesan')->nullable();
            $table->string('no_faktur');
            $table->date('tanggal');
            $table->date('tgl_penerimaan')->nullable();
            $table->enum('jenis_faktur', ['tunai', 'kredit']);
            $table->string('gudang');
            $table->enum('jenis_pembayaran', ['cash', 'transfer']);
            $table->integer('tempo_bayar')->nullable();
            $table->date('jatuh_tempo')->nullable();
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
