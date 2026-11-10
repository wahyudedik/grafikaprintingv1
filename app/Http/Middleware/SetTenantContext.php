<?php

namespace App\Http\Middleware;

use Closure;
use App\Facades\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetTenantContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user()->vendorUser;

            // Get the first vendor associated with the user
            $vendor = $user->first();

            if ($vendor) {
                // Set the tenant context
                Tenant::setVendor($vendor);
            } else if ($user->usertype === 'dev') {
                // Developers might not have a specific vendor context
                // You could handle this differently if needed
            } else {
                // No vendor context available
                abort(403, 'No vendor context available for this user');
            }
        }

        return $next($request);
    }
}
