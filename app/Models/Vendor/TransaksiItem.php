<?php

namespace App\Models\Vendor;

use App\Models\Vendor;
use App\Models\Vendor\Bahan;
use App\Models\Vendor\Produk;
use App\Models\Vendor\Transaksi;
use App\Models\Vendor\TransaksiItemSpecifications;

class TransaksiItem extends TenantModel
{
    protected $table = 'transaksi_items';

    protected $fillable = [
        'vendor_id',
        'transaksi_id',
        'produk_id',
        'kuantitas',
        'harga_satuan'
    ];

    protected $casts = [
        'kuantitas' => 'integer',
        'harga_satuan' => 'decimal:2', 
        'vendor_id' => 'integer',
        'transaksi_id' => 'integer',
        'produk_id' => 'integer'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function transaksiItemSpecifications()
    {
        return $this->hasMany(TransaksiItemSpecifications::class, 'transaksi_item_id');
    }

    public function getSubtotalAttribute()
    {
        return $this->kuantitas * $this->harga_satuan;
    }

    protected static function booted()
    {
        parent::booted();
        
        static::deleting(function ($transaksiItem) {
            // Delete related specifications when item is deleted
            $transaksiItem->transaksiItemSpecifications()->delete();
        });
    }
}
