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
        Schema::create('spesifikasi_produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('produk_id')->constrained('produks')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('spesifikasi_id')->constrained('spesifikasis')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('wajib_diisi');
            $table->json('pilihan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spesifikasi_produks');
    }
};
