<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Services\TenantManager setVendorId(int $vendorId)
 * @method static \App\Services\TenantManager setVendor(\App\Models\Vendor $vendor)
 * @method static int|null getVendorId()
 * @method static \App\Models\Vendor|null getVendor()
 * @method static bool hasVendorContext()
 * @method static \App\Services\TenantManager clearVendorContext()
 * @method static mixed forVendor(int $vendorId, callable $callback)
 * @method static void applyTenantScope(\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $model)
 * 
 * @see \App\Services\TenantManager
 */
class Tenant extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'tenant';
    }
}
