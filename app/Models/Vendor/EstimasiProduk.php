<?php

namespace App\Models\Vendor;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Builder;

class EstimasiProduk extends TenantModel
{
    protected $table = 'estimasi_produks';

    protected $fillable = [
        'vendor_id',
        'produk_id',
        'alat_id',
        'waktu_persiapan',
        'waktu_produksi_per_unit'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function alat()
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }

    /**
     * Scope to find by product and equipment
     */
    public function scopeFindByProductAndEquipment(Builder $query, int $productId, int $equipmentId): Builder
    {
        return $query->where('produk_id', $productId)
                     ->where('alat_id', $equipmentId);
    }

    /**
     * Calculate total production time
     */
    public function calculateTotalProductionTime($quantity, $area = null)
    {
        $setupTime = $this->waktu_persiapan;
        $productionTimePerUnit = $this->waktu_produksi_per_unit;

        // Get all equipment workload
        $equipmentWorkloads = $this->produk->estimasiProduk->map(function ($estimasi) {
            return [
                'alat' => $estimasi->alat,
                'workload' => $estimasi->getWorkloadMultiplier($estimasi->alat)
            ];
        });

        // Calculate maximum workload factor
        $maxWorkload = $equipmentWorkloads->max('workload') ?? 1;

        if ($area) {
            return ($setupTime + ($area * $productionTimePerUnit * $quantity)) * $maxWorkload;
        }

        return ($setupTime + ($productionTimePerUnit * $quantity)) * $maxWorkload;
    }

    /**
     * Get workload multiplier based on active jobs
     */
    private function getWorkloadMultiplier($alat)
    {
        $activeJobs = Transaksi::where('status', 'processing')
            ->whereHas('transaksiItem.produk.estimasiProduk', function ($query) use ($alat) {
                $query->where('alat_id', $alat->id);
            })->count();

        return 1 + ($activeJobs * 0.1); // 10% increase per active job
    }
}
