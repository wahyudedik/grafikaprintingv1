@extends('layouts.layouts_dashboard')

@section('title', 'Invoice ' . $transaksi->kode)

@section('content')
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">Invoice #{{ $transaksi->kode }}</h3>
                        <div>
                            <a href="{{ route('pos.invoice.download', $transaksi->id) }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download me-2"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                                    <path d="M7 11l5 5l5 -5"></path>
                                    <path d="M12 4l0 12"></path>
                                </svg>Download PDF
                            </a>
                            <a href="#" id="print-invoice" class="btn btn-outline-secondary ms-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer me-2"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path
                                        d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2">
                                    </path>
                                    <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                                    <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z">
                                    </path>
                                </svg>Print
                            </a>
                            <a href="#" id="print-preview" class="btn btn-outline-info ms-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye me-2"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path>
                                </svg>Preview
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Invoice yang akan diprint -->
                        <div id="invoice-container" class="invoice-print-container mx-auto">
                            <!-- Konten invoice tetap sama seperti sebelumnya -->
                            <div class="header">
                                @php
                                    $vendorName =
                                        $transaksi->vendor->name ??
                                        ($transaksi->vendor->nama_vendor ?? 'Bamboo Digital Printing');
                                    $vendorAddress =
                                        $transaksi->vendor->address ??
                                        ($transaksi->vendor->alamat ?? 'Pesantren Peterongan Jombang');
                                    $vendorPhone =
                                        $transaksi->vendor->phone ?? ($transaksi->vendor->telepon ?? '081-515-876-755');
                                    $vendorEmail = $transaksi->vendor->email ?? 'infografikaprint@gmail.com';
                                    $logoPath =
                                        $transaksi->vendor && $transaksi->vendor->logo
                                            ? asset('vendors_logo/' . $transaksi->vendor->logo)
                                            : asset('images/logo.png');
                                @endphp
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="text-center">
                                        <img src="{{ $logoPath }}" alt="Logo"
                                            style="height: 50px; margin-bottom: 10px;">
                                        <h2 style="margin: 0;">{{ $vendorName }}</h2>
                                        <p style="margin: 2px 0;">{{ $vendorAddress }}</p>
                                        <p style="margin: 2px 0;">{{ $vendorPhone }} | {{ $vendorEmail }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Sisa konten invoice tetap sama -->
                            <div class="info d-flex mb-5">
                                <div class="col-6">
                                    <p>No: {{ $transaksi->kode }}</p>
                                    <p>Cust: {{ $transaksi->pelanggan->nama }}</p>
                                    <p>Bayar: {{ ucfirst($transaksi->payment_method ?? 'Transfer') }}</p>
                                </div>
                                <div class="col-6">
                                    <p>Tanggal: {{ $transaksi->tanggal_dibuat->format('d/m/Y') }}</p>
                                    <p>Status: {{ ucfirst($transaksi->status ?? 'Processing') }}</p>
                                </div>
                            </div>

                            <div class="items">
                                @foreach ($transaksi->transaksiItem as $item)
                                    <div style="margin-bottom: 15px;">
                                        <!-- Nama Produk -->
                                        <div style="font-weight: bold; margin-bottom: 5px;">
                                            {{ $item->produk->nama_produk }}</div>

                                        <!-- Jumlah (quantity) -->
                                        <div
                                            style="display: flex; justify-content: space-between; margin-bottom: 5px; border-bottom: 1px dashed #ccc; padding-bottom: 3px;">
                                            <span style="color: #666;">Quantity</span>
                                            <span style="font-weight: 500;">{{ $item->kuantitas }} pcs</span>
                                        </div>

                                        <!-- Spesifikasi Produk -->
                                        @if ($item->transaksiItemSpecifications && count($item->transaksiItemSpecifications) > 0)
                                            <div class="item-details">
                                                @foreach ($item->transaksiItemSpecifications as $spec)
                                                    @if ($spec->spesifikasiProduk && $spec->spesifikasiProduk->spesifikasi)
                                                        <div
                                                            style="display: flex; justify-content: space-between; margin-bottom: 3px; border-bottom: 1px dashed #ccc; padding-bottom: 3px;">
                                                            <span style="color: #666;">
                                                                {{ $spec->spesifikasiProduk->spesifikasi->nama_spesifikasi }}
                                                            </span>
                                                            <span style="font-weight: 500;">
                                                                @php
                                                                    $hargaSatuan = 0;
                                                                    $totalHarga = $spec->price ?? 0;
                                                                    $nilai = $spec->nilai_spesifikasi ?? null;

                                                                    if (empty($nilai) && isset($spec->value)) {
                                                                        $nilai = $spec->value;
                                                                    }

                                                                    if ($spec->input_type == 'select' && $spec->bahan) {
                                                                        echo $spec->bahan->nama_bahan .
                                                                            ': ' .
                                                                            $item->kuantitas .
                                                                            ' x Rp ' .
                                                                            number_format(
                                                                                $totalHarga / $item->kuantitas,
                                                                                0,
                                                                                ',',
                                                                                '.',
                                                                            ) .
                                                                            ' = Rp ' .
                                                                            number_format($totalHarga, 0, ',', '.');
                                                                    } elseif (
                                                                        $nilai &&
                                                                        $spec->spesifikasiProduk->spesifikasi
                                                                    ) {
                                                                        if ($totalHarga > 0 && $nilai > 0) {
                                                                            $hargaSatuan = $totalHarga / $nilai;
                                                                        }

                                                                        echo number_format($nilai, 2, ',', '.') .
                                                                            ' ' .
                                                                            ($spec->spesifikasiProduk->spesifikasi
                                                                                ->satuan ??
                                                                                '') .
                                                                            ' x Rp ' .
                                                                            number_format($hargaSatuan, 0, ',', '.') .
                                                                            ' = Rp ' .
                                                                            number_format($totalHarga, 0, ',', '.');
                                                                    } else {
                                                                        echo 'Rp ' .
                                                                            number_format($totalHarga, 0, ',', '.');
                                                                    }
                                                                @endphp
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Total Harga Item -->
                                        <div
                                            style="display: flex; justify-content: space-between; margin-top: 5px; font-weight: bold;">
                                            <span>Total Item</span>
                                            <span style="color: #0d6efd;">
                                                Rp {{ number_format($item->harga_satuan * $item->kuantitas, 0, ',', '.') }}
                                            </span>
                                        </div>

                                        <!-- Garis pemisah antar item -->
                                        @if (!$loop->last)
                                            <div
                                                style="border-bottom: 1px solid #ccc; margin-top: 10px; margin-bottom: 10px;">
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="divider"></div>

                            <div class="total">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="width: 60%; text-align: left; padding-bottom: 5px;">Total</td>
                                        <td style="width: 40%; text-align: right; padding-bottom: 5px;">
                                            Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 60%; text-align: left; padding-bottom: 5px;">Terbayar</td>
                                        <td style="width: 40%; text-align: right; padding-bottom: 5px;">
                                            Rp {{ number_format($transaksi->terbayar, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 60%; text-align: left; padding-bottom: 5px;">Kembali</td>
                                        <td style="width: 40%; text-align: right; padding-bottom: 5px;">
                                            Rp {{ number_format($transaksi->kembali, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="footer">
                                <p>Terima kasih telah berbelanja!</p>
                                <p>Estimasi Selesai:
                                    {{ \Carbon\Carbon::parse($transaksi->estimasi_selesai)->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk preview struk -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Preview Struk POS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex justify-content-center">
                    <div id="preview-container" class="pos-receipt-preview"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="print-from-preview">Print</button>
                </div>
            </div>
        </div>
    </div>

        <style>
        /* Styles untuk tampilan invoice di halaman */
        .invoice-print-container {
            font-family: Arial, sans-serif;
            font-size: 12px;
            width: 7.5cm;
            padding: 10px;
            background: #fff;
            color: #000;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }

        .info {
            font-size: 10px;
            margin-bottom: 10px;
        }

        .info p {
            margin: 2px 0;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .items {
            font-size: 10px;
        }

        .item-details {
            margin-left: 10px;
            font-size: 9px;
        }

        .total {
            font-weight: bold;
            padding-top: 5px;
        }

        .total table {
            width: 100%;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 10px;
            padding-top: 5px;
        }

        /* Preview modal styles */
        .pos-receipt-preview {
            font-family: Arial, sans-serif;
            font-size: 12px;
            width: 7.5cm;
            padding: 10px;
            background: #fff;
            color: #000;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-height: 80vh;
            overflow-y: auto;
        }

        /* Styles khusus untuk print */
        @media print {
            @page {
                size: 80mm auto; /* Lebar 80mm (8cm) dan tinggi otomatis */
                margin: 0mm;
            }
            
            html, body {
                width: 75mm; /* 7.5cm */
                margin: 0;
                padding: 0;
            }
            
            body * {
                visibility: hidden;
            }

            .invoice-print-container,
            .invoice-print-container * {
                visibility: visible;
            }

            .invoice-print-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 75mm; /* 7.5cm */
                margin: 0;
                padding: 5mm;
                border: none;
                box-shadow: none;
            }

            .navbar,
            .header-menu,
            .footer-menu,
            .btn,
            .card-header,
            .modal,
            .modal-backdrop {
                display: none !important;
            }
            
            /* Pastikan semua konten muat dalam lebar kertas */
            .items, .item-details, .total, .footer {
                width: 100%;
                max-width: 65mm; /* Sedikit lebih kecil dari lebar kertas */
            }
            
            /* Ukuran font yang lebih kecil untuk print */
            .header h2 {
                font-size: 14px;
                margin: 0;
            }
            
            .header p {
                font-size: 9px;
                margin: 1px 0;
            }
            
            .info {
                font-size: 9px;
            }
            
            .items {
                font-size: 9px;
            }
            
            .item-details {
                font-size: 8px;
            }
            
            .footer {
                font-size: 9px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi untuk menampilkan preview dalam modal
            document.getElementById('print-preview').addEventListener('click', function(e) {
                e.preventDefault();
                
                // Salin konten invoice ke dalam modal preview
                const invoiceContent = document.getElementById('invoice-container').cloneNode(true);
                const previewContainer = document.getElementById('preview-container');
                
                // Kosongkan container preview dan tambahkan konten baru
                previewContainer.innerHTML = '';
                previewContainer.appendChild(invoiceContent);
                
                // Tampilkan modal
                const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
                previewModal.show();
            });
            
            // Fungsi untuk mencetak dari tombol print biasa
            document.getElementById('print-invoice').addEventListener('click', function(e) {
                e.preventDefault();
                printReceipt();
            });
            
            // Fungsi untuk mencetak dari modal preview
            document.getElementById('print-from-preview').addEventListener('click', function(e) {
                e.preventDefault();
                
                // Sembunyikan modal sebelum mencetak
                const previewModal = bootstrap.Modal.getInstance(document.getElementById('previewModal'));
                previewModal.hide();
                
                // Tunggu modal hilang sebelum mencetak
                setTimeout(function() {
                    printReceipt();
                }, 500);
            });
            
            // Fungsi utama untuk mencetak
            function printReceipt() {
                // Atur pengaturan printer sebelum mencetak
                const style = document.createElement('style');
                style.id = 'print-style';
                style.innerHTML = `
                    @page {
                        size: 80mm auto; /* Lebar 80mm (8cm) dan tinggi otomatis */
                        margin: 0;
                    }
                `;
                document.head.appendChild(style);
                
                // Tunggu sebentar agar style diterapkan
                setTimeout(function() {
                    window.print();
                    // Hapus style setelah mencetak
                    setTimeout(function() {
                        const printStyle = document.getElementById('print-style');
                        if (printStyle) {
                            document.head.removeChild(printStyle);
                        }
                    }, 1000);
                }, 100);
            }
        });
    </script>
@endsection

