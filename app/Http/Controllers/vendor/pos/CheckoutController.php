<?php

namespace App\Http\Controllers\vendor\pos;

use Carbon\Carbon;
use App\Models\Vendor\Bahan;
use Illuminate\Http\Request;
use App\Models\Vendor\Produk;
use App\Models\Vendor\Pelanggan;
use App\Models\Vendor\Transaksi;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Vendor\EstimasiProduk;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        // Dapatkan vendor dari user yang sedang login
        $vendor = Auth::user()->vendorUser->first();

        $validatedData = $request->validate([
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'payment_method' => 'required|in:cash,transfer,qris,credit_card,debit_card,e_wallet',
            'payment_amount' => 'nullable|numeric', // Tambahkan validasi untuk jumlah pembayaran
            'catatan' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $cartItems = session('cart', []);

            if (empty($cartItems)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty. Please add items before checkout.'
                ], 400);
            }

            $totalTime = collect($cartItems)->sum(function ($item) {
                $product = Produk::with('estimasiProduk.alat')->find($item['product_id']);
                return $product ? $product->getEstimatedProductionTime($item['quantity']) : 0;
            });

            $latestPending = Transaksi::where('status', 'pending')
                ->where('vendor_id', $vendor->id)
                ->latest('estimasi_selesai')
                ->first();

            $startTime = $latestPending ? Carbon::parse($latestPending->estimasi_selesai) : now();
            $estimatedCompletion = $startTime->addMinutes($totalTime);

            // Hitung total harga
            $totalAmount = collect($cartItems)->sum('total_price');

            // Hitung jumlah terbayar dan kembalian
            $paymentAmount = $validatedData['payment_method'] === 'cash' && isset($validatedData['payment_amount'])
                ? (float) $validatedData['payment_amount']
                : $totalAmount;

            $changeAmount = max(0, $paymentAmount - $totalAmount);

            // Validasi jumlah pembayaran untuk metode cash (payment_amount harus >= total)
            if ($validatedData['payment_method'] === 'cash' && $paymentAmount < $totalAmount) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount (' . number_format($paymentAmount, 0, ',', '.') . ') is less than the total amount (' . number_format($totalAmount, 0, ',', '.') . ').'
                ], 422);
            }

            // Cek stok bahan semua item terlebih dulu sebelum transaksi dibuat
            // agar stok tidak pernah menjadi negatif
            $stockRequirements = [];
            foreach ($cartItems as $cartItem) {
                if (empty($cartItem['specifications'])) {
                    continue;
                }

                foreach ($cartItem['specifications'] as $spec) {
                    if (empty($spec['bahan_id'])) {
                        continue;
                    }

                    $required = ($spec['input_type'] ?? 'text') === 'number'
                        ? (float) ($spec['value'] ?? 0) * $cartItem['quantity']
                        : $cartItem['quantity'];

                    $stockRequirements[$spec['bahan_id']] = ($stockRequirements[$spec['bahan_id']] ?? 0) + $required;
                }
            }

            if (!empty($stockRequirements)) {
                $bahans = Bahan::whereIn('id', array_keys($stockRequirements))->get()->keyBy('id');

                foreach ($stockRequirements as $bahanId => $requiredAmount) {
                    $bahan = $bahans->get($bahanId);

                    if (!$bahan || (float) $bahan->stok < $requiredAmount) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Insufficient stock for material: ' . ($bahan ? $bahan->nama_bahan : $bahanId) . '. Available: ' . ($bahan ? $bahan->stok : 0) . ', required: ' . $requiredAmount . '.'
                        ], 422);
                    }
                }
            }

            // Generate transaction code (format seragam TRX-YYYYMMDD-XXXX, unik)
            do {
                $transactionCode = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            } while (DB::table('transaksis')->where('kode', $transactionCode)->exists());

            $transaksi = Transaksi::create([
                'vendor_id' => $vendor->id,
                'kode' => $transactionCode,
                'user_id' => Auth::id(),
                'pelanggan_id' => $validatedData['pelanggan_id'],
                'total_harga' => $totalAmount,
                'terbayar' => $paymentAmount, // Tambahkan jumlah terbayar
                'kembali' => $changeAmount, // Tambahkan jumlah kembalian
                'status' => 'pending',
                'payment_method' => $validatedData['payment_method'],
                'estimasi_selesai' => $estimatedCompletion,
                'tanggal_dibuat' => now(),
                'progress_percentage' => 0,
                'catatan' => $validatedData['catatan']
            ]);

            foreach ($cartItems as $item) {
                $transaksiItem = $transaksi->transaksiItem()->create([
                    'vendor_id' => $vendor->id,
                    'produk_id' => $item['product_id'],
                    'kuantitas' => $item['quantity'],
                    'harga_satuan' => $item['total_price'] / $item['quantity']
                ]);

                foreach ($item['specifications'] as $specId => $spec) {
                    $transaksiItem->transaksiItemSpecifications()->create([
                        'vendor_id' => $vendor->id,
                        'spesifikasi_produk_id' => $specId,
                        'bahan_id' => $spec['bahan_id'],
                        'value' => $spec['value'],  // Ubah dari 'nilai_spesifikasi' menjadi 'value'
                        'input_type' => $spec['input_type'],
                        'price' => $spec['price']
                    ]);

                    // Update stock
                    $bahan = Bahan::find($spec['bahan_id']);
                    if ($bahan) {
                        if ($spec['input_type'] === 'number') {
                            // PERBAIKAN: Pastikan nilai desimal dipertahankan
                            $bahan->decrement('stok', (float)$spec['value'] * $item['quantity']);
                        } else {
                            $bahan->decrement('stok', $item['quantity']);
                        }
                        $bahan->checkStockLevel();
                    }
                }
            }

            // Update customer's last transaction timestamp
            $pelanggan = Pelanggan::find($validatedData['pelanggan_id']);
            if ($pelanggan) {
                $pelanggan->update([
                    'transaksi_terakhir' => now()
                ]);
            }

            DB::commit();
            session()->forget('cart');

            return response()->json([
                'success' => true,
                'invoiceUrl' => route('pos.invoice.show', [
                    'transaksi' => $transaksi->id
                ]),
                'downloadUrl' => route('pos.invoice.download', [
                    'transaksi' => $transaksi->id
                ]),
                'redirectUrl' => route('pos.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Transaction failed: ' . $e->getMessage()
            ], 500);
        }
    }
    public function show()
    {
        try {
            // Dapatkan vendor dari user yang sedang login
            $vendor = Auth::user()->vendorUser->first();
            $cartItems = session('cart', []);

            if (empty($cartItems)) {
                return redirect()->route('pos.cart')
                    ->with('toast_error', 'Your cart is empty. Please add items before checkout.');
            }

            $totalAmount = collect($cartItems)->sum('total_price');
            $totalTime = collect($cartItems)->sum(function ($item) {
                $product = Produk::with('estimasiProduk.alat')->find($item['product_id']);
                return $product ? $product->getEstimatedProductionTime($item['quantity']) : 0;
            });

            $customers = Pelanggan::where('vendor_id', $vendor->id)->get();
            $products = Produk::where('vendor_id', $vendor->id)->get();

            return view('pos.checkout', compact(
                'cartItems',
                'totalAmount',
                'totalTime',
                'customers',
                'products'
            ));
        } catch (\Exception $e) {
            Log::error('Error displaying checkout: ' . $e->getMessage());
            return redirect()->route('pos.cart')
                ->with('toast_error', 'Failed to load checkout page: ' . $e->getMessage());
        }
    }

    public function createCustomer(Request $request)
    {
        // Dapatkan vendor dari user yang sedang login
        $vendor = Auth::user()->vendorUser->first();

        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'alamat' => 'required|string',
                'no_telp' => 'required|string',
                'email' => 'nullable|email'
            ]);

            $customer = Pelanggan::create([
                'vendor_id' => $vendor->id,
                'kode' => 'PLG-' . date('YmdHis'),
                'nama' => $validated['nama'],
                'alamat' => $validated['alamat'],
                'no_telp' => $validated['no_telp'],
                'email' => $validated['email']
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'customer' => $customer
                ]);
            }

            return redirect()->back()->with('toast_success', 'Customer created successfully');
        } catch (\Exception $e) {
            Log::error('Error creating customer: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create customer: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('toast_error', 'Failed to create customer: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function calculateEstimatedCompletion($cartItems)
    {
        $equipmentSchedule = [];
        $maxCompletionTime = now(); // Start with current time as Carbon instance

        foreach ($cartItems as $item) {
            $estimasiProduk = EstimasiProduk::where('produk_id', $item['product_id'])->first();
            if (!$estimasiProduk) continue;

            $alat = $estimasiProduk->alat;
            if (!$alat) continue;

            $productionTime = $estimasiProduk->calculateTotalProductionTime($item['quantity']);

            if (!isset($equipmentSchedule[$alat->id])) {
                $equipmentSchedule[$alat->id] = $alat->getNextAvailableSlot();
            }

            $startTime = $equipmentSchedule[$alat->id];
            $equipmentSchedule[$alat->id] = Carbon::parse($startTime)->addMinutes($productionTime);

            // Update max completion time
            if ($equipmentSchedule[$alat->id]->gt($maxCompletionTime)) {
                $maxCompletionTime = $equipmentSchedule[$alat->id];
            }
        }

        return $maxCompletionTime;
    }
}
