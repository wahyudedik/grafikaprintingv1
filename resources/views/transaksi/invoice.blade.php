<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $transaksi->kode }}</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
            width: 7.5cm;
        }

        /* Base styles optimized for 7.5cm width */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            width: 7.5cm;
            margin: 0 auto;
            padding: 10px;
            background: #fff;
            color: #000;
        }

        /* Header section */
        .header {
            text-align: center;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }

        .header h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }

        .header p {
            margin: 2px 0;
            font-size: 11px;
        }

        .logo-img {
            height: 50px;
            margin-bottom: 8px;
            max-width: 100%;
        }

        /* Divider lines */
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        /* Transaction info section */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .info-table td {
            padding: 3px 0;
        }

        /* Items section */
        .items {
            font-size: 12px;
            width: 100%;
        }

        .item-row {
            margin-bottom: 12px;
        }

        .item-title {
            font-weight: bold;
            margin-bottom: 4px;
            font-size: 13px;
        }

        .item-spec {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            border-bottom: 1px dotted #ccc;
            padding-bottom: 3px;
        }

        .spec-label {
            color: #666;
        }

        .spec-value {
            font-weight: 500;
            text-align: right;
        }

        .item-total {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
            font-weight: bold;
        }

        .item-total-value {
            color: #0d6efd;
        }

        .item-separator {
            border-bottom: 1px solid #ccc;
            margin-top: 8px;
            margin-bottom: 8px;
        }

        /* Totals section */
        .total-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .total-table td {
            padding: 4px 0;
        }

        .total-table tr:last-child {
            font-weight: bold;
        }

        /* Footer section */
        .footer {
            text-align: center;
            font-size: 11px;
            margin-top: 8px;
            padding-top: 5px;
            border-top: 1px dashed #000;
        }

        .footer p {
            margin: 2px 0;
        }

        /* Container for the entire invoice */
        .invoice-print-container {
            width: 100%;
            max-width: 7.5cm;
        }

        @media print {
            body {
                width: 7.5cm;
                margin: 0;
                padding: 5px;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-print-container">
        <!-- HEADER SECTION -->
        <div class="header">
            @php
                $vendorName = $transaksi->vendor->name ?? ($transaksi->vendor->nama_vendor ?? 'Grafika Digital Printing');
                $vendorAddress = $transaksi->vendor->address ?? ($transaksi->vendor->alamat ?? 'Alamat Vendor');
                $vendorPhone = $transaksi->vendor->phone ?? ($transaksi->vendor->telepon ?? 'Telepon Vendor');
                $vendorEmail = $transaksi->vendor->email ?? 'email@vendor.com';
            @endphp

            <div class="text-center">
                <!-- Logo with base64 encoding -->
                @if(isset($logoBase64) && $logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" class="logo-img">
                @else
                    <!-- Fallback jika base64 tidak tersedia -->
                    @php
                        $logoPath = $transaksi->vendor && $transaksi->vendor->logo
                            ? asset('vendors_logo/' . $transaksi->vendor->logo)
                            : asset('images/logo.png');
                    @endphp
                    <img src="{{ $logoPath }}" alt="Logo" class="logo-img">
                @endif
                <h2>{{ $vendorName }}</h2>
                <p>{{ $vendorAddress }}</p>
                <p>{{ $vendorPhone }} | {{ $vendorEmail }}</p>
            </div>
        </div>

        <!-- TRANSACTION INFO SECTION -->
        <table class="info-table">
            <tr>
                <td style="width: 40%;">No. Invoice</td>
                <td style="width: 60%;">: {{ $transaksi->kode }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: {{ $transaksi->tanggal_dibuat->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Customer</td>
                <td>: {{ $transaksi->pelanggan->nama }}</td>
            </tr>
            <tr>
                <td>Pembayaran</td>
                <td>: {{ ucfirst($transaksi->payment_method ?? 'Transfer') }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>: {{ ucfirst($transaksi->status ?? 'Processing') }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- ITEMS SECTION -->
        <div class="items">
            @foreach ($transaksi->transaksiItem as $item)
                <div class="item-row">
                    <!-- Product Name -->
                    <div class="item-title">{{ $item->produk->nama_produk }}</div>

                    <!-- Quantity -->
                    <div class="item-spec">
                        <span class="spec-label">Quantity</span>
                        <span class="spec-value">{{ $item->kuantitas }} pcs</span>
                    </div>

                    <!-- Product Specifications -->
                    @if ($item->transaksiItemSpecifications && count($item->transaksiItemSpecifications) > 0)
                        <div class="item-details">
                            @foreach ($item->transaksiItemSpecifications as $spec)
                                @if ($spec->spesifikasiProduk && $spec->spesifikasiProduk->spesifikasi)
                                    <div class="item-spec">
                                        <span class="spec-label">
                                            {{ $spec->spesifikasiProduk->spesifikasi->nama_spesifikasi }}
                                        </span>
                                        <span class="spec-value">
                                            @php
                                                $hargaSatuan = 0;
                                                $totalHarga = $spec->price ?? 0;
                                                $nilai = $spec->nilai_spesifikasi ?? null;

                                                // If nilai_spesifikasi is empty, check value field
                                                if (empty($nilai) && isset($spec->value)) {
                                                    $nilai = $spec->value;
                                                }

                                                if ($spec->input_type == 'select' && $spec->bahan) {
                                                    // For select type (material selection)
                                                    echo $spec->bahan->nama_bahan .
                                                        ': ' .
                                                        $item->kuantitas .
                                                        ' x Rp ' .
                                                        number_format($totalHarga / $item->kuantitas, 0, ',', '.') .
                                                        ' = Rp ' .
                                                        number_format($totalHarga, 0, ',', '.');
                                                } elseif ($nilai && $spec->spesifikasiProduk->spesifikasi) {
                                                    // For numeric type (size, etc)
                                                    if ($totalHarga > 0 && $nilai > 0) {
                                                        $hargaSatuan = $totalHarga / $nilai;
                                                    }

                                                    echo number_format($nilai, 2, ',', '.') .
                                                        ' ' .
                                                        ($spec->spesifikasiProduk->spesifikasi->satuan ?? '') .
                                                        ' x Rp ' .
                                                        number_format($hargaSatuan, 0, ',', '.') .
                                                        ' = Rp ' .
                                                        number_format($totalHarga, 0, ',', '.');
                                                } else {
                                                    echo 'Rp ' . number_format($totalHarga, 0, ',', '.');
                                                }
                                            @endphp
                                        </span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <!-- Item Total -->
                    <div class="item-total">
                        <span>Total Item</span>
                        <span class="item-total-value">
                            Rp {{ number_format($item->harga_satuan * $item->kuantitas, 0, ',', '.') }}
                        </span>
                    </div>

                    <!-- Item Separator -->
                    @if (!$loop->last)
                        <div class="item-separator"></div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="divider"></div>

        <!-- TOTALS SECTION -->
        <table class="total-table">
            <tr>
                <td style="width: 60%; text-align: left;">Total</td>
                <td style="width: 40%; text-align: right;">
                    Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="text-align: left;">Terbayar</td>
                <td style="text-align: right;">
                    Rp {{ number_format($transaksi->terbayar ?? $transaksi->total_harga, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="text-align: left;">Kembali</td>
                <td style="text-align: right;">
                    Rp {{ number_format($transaksi->kembali ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        </table>

        <!-- FOOTER SECTION -->
        <div class="footer">
            <p>Terima kasih telah berbelanja!</p>
            <p>Estimasi Selesai: {{ \Carbon\Carbon::parse($transaksi->estimasi_selesai)->format('d/m/Y H:i') }}</p>
            <p>{{ $vendorName }} | {{ $vendorPhone }}</p>
        </div>
    </div>
</body>

</html>
