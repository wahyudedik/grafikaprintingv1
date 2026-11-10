<?php

namespace App\Models\Vendor;

use App\Models\Vendor;
use App\Models\Vendor\TenantModel;
use App\Models\Vendor\EstimasiProduk;
use App\Models\Vendor\KategoriProduk;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vendor\SpesifikasiProduk;
use Illuminate\Database\Eloquent\Builder;

class Produk extends TenantModel
{
    protected $table = 'produks';

    protected $fillable = [
        'vendor_id',
        'gambar',
        'nama_produk',
        'deskripsi',
        'kategori_id',
    ];

    protected $casts = [
        'gambar' => 'array',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_id');
    }

    public function spesifikasiProduk()
    {
        return $this->hasMany(SpesifikasiProduk::class, 'produk_id');
    }

    public function estimasiProduk()
    {
        return $this->hasMany(EstimasiProduk::class, 'produk_id');
    }

    /**
     * Scope a query to search for products by name or description
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function ($query, $search) {
            return $query->where(function ($query) use ($search) {
                $query->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        });
    }

    /**
     * Scope a query to filter products by category
     */
    public function scopeInCategory(Builder $query, ?int $categoryId): Builder
    {
        return $query->when($categoryId, function ($query, $categoryId) {
            return $query->where('kategori_id', $categoryId);
        });
    }

    /**
     * Get formatted specification list
     */
    public function getSpesifikasiListAttribute()
    {
        $specs = [];
        $this->spesifikasiProduk->each(function ($spek) use (&$specs) {
            $specs[$spek->spesifikasi->nama_spesifikasi] = [
                'tipe' => $spek->spesifikasi->tipe_input,
                'pilihan' => $spek->pilihan,
                'wajib' => $spek->wajib_diisi,
                'bahans' => $spek->bahanSpesifikasiProduk->pluck('id')->toArray()
            ];
        });
        return $specs;
    }

    /**
     * Calculate estimated production time
     */
    public function getEstimatedProductionTime($quantity)
    {
        $totalTime = 0;

        // Calculate time for each equipment process
        $this->estimasiProduk->each(function ($estimasi) use (&$totalTime, $quantity) {
            $totalTime += $estimasi->calculateTotalProductionTime($quantity);
        });

        return $totalTime;
    }
}
