<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $vendor = $user->vendorUser()->first();

        return view('profile.edit', [
            'user' => $user,
            'vendor' => $vendor,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        // Update user profile
        $user = $request->user();
        $user->name = $validated['name'];
        
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        
        $user->save();
        
        // Update vendor profile
        $vendor = $user->vendorUser()->first();
        if ($vendor) {
            $vendorData = [
                'name' => $validated['vendor_name'],
                'email' => $validated['vendor_email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'website' => $validated['website'] ?? null,
            ];
            
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($vendor->logo && file_exists(public_path('vendors_logo/' . $vendor->logo))) {
                    unlink(public_path('vendors_logo/' . $vendor->logo));
                }

                $logo = $request->file('logo');
                $logoName = time() . '.' . $logo->getClientOriginalExtension();
                $logo->move(public_path('vendors_logo'), $logoName);
                $vendorData['logo'] = $logoName;
            }
            
            $vendor->update($vendorData);
            
            return Redirect::route('profile.edit')->with('status', 'vendor-profile-updated');
        }
        
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
