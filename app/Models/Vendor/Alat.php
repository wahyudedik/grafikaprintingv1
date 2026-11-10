<?php

namespace App\Models\Vendor;

use App\Models\Vendor;
use App\Models\Vendor\Produk;
use App\Models\Vendor\Transaksi;
use App\Models\Vendor\TenantModel;
use App\Models\Vendor\EstimasiProduk;
use Illuminate\Database\Eloquent\Model;

class Alat extends TenantModel
{
    protected $table = 'alats';

    protected $fillable = [
        'vendor_id',
        'nama_alat',
        'merek',
        'model',
        'spesifikasi_alat',
        'status',
        'tanggal_pembelian',
        'kapasitas_cetak_per_jam',
        'tersedia'
    ];

    protected $casts = [
        'tersedia' => 'boolean',
        'tanggal_pembelian' => 'date',
        'kapasitas_cetak_per_jam' => 'integer',
    ];

    // Add these scopes for better filtering capabilities
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('nama_alat', 'like', "%{$term}%")
                ->orWhere('merek', 'like', "%{$term}%")
                ->orWhere('model', 'like', "%{$term}%")
                ->orWhere('spesifikasi_alat', 'like', "%{$term}%");
        });
    }

    public function scopeTersedia($query)
    {
        return $query->where('tersedia', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function getStatusColorAttribute()
    {
        return [
            'aktif' => 'success',
            'maintenance' => 'warning',
            'rusak' => 'danger',
        ][$this->status] ?? 'secondary';
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    public function scopeRusak($query)
    {
        return $query->where('status', 'rusak');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    // public function produks()
    // {
    //     return $this->belongsToMany(Produk::class, 'produk_alat');
    // }

    public function estimasiProduk()
    {
        return $this->hasMany(EstimasiProduk::class, 'alat_id');
    }

    public function checkDailyCapacity($requestedTime)
    {
        // Hitung waktu terjadwal hari ini dari data estimasi produksi alat ini:
        // waktu_persiapan + waktu_produksi_per_unit * kuantitas, untuk transaksi berstatus processing.
        // (Kolom estimated_duration tidak ada di migrasi mana pun.)
        $totalScheduledTime = 0;

        $transaksis = Transaksi::whereDate('estimasi_selesai', today())
            ->where('status', 'processing')
            ->with(['transaksiItem.produk.estimasiProduk' => function ($query) {
                $query->where('alat_id', $this->id);
            }])
            ->get();

        foreach ($transaksis as $transaksi) {
            foreach ($transaksi->transaksiItem as $item) {
                foreach ($item->produk->estimasiProduk ?? [] as $estimasi) {
                    $totalScheduledTime += $estimasi->waktu_persiapan
                        + ($estimasi->waktu_produksi_per_unit * $item->kuantitas);
                }
            }
        }

        $availableMinutes = $this->kapasitas_cetak_per_jam * 60;
        return ($totalScheduledTime + $requestedTime) <= $availableMinutes;
    }

    public function getNextAvailableSlot()
    {
        $lastScheduledJob = Transaksi::whereHas('transaksiItem.produk.estimasiProduk', function ($query) {
            $query->where('alat_id', $this->id);
        })
            ->orderBy('estimasi_selesai', 'desc')
            ->first();

        return $lastScheduledJob ? $lastScheduledJob->estimasi_selesai : now();
    }

    // Tambahan accessor untuk ketersediaan
    public function getAvailabilityLabelAttribute()
    {
        return $this->tersedia
            ? '<span class="badge bg-success-lt">Tersedia</span>'
            : '<span class="badge bg-danger-lt">Tidak Tersedia</span>';
    }
}
