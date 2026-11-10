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
        Schema::create('bahan_spesifikasi_produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_id')->constrained('bahans')->cascadeOnDelete();
            $table->foreignId('spesifikasi_produk_id')->constrained('spesifikasi_produks')->cascadeOnDelete();
            $table->timestamps();

            // Ensure unique combination of bahan and spesifikasi_produk
            $table->unique(['bahan_id', 'spesifikasi_produk_id'], 'bahan_spek_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_spesifikasi_produk');
    }
};
