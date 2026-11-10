<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\TryCatch;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $vendor = Vendor::query();
            if($request->has('search')){
                $search = $request->search;
                $vendor->where(function($query) use ($search){
                    $query->where('name', 'like', "%{$search}%");
                });
            }

            $perPage = $request->get('perPage', 5);
            $vendors = $vendor->paginate($perPage);
            return view('dev.vendors.index', compact('vendors'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('toast_error', 'Something went wrong');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $users = User::get();
            return view('dev.vendors.create', compact('users'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('toast_error', 'Something went wrong');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'logo' => 'image|mimes:jpeg,png|max:2048',
                'website' => 'nullable|string|max:255',
                'is_active' => 'boolean',
                'user_id' => 'required|exists:users,id',
            ]);

            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $logoName = time() . '.' . $logo->getClientOriginalExtension();
                $logo->move(public_path('vendors_logo'), $logoName);
            }

            $vendors = new Vendor([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'logo' => $logoName,
                'website' => $request->website,
                'is_active' => $request->is_active,
            ]);

            $vendors->save();

            $vendors->vendorUser()->attach($request->user_id);

            return redirect()->route('vendors.index')->with('toast_success', 'Vendor created successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('toast_error', 'Something went wrong' . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $vendor = Vendor::findOrFail($id);
            $users = $vendor->vendorUser()->first();
            return view('dev.vendors.show', compact('vendor', 'users'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('toast_error', 'Something went wrong');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $vendor = Vendor::findOrFail($id);
            $users = $vendor->vendorUser()->first();
            return view('dev.vendors.edit', compact('vendor', 'users'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('toast_error', 'Something went wrong');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $vendor = Vendor::findOrFail($id);
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $vendor->id,
                'phone' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'logo' => 'image|mimes:jpeg,png|max:2048',
                'website' => 'nullable|string|max:255',
                'is_active' => 'boolean',
                'user_id' => 'required|exists:users,id',
            ]);

            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($vendor->logo && file_exists(public_path('vendors_logo/' . $vendor->logo))) {
                    unlink(public_path('vendors_logo/' . $vendor->logo));
                }
                
                $logo = $request->file('logo');
                $logoName = time() . '.' . $logo->getClientOriginalExtension();
                $logo->move(public_path('vendors_logo'), $logoName);
            }

            $vendor->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'logo' => $logoName,
                'website' => $request->website,
                'is_active' => $request->is_active,
            ]);

            $vendor->vendorUser()->sync($request->user_id);

            return redirect()->route('vendors.index')->with('toast_success', 'Vendor updated successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('toast_error', 'Something went wrong' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $vendor = Vendor::findOrFail($id);
            if ($vendor->logo && file_exists(public_path('vendors_logo/' . $vendor->logo))) {
                unlink(public_path('vendors_logo/' . $vendor->logo));
            }
            $vendor->delete();
            return redirect()->route('vendors.index')->with('toast_success', 'Vendor deleted successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('toast_error', 'Something went wrong');
        }
    }
}
 