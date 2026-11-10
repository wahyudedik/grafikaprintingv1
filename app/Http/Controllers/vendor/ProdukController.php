<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor\Produk;
use App\Models\Vendor\KategoriProduk;
use App\Models\Vendor\Spesifikasi;
use App\Models\Vendor\Alat;
use App\Models\Vendor\Bahan;
use App\Models\Vendor\EstimasiProduk;
use App\Models\Vendor\SpesifikasiProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Produk::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', '%' . $search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $search . '%');
            });
        }

        // Filter by category
        if ($request->has('kategori_id') && !empty($request->kategori_id)) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Get all categories for filter dropdown
        $kategories = KategoriProduk::all();

        // Get the currently selected category (if any)
        $selectedCategory = null;
        if ($request->has('kategori_id') && !empty($request->kategori_id)) {
            $selectedCategory = KategoriProduk::find($request->kategori_id);
        }

        // Get products with pagination
        $produks = $query->with('kategori')->latest()->paginate(10);

        return view('produk.index', compact('produks', 'kategories', 'selectedCategory'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategories = KategoriProduk::all();
        $spesifikasis = Spesifikasi::all();
        $alats = Alat::all();
        $bahans = Bahan::all();

        return view('produk.create', compact('kategories', 'spesifikasis', 'alats', 'bahans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Modify validation rules to handle "new" category
        $rules = [
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'spesifikasi' => 'nullable|array',
            'spesifikasi.*.spesifikasi_id' => 'required|exists:spesifikasis,id',
            'spesifikasi.*.wajib_diisi' => 'boolean',
            'spesifikasi.*.pilihan' => 'nullable|array',
            'spesifikasi.*.bahan_ids' => 'nullable|array',
            'spesifikasi.*.bahan_ids.*' => 'exists:bahans,id',
            'estimasi' => 'nullable|array',
            'estimasi.*.alat_id' => 'required|exists:alats,id',
            'estimasi.*.waktu_persiapan' => 'required|numeric|min:0',
            'estimasi.*.waktu_produksi_per_unit' => 'required|numeric|min:0',
        ];

        // Conditional validation for category
        if ($request->kategori_id === 'new') {
            $rules['new_kategori'] = 'required|string|max:255';
        } else {
            $rules['kategori_id'] = 'required|exists:kategori_produks,id';
        }

        $request->validate($rules);

        // Handle category - create new if needed
        $kategori_id = $request->kategori_id;
        if ($request->kategori_id === 'new' && !empty($request->new_kategori)) {
            $kategori = KategoriProduk::create([
                'nama_kategori' => $request->new_kategori,
                'slug' => Str::slug($request->new_kategori),
            ]);
            $kategori_id = $kategori->id;
        }

        // Handle image uploads
        $gambars = [];
        if ($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $file) {
                $gambarName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('produk_gambar'), $gambarName);
                $gambars[] = 'produk_gambar/' . $gambarName;
            }
        }

        // Create the product
        $produk = Produk::create([
            'nama_produk' => $request->nama_produk,
            'deskripsi' => $request->deskripsi,
            'kategori_id' => $kategori_id,
            'gambar' => $gambars,
        ]);

        // Handle specifications
        if ($request->has('spesifikasi')) {
            foreach ($request->spesifikasi as $spec) {
                $specModel = SpesifikasiProduk::create([
                    'produk_id' => $produk->id,
                    'spesifikasi_id' => $spec['spesifikasi_id'],
                    'wajib_diisi' => $spec['wajib_diisi'] ?? false,
                    'pilihan' => $spec['pilihan'] ?? [],
                ]);

                // Handle bahan (materials) for this specification
                if (isset($spec['bahan_ids']) && is_array($spec['bahan_ids'])) {
                    $specModel->bahanSpesifikasiProduk()->attach($spec['bahan_ids']);
                }
            }
        }

        // Handle production estimates
        if ($request->has('estimasi')) {
            foreach ($request->estimasi as $estimasi) {
                EstimasiProduk::create([
                    'produk_id' => $produk->id,
                    'alat_id' => $estimasi['alat_id'],
                    'waktu_persiapan' => $estimasi['waktu_persiapan'],
                    'waktu_produksi_per_unit' => $estimasi['waktu_produksi_per_unit'],
                ]);
            }
        }

        return redirect()->route('produk.index')
            ->with('toast_success', 'Produk berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $produk = Produk::with([
            'kategori',
            'spesifikasiProduk.spesifikasi',
            'spesifikasiProduk.bahanSpesifikasiProduk',
            'estimasiProduk.alat'
        ])->findOrFail($id);

        return view('produk.show', compact('produk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $produk = Produk::with([
            'spesifikasiProduk.spesifikasi',
            'spesifikasiProduk.bahanSpesifikasiProduk',
            'estimasiProduk'
        ])->findOrFail($id);

        $kategories = KategoriProduk::all();
        $spesifikasis = Spesifikasi::all();
        $alats = Alat::all();
        $bahans = Bahan::all();

        return view('produk.edit', compact('produk', 'kategories', 'spesifikasis', 'alats', 'bahans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $produk = Produk::findOrFail($id);

        // Modify validation rules to handle "new" category
        $rules = [
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'spesifikasi' => 'nullable|array',
            'spesifikasi.*.id' => 'nullable|exists:spesifikasi_produks,id',
            'spesifikasi.*.spesifikasi_id' => 'required|exists:spesifikasis,id',
            'spesifikasi.*.wajib_diisi' => 'boolean',
            'spesifikasi.*.pilihan' => 'nullable|array',
            'spesifikasi.*.bahan_ids' => 'nullable|array',
            'spesifikasi.*.bahan_ids.*' => 'exists:bahans,id',
            'new_spesifikasi' => 'nullable|array',
            'new_spesifikasi.*.spesifikasi_id' => 'required|exists:spesifikasis,id',
            'new_spesifikasi.*.wajib_diisi' => 'boolean',
            'new_spesifikasi.*.pilihan' => 'nullable|array',
            'new_spesifikasi.*.bahan_ids' => 'nullable|array',
            'new_spesifikasi.*.bahan_ids.*' => 'exists:bahans,id',
            'estimasi' => 'nullable|array',
            'estimasi.*.id' => 'nullable|exists:estimasi_produks,id',
            'estimasi.*.alat_id' => 'required|exists:alats,id',
            'estimasi.*.waktu_persiapan' => 'required|numeric|min:0',
            'estimasi.*.waktu_produksi_per_unit' => 'required|numeric|min:0',
            'new_estimasi' => 'nullable|array',
            'new_estimasi.*.alat_id' => 'required|exists:alats,id',
            'new_estimasi.*.waktu_persiapan' => 'required|numeric|min:0',
            'new_estimasi.*.waktu_produksi_per_unit' => 'required|numeric|min:0',
            'delete_image' => 'nullable|array',
        ];

        // Conditional validation for category
        if ($request->kategori_id === 'new') {
            $rules['new_kategori'] = 'required|string|max:255';
        } else {
            $rules['kategori_id'] = 'required|exists:kategori_produks,id';
        }

        $request->validate($rules);

        // Handle category - create new if needed
        $kategori_id = $request->kategori_id;
        if ($request->kategori_id === 'new' && !empty($request->new_kategori)) {
            $kategori = KategoriProduk::create([
                'nama_kategori' => $request->new_kategori,
                'slug' => Str::slug($request->new_kategori),
            ]);
            $kategori_id = $kategori->id;
        }

        // Handle image deletions
        if ($request->has('delete_image')) {
            $currentImages = $produk->gambar ?? [];
            $newImages = [];

            foreach ($currentImages as $index => $image) {
                if (!in_array($index, $request->delete_image)) {
                    $newImages[] = $image;
                } else {
                    // Delete the file
                    $imagePath = public_path($image);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
            }

            $produk->gambar = $newImages;
        }

        // Handle new image uploads
        if ($request->hasFile('gambar')) {
            $currentImages = $produk->gambar ?? [];

            foreach ($request->file('gambar') as $file) {
                $gambarName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('produk_gambar'), $gambarName);
                $currentImages[] = 'produk_gambar/' . $gambarName;
            }

            $produk->gambar = $currentImages;
        }

        // Update basic product info
        $produk->nama_produk = $request->nama_produk;
        $produk->deskripsi = $request->deskripsi;
        $produk->kategori_id = $kategori_id;
        $produk->save();

        // Handle specifications
        if ($request->has('spesifikasi')) {
            // Track existing specs to determine which ones to delete
            $existingSpecIds = $produk->spesifikasiProduk->pluck('id')->toArray();
            $updatedSpecIds = [];

            foreach ($request->spesifikasi as $spec) {
                if (isset($spec['id'])) {
                    // Update existing specification
                    $specModel = SpesifikasiProduk::findOrFail($spec['id']);
                    $specModel->update([
                        'spesifikasi_id' => $spec['spesifikasi_id'],
                        'wajib_diisi' => $spec['wajib_diisi'] ?? false,
                        'pilihan' => $spec['pilihan'] ?? [],
                    ]);

                    // Handle bahan associations - detach all then attach new ones
                    $specModel->bahanSpesifikasiProduk()->detach();
                    if (isset($spec['bahan_ids']) && is_array($spec['bahan_ids'])) {
                        $specModel->bahanSpesifikasiProduk()->attach($spec['bahan_ids']);
                    }

                    $updatedSpecIds[] = $spec['id'];
                }
            }

            // Delete specifications that were removed in the form
            if ($request->has('deleted_spec_ids') && !empty($request->deleted_spec_ids)) {
                $deletedIds = explode(',', $request->deleted_spec_ids);
                SpesifikasiProduk::whereIn('id', $deletedIds)->delete();
            } else {
                // If no explicit deletion IDs, delete specs not included in the form
                $toDelete = array_diff($existingSpecIds, $updatedSpecIds);
                if (!empty($toDelete)) {
                    SpesifikasiProduk::whereIn('id', $toDelete)->delete();
                }
            }
        }

        // Handle new specifications
        if ($request->has('new_spesifikasi')) {
            foreach ($request->new_spesifikasi as $spec) {
                $specModel = SpesifikasiProduk::create([
                    'produk_id' => $produk->id,
                    'spesifikasi_id' => $spec['spesifikasi_id'],
                    'wajib_diisi' => $spec['wajib_diisi'] ?? false,
                    'pilihan' => $spec['pilihan'] ?? [],
                ]);

                // Handle bahan associations for new specs
                if (isset($spec['bahan_ids']) && is_array($spec['bahan_ids'])) {
                    $specModel->bahanSpesifikasiProduk()->attach($spec['bahan_ids']);
                }
            }
        }

        // Handle production estimates
        if ($request->has('estimasi')) {
            // Track existing estimates to determine which ones to delete
            $existingEstimateIds = $produk->estimasiProduk->pluck('id')->toArray();
            $updatedEstimateIds = [];

            foreach ($request->estimasi as $estimasi) {
                if (isset($estimasi['id'])) {
                    // Update existing estimate
                    $estimateModel = EstimasiProduk::findOrFail($estimasi['id']);
                    $estimateModel->update([
                        'alat_id' => $estimasi['alat_id'],
                        'waktu_persiapan' => $estimasi['waktu_persiapan'],
                        'waktu_produksi_per_unit' => $estimasi['waktu_produksi_per_unit'],
                    ]);

                    $updatedEstimateIds[] = $estimasi['id'];
                }
            }

            // Delete estimates that were removed in the form
            if ($request->has('deleted_estimate_ids') && !empty($request->deleted_estimate_ids)) {
                $deletedIds = explode(',', $request->deleted_estimate_ids);
                EstimasiProduk::whereIn('id', $deletedIds)->delete();
            } else {
                // If no explicit deletion IDs, delete estimates not included in the form
                $toDelete = array_diff($existingEstimateIds, $updatedEstimateIds);
                if (!empty($toDelete)) {
                    EstimasiProduk::whereIn('id', $toDelete)->delete();
                }
            }
        }

        // Handle new estimates
        if ($request->has('new_estimasi')) {
            foreach ($request->new_estimasi as $estimasi) {
                EstimasiProduk::create([
                    'produk_id' => $produk->id,
                    'alat_id' => $estimasi['alat_id'],
                    'waktu_persiapan' => $estimasi['waktu_persiapan'],
                    'waktu_produksi_per_unit' => $estimasi['waktu_produksi_per_unit'],
                ]);
            }
        }

        return redirect()->route('produk.index')
            ->with('toast_success', 'Produk berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $produk = Produk::findOrFail($id);

        // Delete associated images
        if (!empty($produk->gambar)) {
            foreach ($produk->gambar as $image) {
                $imagePath = public_path($image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
        }

        // Delete associated data
        $produk->spesifikasiProduk()->each(function ($spec) {
            // Detach all bahan associations first
            $spec->bahanSpesifikasiProduk()->detach();
            $spec->delete();
        });

        $produk->estimasiProduk()->delete();

        // Delete the product
        $produk->delete();

        return redirect()->route('produk.index')
            ->with('toast_success', 'Produk berhasil dihapus!');
    }
}
