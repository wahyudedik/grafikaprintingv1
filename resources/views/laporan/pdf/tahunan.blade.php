<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan Tahunan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h1 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 5px;
        }

        h2 {
            text-align: center;
            font-size: 14px;
            margin-top: 0;
            margin-bottom: 20px;
        }

        .summary {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 10px;
        }

        .summary-item {
            display: inline-block;
            width: 45%;
            margin: 5px;
            padding: 5px;
            border: 1px solid #eee;
            border-radius: 5px;
        }

        .summary-value {
            font-size: 18px;
            font-weight: bold;
        }

        .summary-label {
            font-size: 11px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-end {
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            color: #666;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0 10px 0;
        }
    </style>
</head>

<body>
    <h1>Grafika Printing</h1>
    <h2>Laporan Penjualan Tahunan - {{ $tahun }}</h2>

    <div class="summary">
        <div class="summary-item">
            <div class="summary-value">{{ count($transaksis) }}</div>
            <div class="summary-label">Total Transaksi</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
            <div class="summary-label">Total Penjualan</div>
        </div>
    </div>

    <div class="section-title">Ringkasan Penjualan Per Bulan</div>
    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th class="text-end">Jumlah Transaksi</th>
                <th class="text-end">Total Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $bulanIndonesia = [
                    '01' => 'Januari',
                    '02' => 'Februari',
                    '03' => 'Maret',
                    '04' => 'April',
                    '05' => 'Mei',
                    '06' => 'Juni',
                    '07' => 'Juli',
                    '08' => 'Agustus',
                    '09' => 'September',
                    '10' => 'Oktober',
                    '11' => 'November',
                    '12' => 'Desember',
                ];

                $penjualanPerBulan = $transaksis->groupBy(function ($item) {
                    return \Carbon\Carbon::parse($item->tanggal_dibuat)->format('m');
                });
            @endphp

            @foreach (range(1, 12) as $bulan)
                @php
                    $bulanKey = sprintf('%02d', $bulan);
                    $dataPerBulan = $penjualanPerBulan[$bulanKey] ?? collect([]);
                @endphp
                <tr>
                    <td>{{ $bulanIndonesia[$bulanKey] }}</td>
                    <td class="text-end">{{ $dataPerBulan->count() }}</td>
                    <td class="text-end">Rp {{ number_format($dataPerBulan->sum('total_harga'), 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan ini dicetak pada {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>

</html>
