<?php

namespace App\Http\Controllers\vendor;

use App\Models\Vendor\Bahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Vendor\WholesalePrice;

class BahanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $bahan = Bahan::search($request->search)
                      ->stockFilter($request->stok)
                      ->wholesaleFilter($request->has_wholesale)
                      ->latest()
                      ->paginate(10);

        return view('bahan.index', compact('bahan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('bahan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateBahan($request);

        return DB::transaction(function() use ($request, $validated) {
            // Add vendor_id to validated data
            $validated['vendor_id'] = session('current_vendor_id');

            // Create bahan
            $bahan = Bahan::create($validated);

            // Process wholesale prices
            $this->processWholesalePrices($bahan->id, $request);

            return redirect()->route('bahan.index')
                ->with('toast_success', 'Bahan berhasil ditambahkan!');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $bahan = Bahan::with('wholesalePrices')->findOrFail($id);
        return view('bahan.show', compact('bahan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $bahan = Bahan::with('wholesalePrices')->findOrFail($id);
        return view('bahan.edit', compact('bahan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $this->validateBahan($request);

        return DB::transaction(function() use ($request, $validated, $id) {
            $bahan = Bahan::findOrFail($id);
            $bahan->update($validated);

            // Update existing wholesale prices
            $this->updateExistingWholesalePrices($request);

            // Add new wholesale prices
            $this->addNewWholesalePrices($bahan->id, $request);

            // Delete removed wholesale prices
            $this->deleteRemovedWholesalePrices($request);

            return redirect()->route('bahan.index')
                ->with('toast_success', 'Bahan berhasil diperbarui!');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return DB::transaction(function() use ($id) {
            $bahan = Bahan::findOrFail($id);

            // Delete associated wholesale prices
            $bahan->wholesalePrices()->delete();

            // Delete the bahan
            $bahan->delete();

            return redirect()->route('bahan.index')
                ->with('toast_success', 'Bahan berhasil dihapus!');
        });
    }

    /**
     * Delete a wholesale price tier
     */
    public function deleteWholesalePrice(string $id)
    {
        // Scoped vendor melalui global scope TenantModel pada WholesalePrice
        $wholesale = WholesalePrice::findOrFail($id);
        $wholesale->delete();

        return redirect()->back()
            ->with('toast_success', 'Harga grosir berhasil dihapus!');
    }

    /**
     * Validate bahan data
     */
    private function validateBahan(Request $request)
    {
        return $request->validate([
            'nama_bahan' => 'required|string|max:255',
            'hpp' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:50',
            'stok' => 'required|numeric|min:0',
        ]);
    }

    /**
     * Process wholesale prices for create
     */
    private function processWholesalePrices($bahanId, $request)
    {
        if (!$request->has('wholesale_min_qty') || !is_array($request->wholesale_min_qty)) {
            return;
        }

        $wholesaleData = [];

        foreach ($request->wholesale_min_qty as $key => $min_qty) {
            if (empty($min_qty) || !isset($request->wholesale_price[$key])) {
                continue;
            }

            $wholesaleData[] = [
                'vendor_id' => session('current_vendor_id'),
                'bahan_id' => $bahanId,
                'min_quantity' => $min_qty,
                'max_quantity' => $request->wholesale_max_qty[$key] ?? null,
                'harga' => $request->wholesale_price[$key],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($wholesaleData)) {
            WholesalePrice::insert($wholesaleData);
        }
    }

    /**
     * Update existing wholesale prices
     */
    private function updateExistingWholesalePrices($request)
    {
        if (!$request->has('wholesale_id') || !is_array($request->wholesale_id)) {
            return;
        }

        foreach ($request->wholesale_id as $key => $wholesale_id) {
            if (empty($wholesale_id)) {
                continue;
            }

            $wholesale = WholesalePrice::find($wholesale_id);
            if (!$wholesale) {
                continue;
            }

            $wholesale->update([
                'min_quantity' => $request->wholesale_min_qty[$key],
                'max_quantity' => $request->wholesale_max_qty[$key],
                'harga' => $request->wholesale_price[$key],
            ]);
        }
    }

    /**
     * Add new wholesale prices
     */
    private function addNewWholesalePrices($bahanId, $request)
    {
        if (!$request->has('new_wholesale_min_qty') || !is_array($request->new_wholesale_min_qty)) {
            return;
        }

        foreach ($request->new_wholesale_min_qty as $key => $min_qty) {
            if (empty($min_qty) || !isset($request->new_wholesale_price[$key])) {
                continue;
            }

            WholesalePrice::create([
                'vendor_id' => session('current_vendor_id'),
                'bahan_id' => $bahanId,
                'min_quantity' => $min_qty,
                'max_quantity' => $request->new_wholesale_max_qty[$key] ?? null,
                'harga' => $request->new_wholesale_price[$key],
            ]);
        }
    }

    /**
     * Delete removed wholesale prices
     */
    private function deleteRemovedWholesalePrices($request)
    {
        if (!$request->has('deleted_wholesale_ids') || empty($request->deleted_wholesale_ids)) {
            return;
        }

        $deleted_ids = explode(',', $request->deleted_wholesale_ids);
        WholesalePrice::whereIn('id', $deleted_ids)->delete();
    }
}
