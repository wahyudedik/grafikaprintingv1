<?php

namespace App\Models\Vendor;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Vendor\Pelanggan;
use Illuminate\Support\Facades\DB;
use App\Models\Vendor\TransaksiItem;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\OrderStatusChanged;
use App\Models\Vendor\TransaksiItemSpecifications;

class Transaksi extends TenantModel
{
    protected $table = 'transaksis';
    
    protected $fillable = [
        'vendor_id',
        'kode',
        'user_id',
        'pelanggan_id',
        'total_harga',
        'terbayar',
        'kembali',
        'status',
        'payment_method',
        'estimasi_selesai',
        'tanggal_dibuat',
        'progress_percentage',
        'catatan'
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'terbayar' => 'decimal:2',
        'kembali' => 'decimal:2',
        'status' => 'string',
        'tanggal_dibuat' => 'datetime', 
        'estimasi_selesai' => 'datetime',
        'progress_percentage' => 'integer'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public function transaksiItem()
    {
        return $this->hasMany(TransaksiItem::class, 'transaksi_id');
    }

    public function transaksiItemSpecifications()
    {
        return $this->hasManyThrough(
            TransaksiItemSpecifications::class,
            TransaksiItem::class,
            'transaksi_id',
            'transaksi_item_id'
        );
    }

    /**
     * Scope a query to filter transactions by status.
     */
    public function scopeWithStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter transactions by date range.
     */
    public function scopeWithinDateRange(Builder $query, $startDate, $endDate): Builder
    {
        if ($startDate) {
            $query->whereDate('tanggal_dibuat', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('tanggal_dibuat', '<=', $endDate);
        }
        
        return $query;
    }

    /**
     * Scope a query to search transactions by code or customer.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('kode', 'like', "%{$search}%")
                ->orWhereHas('pelanggan', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%");
                });
        });
    }

    protected static function booted()
    {
        parent::booted();

        static::creating(function ($transaksi) {
            // Set current date if not provided
            if (!$transaksi->tanggal_dibuat) {
                $transaksi->tanggal_dibuat = now();
            }
            
            // Set default status and progress if not set
            if (!$transaksi->status) {
                $transaksi->status = 'pending';
            }
            
            if (!$transaksi->progress_percentage) {
                $transaksi->progress_percentage = 0;
            }
        });

        static::updating(function ($transaksi) {
            // Make sure progress percentage matches status if status is being updated
            if ($transaksi->isDirty('status')) {
                $progressMap = [
                    'pending' => 0,
                    'processing' => 25,
                    'quality_check' => 80,
                    'completed' => 100,
                    'cancelled' => 0
                ];
                
                $transaksi->progress_percentage = $progressMap[$transaksi->status] ?? 0;
            }
        });
    }

    public function updateOrderStatus($status)
    {
        $progressMap = [
            'pending' => 0,
            'processing' => 25,
            'quality_check' => 80,
            'completed' => 100,
            'cancelled' => 0
        ];

        DB::transaction(function () use ($status, $progressMap) {
            $this->forceFill([
                'status' => $status,
                'progress_percentage' => $progressMap[$status]
            ])->save();

            $this->refresh();
            
            // Only notify if the customer relationship exists
            if ($this->pelanggan) {
                // Only attempt to notify if the OrderStatusChanged class exists
                if (class_exists('App\Notifications\OrderStatusChanged')) {
                    $this->pelanggan->notify(new OrderStatusChanged($this));
                }
            }
        });
    }
}
