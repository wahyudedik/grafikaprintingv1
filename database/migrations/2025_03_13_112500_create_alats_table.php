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
        Schema::create('alats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('nama_alat');
            $table->string('merek')->nullable();
            $table->string('model')->nullable();
            $table->text('spesifikasi_alat')->nullable();
            $table->enum('status', ['aktif', 'maintenance', 'rusak']);
            $table->date('tanggal_pembelian');
            $table->integer('kapasitas_cetak_per_jam');
            $table->boolean('tersedia')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alats');
    }
};
