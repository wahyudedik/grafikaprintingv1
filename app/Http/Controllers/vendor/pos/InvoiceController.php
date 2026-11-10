<?php

namespace App\Http\Controllers\vendor\pos;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Vendor\Transaksi;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class InvoiceController extends Controller
{
    public function show(Transaksi $transaksi)
    {
        try {
            // Dapatkan vendor dari user yang sedang login
            $vendor = Auth::user()->vendorUser->first();

            $transaksi = Transaksi::with([
                'transaksiItem.produk',
                'transaksiItem.transaksiItemSpecifications.spesifikasiProduk.spesifikasi',
                'transaksiItem.transaksiItemSpecifications.bahan',
                'pelanggan', 
                'vendor',
            ])->where('vendor_id', $vendor->id)
                ->findOrFail($transaksi->id);

            return view('pos.show', compact('transaksi'));
        } catch (\Exception $e) {
            Log::error('Error showing invoice: ' . $e->getMessage());
            return redirect()->route('pos.index')
                ->with('toast_error', 'Failed to display invoice: ' . $e->getMessage());
        }
    }

    public function download(Transaksi $transaksi)
    {
        try {
            // Dapatkan vendor dari user yang sedang login
            $vendor = Auth::user()->vendorUser->first();

            $transaksi = Transaksi::with([
                'transaksiItem.produk',
                'transaksiItem.transaksiItemSpecifications.spesifikasiProduk.spesifikasi',
                'transaksiItem.transaksiItemSpecifications.bahan',
                'pelanggan',
                'vendor'
            ])->where('vendor_id', $vendor->id)
                ->findOrFail($transaksi->id);

            // Prepare logo as base64 data URI
            $logoBase64 = null;

            if ($transaksi->vendor && $transaksi->vendor->logo) {
                $logoPath = public_path('vendors_logo/' . $transaksi->vendor->logo);
                if (File::exists($logoPath)) {
                    $logoBase64 = $this->getBase64Image($logoPath);
                }
            }

            // If vendor logo doesn't exist, use default logo
            if (!$logoBase64) {
                $defaultLogoPath = public_path('images/logo.png');
                if (File::exists($defaultLogoPath)) {
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

            // Let CSS handle the width instead of setting paper size
            $pdf = Pdf::loadView('pos.print-invoice', compact('transaksi', 'logoBase64'))
                ->setOptions($options)
                ->setPaper([0, 0, 283.46, 283.46], 'portrait');
                
            return $pdf->download("invoice-{$transaksi->kode}.pdf");
        } catch (\Exception $e) {
            Log::error('Error downloading invoice: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
            return redirect()->route('pos.index')
                ->with('toast_error', 'Failed to download invoice: ' . $e->getMessage());
        }
    }

    /**
     * Convert an image to base64 data URI
     */
    private function getBase64Image($path)
    {
        try {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = File::get($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            return $base64;
        } catch (\Exception $e) {
            Log::error('Error converting image to base64: ' . $e->getMessage());
            return null;
        }
    }
}
