<?php

namespace App\Services;

use App\Models\Vendor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class TenantManager
{
    /**
     * The current vendor ID.
     *
     * @var int|null
     */
    protected $currentVendorId = null;

    /**
     * The current vendor instance.
     *
     * @var \App\Models\Vendor|null
     */
    protected $currentVendor = null;

    /**
     * Set the current vendor ID.
     *
     * @param int $vendorId
     * @return $this
     */
    public function setVendorId(int $vendorId)
    {
        $this->currentVendorId = $vendorId;
        $this->currentVendor = null; // Reset cached vendor

        // Also set in session as fallback
        session(['current_vendor_id' => $vendorId]);

        Log::info('Tenant context set', ['vendor_id' => $vendorId]);

        return $this;
    }

    /**
     * Set the current vendor using a Vendor model.
     *
     * @param \App\Models\Vendor $vendor
     * @return $this
     */
    public function setVendor(Vendor $vendor)
    {
        $this->currentVendor = $vendor;
        $this->currentVendorId = $vendor->id;

        // Also set in session as fallback
        session(['current_vendor_id' => $vendor->id]);

        Log::info('Tenant context set', ['vendor_id' => $vendor->id, 'vendor_name' => $vendor->name]);

        return $this;
    }

    /**
     * Get the current vendor ID.
     *
     * @return int|null
     */
    public function getVendorId()
    {
        // If we have it in memory, use that first
        if ($this->currentVendorId) {
            return $this->currentVendorId;
        }

        // Try to get from session as fallback
        if (session()->has('current_vendor_id')) {
            $this->currentVendorId = session('current_vendor_id');
            return $this->currentVendorId;
        }

        // Log when no vendor ID is found
        Log::warning('No vendor ID found in TenantManager', [
            'user_id' => Auth::id() ?? 'none',
            'url' => request()->fullUrl() ?? 'unknown'
        ]);

        return null;
    }


    /**
     * Get the current vendor.
     *
     * @return \App\Models\Vendor|null
     */
    public function getVendor()
    {
        if ($this->currentVendor) {
            return $this->currentVendor;
        }

        $vendorId = $this->getVendorId();

        if ($vendorId) {
            $this->currentVendor = Vendor::withoutGlobalScopes()->find($vendorId);
            return $this->currentVendor;
        }

        return null;
    }

    /**
     * Check if a vendor context is set.
     *
     * @return bool
     */
    public function hasVendorContext()
    {
        return $this->getVendorId() !== null;
    }

    /**
     * Clear the current vendor context.
     *
     * @return $this
     */
    public function clearVendorContext()
    {
        $this->currentVendorId = null;
        $this->currentVendor = null;

        session()->forget('current_vendor_id');

        Log::info('Tenant context cleared');

        return $this;
    }

    /**
     * Execute a callback within a specific vendor context.
     *
     * @param int $vendorId
     * @param callable $callback
     * @return mixed
     */
    public function forVendor(int $vendorId, callable $callback)
    {
        // Save current context
        $previousVendorId = $this->getVendorId();

        // Set new context
        $this->setVendorId($vendorId);

        try {
            // Execute the callback
            return $callback();
        } finally {
            // Restore previous context
            if ($previousVendorId) {
                $this->setVendorId($previousVendorId);
            } else {
                $this->clearVendorContext();
            }
        }
    }

    /**
     * Apply vendor scope to a model or query builder.
     *
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $model
     * @return void
     */
    public function applyTenantScope($model)
    {
        $vendorId = $this->getVendorId();

        if (!$vendorId) {
            Log::warning('Attempting to apply tenant scope without vendor context');
            return;
        }

        if ($model instanceof Model) {
            $model->vendor_id = $vendorId;
        } else {
            $model->where('vendor_id', $vendorId);
        }
    }
}
