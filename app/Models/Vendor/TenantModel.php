<?php

namespace App\Models\Vendor;

use App\Facades\Tenant;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

abstract class TenantModel extends Model
{
    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->vendor_id) {
                $vendorId = Tenant::getVendorId();

                if ($vendorId) {
                    $model->vendor_id = $vendorId;
                } else {
                    Log::error('Failed to create tenant model: No vendor context', [
                        'model' => get_class($model),
                        'data' => $model->toArray(),
                        'user_id' => Auth::id() ?? 'none'
                    ]);

                    throw new \Exception('Cannot create tenant model without vendor_id. Please contact support.');
                }
            }
        });

        static::saving(function ($model) {
            // Prevent vendor_id from being changed or nullified
            if ($model->isDirty('vendor_id') && $model->exists) {
                $model->vendor_id = $model->getOriginal('vendor_id');
            }

            // Ensure vendor_id is set
            if (!$model->vendor_id) {
                $vendorId = Tenant::getVendorId();

                if ($vendorId) {
                    $model->vendor_id = $vendorId;
                } else {
                    throw new \Exception('Cannot save tenant model without vendor_id');
                }
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder) {
            $vendorId = Tenant::getVendorId();

            if ($vendorId) {
                $builder->where('vendor_id', $vendorId);
            }
        });
    }
}
