<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor\KategoriProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KategoriProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = KategoriProduk::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where('nama_kategori', 'like', '%' . $request->search . '%');
        }

        // Sort functionality
        $sortField = $request->sort ?? 'nama_kategori';
        $sortOrder = $request->order ?? 'asc';
        $query->orderBy($sortField, $sortOrder);

        $kategori = $query->paginate(10)->appends(request()->query());

        return view('kategori_produk.index', compact('kategori'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kategori_produk.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        KategoriProduk::create([
            'nama_kategori' => $request->nama_kategori,
            'slug' => Str::slug($request->nama_kategori),
            'vendor_id' => session('current_vendor_id', 1) // Assuming you store current vendor ID in session
        ]);

        return redirect()->route('kategori-produk.index')
            ->with('toast_success', 'Kategori produk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(KategoriProduk $kategoriProduk)
    {
        return view('kategori_produk.show', compact('kategoriProduk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KategoriProduk $kategoriProduk)
    {
        return view('kategori_produk.edit', compact('kategoriProduk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KategoriProduk $kategoriProduk)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        $kategoriProduk->update([
            'nama_kategori' => $request->nama_kategori,
            'slug' => Str::slug($request->nama_kategori)
        ]);

        return redirect()->route('kategori-produk.index')
            ->with('toast_success', 'Kategori produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KategoriProduk $kategoriProduk)
    {
        // Check if category has products
        if ($kategoriProduk->produk()->count() > 0) {
            return redirect()->route('kategori-produk.index')
                ->with('toast_error', 'Kategori tidak dapat dihapus karena masih memiliki produk terkait.');
        }

        $kategoriProduk->delete();

        return redirect()->route('kategori-produk.index')
            ->with('toast_success', 'Kategori produk berhasil dihapus.');
    }
}
