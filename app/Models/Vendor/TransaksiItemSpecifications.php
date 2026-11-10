<?php

namespace App\Models\Vendor;

use App\Models\Vendor;
use App\Models\Vendor\Bahan;
use App\Models\Vendor\TransaksiItem;
use App\Models\Vendor\SpesifikasiProduk;

class TransaksiItemSpecifications extends TenantModel
{
    protected $table = 'transaksi_item_specifications';

    protected $fillable = [
        'vendor_id',
        'transaksi_item_id',
        'spesifikasi_produk_id',
        'bahan_id',
        'value',
        'input_type',
        'price'
    ];

    protected $casts = [
        'price' => 'decimal:2', 
        'vendor_id' => 'integer',
        'transaksi_item_id' => 'integer',
        'spesifikasi_produk_id' => 'integer',
        'bahan_id' => 'integer',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function transaksiItem()
    {
        return $this->belongsTo(TransaksiItem::class, 'transaksi_item_id');
    }

    public function spesifikasiProduk()
    {
        return $this->belongsTo(SpesifikasiProduk::class, 'spesifikasi_produk_id');
    }

    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }
}
