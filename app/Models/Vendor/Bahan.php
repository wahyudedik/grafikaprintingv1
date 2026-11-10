<?php

namespace App\Models\Vendor;

use App\Models\Vendor;
use App\Models\Vendor\TenantModel;
use Illuminate\Support\Facades\Log;
use App\Models\Vendor\WholesalePrice;
use Illuminate\Database\Eloquent\Builder;

class Bahan extends TenantModel
{
    protected $table = 'bahans';

    protected $fillable = [
        'vendor_id',
        'nama_bahan',
        'hpp',
        'satuan',
        'stok'
    ];

    protected $casts = [
        'hpp' => 'decimal:2',
        'stok' => 'integer'
    ];

    /**
     * Relationships
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function wholesalePrices()
    {
        return $this->hasMany(WholesalePrice::class, 'bahan_id');
    }

    // In app/Models/Vendor/Bahan.php
    public function wholesalePrice()
    {
        return $this->wholesalePrices();
    }

    /**
     * Scopes
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function ($query, $search) {
            return $query->where(function ($query) use ($search) {
                $query->where('nama_bahan', 'like', "%{$search}%")
                    ->orWhere('satuan', 'like', "%{$search}%");
            });
        });
    }

    public function scopeStockFilter(Builder $query, ?string $stockStatus): Builder
    {
        return $query->when($stockStatus, function ($query, $stockStatus) {
            return match ($stockStatus) {
                'low' => $query->where('stok', '<', 10)->where('stok', '>', 0),
                'out' => $query->where('stok', '=', 0),
                'available' => $query->where('stok', '>', 0),
                default => $query
            };
        });
    }

    public function scopeWholesaleFilter(Builder $query, ?string $hasWholesale): Builder
    {
        return $query->when($hasWholesale, function ($query, $hasWholesale) {
            return match ($hasWholesale) {
                'yes' => $query->whereHas('wholesalePrices'),
                'no' => $query->whereDoesntHave('wholesalePrices'),
                default => $query
            };
        });
    }

    /**
     * Helper methods
     */
    public function getPriceForQuantity(int $quantity): float
    {
        $wholesalePrice = $this->wholesalePrices()
            ->where('min_quantity', '<=', $quantity)
            ->where(function ($query) use ($quantity) {
                $query->where('max_quantity', '>=', $quantity)
                    ->orWhereNull('max_quantity');
            })
            ->orderBy('min_quantity', 'desc')
            ->first();

        return $wholesalePrice ? $wholesalePrice->harga : $this->hpp;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stok == 0) {
            return 'out';
        } elseif ($this->stok < 10) {
            return 'low';
        } else {
            return 'available';
        }
    }

    public function getStockStatusLabelAttribute(): string
    {
        return match ($this->stock_status) {
            'out' => '<span class="badge bg-danger text-white">Habis</span>',
            'low' => '<span class="badge bg-warning text-white">Rendah (' . $this->stok . ')</span>',
            default => '<span class="badge bg-success text-white">' . $this->stok . '</span>',
        };
    }

    /**
     * Check if the stock level is low and update status accordingly
     *
     * @return void
     */
    public function checkStockLevel()
    {
        // Assuming you have a 'stok' column in your bahans table
        // If you have a minimum_stok field, use that, otherwise use a default value
        $minimumStock = $this->minimum_stok ?? 5; // Default minimum stock level is 5

        if ($this->stok <= $minimumStock) {
            // If you have a status field, update it
            if (isset($this->attributes['status'])) {
                $this->status = 'low_stock';
                $this->save();
            }

            // You could log low stock situations
            \Illuminate\Support\Facades\Log::warning("Low stock alert: {$this->nama_bahan} (ID: {$this->id}) - Current stock: {$this->stok}");
        }
    }
}
