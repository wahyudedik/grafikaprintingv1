<?php

namespace App\Http\Controllers\vendor;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Vendor\Produk;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Vendor\Pelanggan;
use App\Models\Vendor\Transaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Vendor\TransaksiItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Vendor\TransaksiItemSpecifications;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Build transaction query
        $query = Transaksi::query();

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        // Apply status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->withStatus($request->status);
        }

        // Apply date range filter
        $query->withinDateRange($request->start_date, $request->end_date);

        // Get transactions with pagination
        $transaksis = $query->with(['pelanggan', 'user', 'transaksiItem.produk'])
            ->latest('tanggal_dibuat')
            ->paginate(10)
            ->appends($request->query());

        // Status options for filter dropdown
        $statusOptions = [
            'pending' => 'Pending',
            'processing' => 'Diproses',
            'quality_check' => 'QC',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan'
        ];

        return view('transaksi.index', compact('transaksis', 'statusOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            return redirect()->route('dashboard')
                ->with('toast_error', 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
        }

        $pelanggans = Pelanggan::where('vendor_id', $vendor->id)->get();

        if ($pelanggans->isEmpty()) {
            return redirect()->route('pelanggan.create')
                ->with('toast_info', 'Anda belum memiliki pelanggan. Silakan tambahkan pelanggan terlebih dahulu.');
        }

        $produks = Produk::where('vendor_id', $vendor->id)
            ->with(['spesifikasiProduk.spesifikasi', 'spesifikasiProduk.bahanSpesifikasiProduk'])
            ->get();

        if ($produks->isEmpty()) {
            return redirect()->route('produk.create')
                ->with('toast_info', 'Anda belum memiliki produk. Silakan tambahkan produk terlebih dahulu.');
        }

        $paymentMethods = [
            'cash' => 'Cash',
            'transfer' => 'Bank Transfer',
            'qris' => 'QRIS',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'e_wallet' => 'E-Wallet',
        ];

        return view('transaksi.create', compact('pelanggans', 'produks', 'paymentMethods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Get current vendor from session
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            return redirect()->route('dashboard')
                ->with('toast_error', 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
        }

        // Validate main transaction data
        $validator = Validator::make($request->all(), [
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'payment_method' => 'required|string',
            'estimasi_selesai' => 'required|date',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produks,id',
            'items.*.kuantitas' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Begin transaction
        DB::beginTransaction();

        try {
            // Generate transaction code (format seragam TRX-YYYYMMDD-XXXX, unik)
            do {
                $transactionCode = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            } while (DB::table('transaksis')->where('kode', $transactionCode)->exists());

            // Calculate total price
            $totalPrice = 0;
            foreach ($request->items as $item) {
                $totalPrice += $item['kuantitas'] * $item['harga_satuan'];
            }

            // Ambil jumlah pembayaran dari request
            $terbayar = $request->input('terbayar', $totalPrice);

            // Hitung kembalian
            $kembali = max(0, $terbayar - $totalPrice);

            // Create transaction
            $transaksi = Transaksi::create([
                'vendor_id' => $vendor->id,
                'kode' => $transactionCode,
                'user_id' => Auth::id(),
                'pelanggan_id' => $request->pelanggan_id,
                'total_harga' => $totalPrice,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'estimasi_selesai' => $request->estimasi_selesai,
                'tanggal_dibuat' => now(),
                'progress_percentage' => 0,
                'catatan' => $request->catatan,
                'terbayar' => $terbayar,
                'kembali' => $kembali
            ]);

            // Process transaction items
            foreach ($request->items as $itemData) {
                $item = TransaksiItem::create([
                    'vendor_id' => $vendor->id,
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $itemData['produk_id'],
                    'kuantitas' => $itemData['kuantitas'],
                    'harga_satuan' => $itemData['harga_satuan']
                ]);

                // Process specifications if provided
                if (isset($itemData['specifications']) && is_array($itemData['specifications'])) {
                    foreach ($itemData['specifications'] as $specId => $specData) {
                        if (empty($specData['value']) && !isset($specData['bahan_id'])) {
                            continue; // Skip empty specs
                        }

                        TransaksiItemSpecifications::create([
                            'vendor_id' => $vendor->id,
                            'transaksi_item_id' => $item->id,
                            'spesifikasi_produk_id' => $specId,
                            'bahan_id' => $specData['bahan_id'] ?? null,
                            'value' => $specData['value'] ?? null,
                            'input_type' => $specData['input_type'] ?? 'text',
                            'price' => $specData['price'] ?? 0
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('transaksi.show', $transaksi->id)
                ->with('toast_success', 'Transaksi berhasil dibuat dengan kode: ' . $transactionCode);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('toast_error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            return redirect()->route('dashboard')
                ->with('toast_error', 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
        }

        $transaksi = Transaksi::where('vendor_id', $vendor->id)
            ->with([
                'pelanggan',
                'user',
                'transaksiItem.produk',
                'transaksiItem.transaksiItemSpecifications.spesifikasiProduk.spesifikasi',
                'transaksiItem.transaksiItemSpecifications.bahan'
            ])
            ->findOrFail($id);

        return view('transaksi.show', compact('transaksi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            return redirect()->route('dashboard')
                ->with('toast_error', 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
        }

        $transaksi = Transaksi::where('vendor_id', $vendor->id)
            ->with([
                'pelanggan',
                'transaksiItem.produk',
                'transaksiItem.transaksiItemSpecifications.spesifikasiProduk.spesifikasi',
                'transaksiItem.transaksiItemSpecifications.bahan'
            ])
            ->findOrFail($id);

        $pelanggans = Pelanggan::where('vendor_id', $vendor->id)->get();
        $produks = Produk::where('vendor_id', $vendor->id)
            ->with(['spesifikasiProduk.spesifikasi', 'spesifikasiProduk.bahanSpesifikasiProduk'])
            ->get();

        $paymentMethods = [
            'cash' => 'Cash',
            'transfer' => 'Bank Transfer',
            'qris' => 'QRIS',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'e_wallet' => 'E-Wallet',
        ];

        $statusOptions = [
            'pending' => 'Pending',
            'processing' => 'Diproses',
            'quality_check' => 'QC',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan'
        ];

        return view('transaksi.edit', compact('transaksi', 'pelanggans', 'produks', 'paymentMethods', 'statusOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            return redirect()->route('dashboard')
                ->with('toast_error', 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
        }

        $transaksi = Transaksi::where('vendor_id', $vendor->id)->findOrFail($id);

        // Pisahkan logika: jika request hanya membawa status (tanpa data item), cukup update status saja
        $hasItems = $request->has('items') && is_array($request->items);

        // Validate main transaction data
        $rules = [
            'status' => 'required|string|in:pending,processing,quality_check,completed,cancelled',
            'catatan' => 'nullable|string',
        ];

        if ($hasItems) {
            $rules += [
                'pelanggan_id' => 'required|exists:pelanggans,id',
                'payment_method' => 'required|string',
                'estimasi_selesai' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.id' => 'nullable|exists:transaksi_items,id',
                'items.*.produk_id' => 'required|exists:produks,id',
                'items.*.kuantitas' => 'required|integer|min:1',
                'items.*.harga_satuan' => 'required|numeric|min:0',
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Begin transaction
        DB::beginTransaction();

        try {
            $progressMap = [
                'pending' => 0,
                'processing' => 25,
                'quality_check' => 80,
                'completed' => 100,
                'cancelled' => 0
            ];

            $updateData = [
                'status' => $request->status,
                'progress_percentage' => $progressMap[$request->status],
            ];

            if ($hasItems) {
                // Calculate total price
                $totalPrice = 0;
                foreach ($request->items as $item) {
                    $totalPrice += $item['kuantitas'] * $item['harga_satuan'];
                }

                $updateData += [
                    'pelanggan_id' => $request->pelanggan_id,
                    'total_harga' => $totalPrice,
                    'payment_method' => $request->payment_method,
                    'estimasi_selesai' => $request->estimasi_selesai,
                    'catatan' => $request->catatan,
                ];
            } elseif ($request->has('catatan')) {
                $updateData['catatan'] = $request->catatan;
            }

            $transaksi->update($updateData);

            // Hitung kembali berdasarkan total_harga hasil update terbaru
            if ($request->has('terbayar')) {
                $terbayar = $request->input('terbayar');

                $transaksi->update([
                    'terbayar' => $terbayar,
                    'kembali' => max(0, $terbayar - (float) $transaksi->total_harga),
                ]);
            }

            if ($hasItems) {
                // Track existing items to determine which to delete
                $existingItemIds = $transaksi->transaksiItem()->pluck('id')->toArray();
                $updatedItemIds = [];

                // Process transaction items
                foreach ($request->items as $itemData) {
                    if (isset($itemData['id']) && !empty($itemData['id'])) {
                        // Update existing item
                        $item = TransaksiItem::findOrFail($itemData['id']);
                        $item->update([
                            'produk_id' => $itemData['produk_id'],
                            'kuantitas' => $itemData['kuantitas'],
                            'harga_satuan' => $itemData['harga_satuan']
                        ]);

                        $updatedItemIds[] = $item->id;

                        // Hapus spesifikasi lama hanya jika request membawa data specifications
                        if (isset($itemData['specifications']) && is_array($itemData['specifications'])) {
                            TransaksiItemSpecifications::where('transaksi_item_id', $item->id)->delete();
                        }
                    } else {
                        // Create new item
                        $item = TransaksiItem::create([
                            'vendor_id' => $vendor->id,
                            'transaksi_id' => $transaksi->id,
                            'produk_id' => $itemData['produk_id'],
                            'kuantitas' => $itemData['kuantitas'],
                            'harga_satuan' => $itemData['harga_satuan']
                        ]);

                        $updatedItemIds[] = $item->id;
                    }

                    // Process specifications if provided
                    if (isset($itemData['specifications']) && is_array($itemData['specifications'])) {
                        foreach ($itemData['specifications'] as $specId => $specData) {
                            if (empty($specData['value']) && !isset($specData['bahan_id'])) {
                                continue; // Skip empty specs
                            }

                            TransaksiItemSpecifications::create([
                                'vendor_id' => $vendor->id,
                                'transaksi_item_id' => $item->id,
                                'spesifikasi_produk_id' => $specId,
                                'bahan_id' => $specData['bahan_id'] ?? null,
                                'value' => $specData['value'] ?? null,
                                'input_type' => $specData['input_type'] ?? 'text',
                                'price' => $specData['price'] ?? 0
                            ]);
                        }
                    }
                }

                // Delete items that were not updated
                $itemsToDelete = array_diff($existingItemIds, $updatedItemIds);
                if (!empty($itemsToDelete)) {
                    TransaksiItem::whereIn('id', $itemsToDelete)->delete();
                }
            }

            DB::commit();

            return redirect()->route('transaksi.show', $transaksi->id)
                ->with('toast_success', 'Transaksi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('toast_error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            return redirect()->route('dashboard')
                ->with('toast_error', 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
        }

        $transaksi = Transaksi::where('vendor_id', $vendor->id)->findOrFail($id);

        // Begin transaction
        DB::beginTransaction();

        try {
            // Delete related records
            // First delete specifications
            TransaksiItemSpecifications::whereIn(
                'transaksi_item_id',
                $transaksi->transaksiItem->pluck('id')->toArray()
            )->delete();

            // Delete transaction items
            TransaksiItem::where('transaksi_id', $transaksi->id)->delete();

            // Delete the transaction
            $transaksi->delete();

            DB::commit();

            return redirect()->route('transaksi.index')
                ->with('toast_success', 'Transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('toast_error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Generate invoice for transaction
     */
    public function generateInvoice(string $id)
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            return redirect()->route('dashboard')
                ->with('toast_error', 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
        }

        $transaksi = Transaksi::where('vendor_id', $vendor->id)
            ->with([
                'pelanggan',
                'transaksiItem.produk',
                'transaksiItem.transaksiItemSpecifications.spesifikasiProduk.spesifikasi',
                'transaksiItem.transaksiItemSpecifications.bahan',
                'vendor'
            ])
            ->findOrFail($id);

        // Prepare logo as base64 data URI
        $logoBase64 = null;

        if ($transaksi->vendor && $transaksi->vendor->logo) {
            $logoPath = public_path('vendors_logo/' . $transaksi->vendor->logo);
            if (file_exists($logoPath)) {
                $logoBase64 = $this->getBase64Image($logoPath);
            }
        }

        // If vendor logo doesn't exist, use default logo
        if (!$logoBase64) {
            $defaultLogoPath = public_path('images/logo.png');
            if (file_exists($defaultLogoPath)) {
                $logoBase64 = $this->getBase64Image($defaultLogoPath);
            }
        }

        // Set PDF options
        $options = [
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
            'dpi' => 150,
            'fontDir' => storage_path('fonts/'),
            'fontCache' => storage_path('fonts/'),
            'chroot' => public_path(),
        ];

        $pdf = PDF::loadView('transaksi.invoice', compact('transaksi', 'logoBase64'))
            ->setOptions($options)
            ->setPaper([0, 0, 283.46, 283.46], 'portrait');

        return $pdf->stream("invoice-{$transaksi->kode}.pdf");
    }

    /**
     * Convert an image to base64 data URI
     */
    private function getBase64Image($path)
    {
        try {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            return $base64;
        } catch (\Exception $e) {
            Log::error('Error converting image to base64: ' . $e->getMessage());
            return null;
        }
    }
}
