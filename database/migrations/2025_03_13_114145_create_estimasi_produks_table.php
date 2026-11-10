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
        Schema::create('estimasi_produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('produk_id')->constrained('produks')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('alat_id')->constrained('alats')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('waktu_persiapan');
            $table->integer('waktu_produksi_per_unit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimasi_produks');
    }
};
