<?php

namespace App\Models\Vendor;

use App\Models\Vendor;
use App\Models\Vendor\Transaksi;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends TenantModel
{
    protected $table = 'pelanggans';

    protected $fillable = [
        'vendor_id',
        'kode',
        'nama',
        'alamat',
        'no_telp',
        'email',
        'transaksi_terakhir'
    ];

    protected $casts = [
        'transaksi_terakhir' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'pelanggan_id');
    }

    /**
     * Get the date of the latest transaction for this customer
     *
     * @return \Carbon\Carbon|null
     */
    public function getLatestTransactionDate()
    {
        $latestTransaction = $this->transaksi()->latest('created_at')->first();
        return $latestTransaction ? $latestTransaction->created_at : null;
    }
}
