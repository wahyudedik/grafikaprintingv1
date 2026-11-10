<?php

namespace App\Http\Controllers\vendor;

use App\Models\Vendor;
use App\Models\Vendor\Alat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage;

class AlatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Alat::query();

            // Search filter
            if ($request->has('search') && !empty($request->search)) {
                $query->search($request->search);
            }

            // Status filter
            if ($request->has('status') && !empty($request->status)) {
                $query->byStatus($request->status);
            }

            // Availability filter (tambahan)
            if ($request->has('tersedia') && $request->tersedia !== '') {
                $query->where('tersedia', $request->tersedia === 'yes');
            }

            // Sorting
            $sort = $request->input('sort', 'created_at');
            $order = $request->input('order', 'desc');
            $query->orderBy($sort, $order);

            $alat = $query->paginate(10);
            return view('alat.index', compact('alat'));
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error getting alat: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('alat.create');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'merek' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'spesifikasi_alat' => 'required|string',
            'status' => 'required|in:aktif,maintenance,rusak',
            'tanggal_pembelian' => 'required|date',
            'kapasitas_cetak_per_jam' => 'required|integer|min:1',
            'tersedia' => 'required|boolean',
        ]);

        try {
            // Get the current vendor ID from session
            $vendorId = session('current_vendor_id');

            // If not in session, try to get from authenticated user's relationship
            if (!$vendorId) {
                $vendor = Vendor::forUser(Auth::id())->first();

                if ($vendor) {
                    $vendorId = $vendor->id;
                    // Store in session for future use
                    Session::put('current_vendor_id', $vendorId);
                } else {
                    return redirect()->back()->with('toast_error', 'No associated vendor found')->withInput();
                }
            }

            // Create alat with vendor_id
            Alat::create(array_merge($request->all(), ['vendor_id' => $vendorId]));

            return redirect()->route('alat.index')->with('toast_success', 'Alat berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error adding alat: ' . $e->getMessage())->withInput();
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $alat = Alat::findOrFail($id);
            return view('alat.show', compact('alat'));
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error finding alat: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $alat = Alat::findOrFail($id);
            return view('alat.edit', compact('alat'));
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error finding alat: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'merek' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'spesifikasi_alat' => 'required|string',
            'status' => 'required|in:aktif,maintenance,rusak',
            'tanggal_pembelian' => 'required|date',
            'kapasitas_cetak_per_jam' => 'required|integer|min:1',
            'tersedia' => 'required|boolean',
        ]);

        try {
            $alat = Alat::findOrFail($id);

            // Preserve the vendor_id
            $data = $request->all();
            $data['vendor_id'] = $alat->vendor_id;

            $alat->update($data);
            return redirect()->route('alat.index')->with('toast_success', 'Alat berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error updating alat: ' . $e->getMessage())->withInput();
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $alat = Alat::findOrFail($id);
            $alat->delete();
            return redirect()->route('alat.index')->with('toast_success', 'Alat berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error deleting alat: ' . $e->getMessage());
        }
    }
}
