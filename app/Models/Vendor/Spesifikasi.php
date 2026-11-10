<?php

namespace App\Models\Vendor;

use App\Models\Vendor;
use App\Models\Vendor\TenantModel;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vendor\SpesifikasiProduk;

class Spesifikasi extends TenantModel
{
    protected $table = 'spesifikasis';

    protected $fillable = [
        'vendor_id',
        'nama_spesifikasi',
        'tipe_input',
        'satuan'
    ];

    const TIPE_INPUT = [
        'number' => 'number',
        'select' => 'select',
        'text' => 'text'
    ];

    protected $casts = [
        'nama_spesifikasi' => 'string',
        'tipe_input' => 'string',
        'satuan' => 'string'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function spesifikasiProduk()
    {
        return $this->hasMany(SpesifikasiProduk::class, 'spesifikasi_id');
    }

    public function isNumeric()
    {
        return $this->tipe_input === self::TIPE_INPUT['number'];
    }

    public function isText()
    {
        return $this->tipe_input === self::TIPE_INPUT['text'];
    }

    public function isSelect()
    {
        return $this->tipe_input === self::TIPE_INPUT['select'];
    }
}
