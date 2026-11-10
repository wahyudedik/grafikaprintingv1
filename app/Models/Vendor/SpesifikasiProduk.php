<?php

namespace App\Models\Vendor;

use App\Models\Vendor;
use App\Models\Vendor\Bahan;
use Illuminate\Database\Eloquent\Builder;

class SpesifikasiProduk extends TenantModel
{
    protected $table = 'spesifikasi_produks';

    protected $fillable = [
        'vendor_id',
        'produk_id',
        'spesifikasi_id',
        'wajib_diisi',
        'pilihan',
    ];

    protected $casts = [
        'pilihan' => 'array',
        'wajib_diisi' => 'boolean',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function spesifikasi()
    {
        return $this->belongsTo(Spesifikasi::class, 'spesifikasi_id');
    }

    public function transaksiItemSpecifications()
    {
        return $this->hasMany(TransaksiItemSpecifications::class, 'spesifikasi_produk_id');
    }

    public function bahanSpesifikasiProduk()
    {
       return $this->belongsToMany(Bahan::class, 'bahan_spesifikasi_produk', 'spesifikasi_produk_id', 'bahan_id')
                  ->withTimestamps();
    }

    public function bahans()
    {
        return $this->bahanSpesifikasiProduk();
    }

    /**
     * Find specification by product and specification ID
     */
    public function scopeFindByProductAndSpec(Builder $query, int $productId, int $specId): Builder
    {
        return $query->where('produk_id', $productId)
                     ->where('spesifikasi_id', $specId);
    }

    /**
     * Calculate price based on selected bahan and quantity
     */
    public function calculatePrice($value, $bahanId, $quantity)
    {
        $bahan = Bahan::find($bahanId);
        if (!$bahan) return 0;

        $basePrice = $bahan->hpp * $value;

        // Apply wholesale pricing if applicable
        return (new WholesalePrice)->calculateFinalPrice($basePrice, $quantity, $bahanId);
    }

    /**
     * Validate specification value
     */
    public function validateSpecificationValue($value)
    {
        switch ($this->spesifikasi->tipe_input) {
            case 'number':
                return is_numeric($value) && $value >= 0;
            case 'select':
                return $this->bahanSpesifikasiProduk->pluck('id')->contains($value);
            case 'text':
                return is_string($value) && !empty($value);
            default:
                return false;
        }
    }
}
