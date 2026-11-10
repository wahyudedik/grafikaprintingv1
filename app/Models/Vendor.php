<?php

namespace App\Models;

use App\Models\User;
use App\Models\Vendor\Alat;
use App\Models\Vendor\Bahan;
use App\Models\Vendor\Produk;
use App\Models\Vendor\Pelanggan;
use App\Models\Vendor\Transaksi;
use App\Models\Vendor\Spesifikasi;
use App\Models\Vendor\TransaksiItem;
use App\Models\Vendor\EstimasiProduk;
use App\Models\Vendor\KategoriProduk;
use App\Models\Vendor\WholesalePrice;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vendor\SpesifikasiProduk;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Vendor\TransaksiItemSpecifications;

class Vendor extends Model
{
    protected $table = 'vendors';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'logo',
        'website',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // Global scope to only show active vendors by default
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', true);
        });
    }

    /**
     * Scope a query to include inactive vendors.
     */
    public function scopeWithInactive(Builder $query): Builder
    {
        return $query->withoutGlobalScope('active');
    }

    /**
     * Scope a query to only include vendors associated with a specific user.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->whereHas('vendorUser', function ($q) use ($userId) {
            $q->where('users.id', $userId);
        });
    }

    /**
     * Scope a query to filter vendors by name.
     */
    public function scopeSearchByName(Builder $query, string $search): Builder
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    /**
     * Define relationship with vendors
     */
    public function vendorUser()
    {
        return $this->belongsToMany(User::class, 'vendor_user');
    }

    public function bahan()
    {
        return $this->hasMany(Bahan::class, 'vendor_id');
    }

    public function alat()
    {
        return $this->hasMany(Alat::class, 'vendor_id');
    }

    public function produk()
    {
        return $this->hasMany(Produk::class, 'vendor_id');
    }

    public function pelanggan()
    {
        return $this->hasMany(Pelanggan::class, 'vendor_id');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'vendor_id');
    }

    public function wholesalePrice()
    {
        return $this->hasMany(WholesalePrice::class, 'vendor_id');
    }

    public function spesifikasiProduk()
    {
        return $this->hasMany(SpesifikasiProduk::class, 'vendor_id');
    }

    public function transaksiItem()
    {
        return $this->hasMany(TransaksiItem::class, 'vendor_id');
    }

    public function kategori()
    {
        return $this->hasMany(KategoriProduk::class, 'vendor_id');
    }

    public function spesifikasi()
    {
        return $this->hasMany(Spesifikasi::class, 'vendor_id');
    }

    public function estimasiProduk()
    {
        return $this->hasMany(EstimasiProduk::class, 'vendor_id');
    }

    public function transaksiItemSpecifications()
    {
        return $this->hasMany(TransaksiItemSpecifications::class, 'vendor_id');
    }
}
