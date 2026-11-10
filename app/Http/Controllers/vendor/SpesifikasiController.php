<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor\Spesifikasi;
use Illuminate\Http\Request;

class SpesifikasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Spesifikasi::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where('nama_spesifikasi', 'like', '%' . $request->search . '%');
        }

        // Filter by tipe_input
        if ($request->has('tipe_input') && !empty($request->tipe_input)) {
            $query->where('tipe_input', $request->tipe_input);
        }

        $spesifikasi = $query->paginate(10);
        return view('spesifikasi.index', compact('spesifikasi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tipeInput = Spesifikasi::TIPE_INPUT;
        return view('spesifikasi.create', compact('tipeInput'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_spesifikasi' => 'required|string|max:255',
            'tipe_input' => 'required|in:' . implode(',', Spesifikasi::TIPE_INPUT),
            'satuan' => 'nullable|string|max:50',
        ]);

        // Set default empty string for satuan when not provided
        if (!isset($validated['satuan'])) {
            $validated['satuan'] = '';
        }

        Spesifikasi::create($validated);

        return redirect()->route('spesifikasi.index')
            ->with('toast_success', 'Spesifikasi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $spesifikasi = Spesifikasi::findOrFail($id);
        return view('spesifikasi.show', compact('spesifikasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $spesifikasi = Spesifikasi::findOrFail($id);
        $tipeInput = Spesifikasi::TIPE_INPUT;
        return view('spesifikasi.edit', compact('spesifikasi', 'tipeInput'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $spesifikasi = Spesifikasi::findOrFail($id);

        $validated = $request->validate([
            'nama_spesifikasi' => 'required|string|max:255',
            'tipe_input' => 'required|in:' . implode(',', Spesifikasi::TIPE_INPUT),
            'satuan' => 'nullable|string|max:50',
        ]);

        // Set default empty string for satuan when not provided
        if (!isset($validated['satuan'])) {
            $validated['satuan'] = '';
        }

        $spesifikasi->update($validated);

        return redirect()->route('spesifikasi.index')
            ->with('toast_success', 'Spesifikasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $spesifikasi = Spesifikasi::findOrFail($id);

        // Check if the specification is being used
        if ($spesifikasi->spesifikasiProduk()->count() > 0) {
            return redirect()->route('spesifikasi.index')
                ->with('toast_error', 'Spesifikasi tidak dapat dihapus karena sedang digunakan oleh produk.');
        }

        $spesifikasi->delete();

        return redirect()->route('spesifikasi.index')
            ->with('toast_success', 'Spesifikasi berhasil dihapus.');
    }
}
