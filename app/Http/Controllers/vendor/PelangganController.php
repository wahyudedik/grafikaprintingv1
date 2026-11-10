<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pelanggan::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama', 'like', "%{$searchTerm}%")
                    ->orWhere('kode', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('no_telp', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by transaksi status if provided
        if ($request->has('status') && !empty($request->status)) {
            if ($request->status === 'active') {
                $query->whereHas('transaksi');
            } elseif ($request->status === 'inactive') {
                $query->whereDoesntHave('transaksi');
            }
        }

        // Use with() to load the latest transaction for each customer
        $query->withCount('transaksi');

        $pelanggan = $query->latest()->paginate(10);

        // Update transaksi_terakhir for display purposes
        foreach ($pelanggan as $customer) {
            if (!$customer->transaksi_terakhir && $customer->transaksi_count > 0) {
                $customer->transaksi_terakhir = $customer->getLatestTransactionDate();
            }
        }

        return view('pelanggan.index', compact('pelanggan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pelanggan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        // Generate a unique customer code
        $validated['kode'] = 'C' . strtoupper(Str::random(6));

        // Set vendor_id from authenticated user's vendor
        $validated['vendor_id'] = Auth::user()->vendor_id;

        $pelanggan = Pelanggan::create($validated);

        return redirect()->route('pelanggan.index')
            ->with('toast_success', 'Pelanggan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        return view('pelanggan.show', compact('pelanggan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        return view('pelanggan.edit', compact('pelanggan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        // Ensure we don't change the vendor_id
        $pelanggan->update($validated);

        return redirect()->route('pelanggan.index')
            ->with('toast_success', 'Pelanggan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        // Check if the customer has any transactions
        if ($pelanggan->transaksi()->count() > 0) {
            return redirect()->route('pelanggan.index')
                ->with('toast_error', 'Pelanggan tidak dapat dihapus karena memiliki transaksi.');
        }

        $pelanggan->delete();

        return redirect()->route('pelanggan.index')
            ->with('toast_success', 'Pelanggan berhasil dihapus.');
    }
}
