@extends('layouts.layouts_dashboard')

@section('content')
    <div class="container-xl">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Laporan Penjualan Harian
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="#" onclick="window.print();" class="btn btn-primary d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                                <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                                <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                            </svg>
                            Print
                        </a>
                        <a href="{{ route('laporan.export-penjualan', ['type' => 'daily', 'date' => $selectedDate->format('Y-m-d')]) }}"
                            class="btn btn-primary d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                <path d="M12 17v-6" />
                                <path d="M9.5 14.5l2.5 2.5l2.5 -2.5" />
                            </svg>
                            Export PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Filter</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('laporan.penjualan-harian') }}" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Pilih Tanggal</label>
                                <input type="date" class="form-control" name="date"
                                    value="{{ $selectedDate->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Terapkan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ringkasan Penjualan {{ $selectedDate->format('d F Y') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body p-3 text-center">
                                        <div class="h1 m-0">{{ $totalTransaksi }}</div>
                                        <div class="text-muted mb-3">Total Transaksi</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body p-3 text-center">
                                        <div class="h1 m-0">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
                                        <div class="text-muted mb-3">Total Penjualan</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Penjualan Per Jam</h3>
                    </div>
                    <div class="card-body">
                        <div id="chart-penjualan-per-jam"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detail Transaksi</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Kode Transaksi</th>
                                        <th>Waktu</th>
                                        <th>Pelanggan</th>
                                        <th>Status</th>
                                        <th>Metode Pembayaran</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transaksis as $index => $transaksi)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $transaksi->kode }}</td>
                                            <td>{{ $transaksi->tanggal_dibuat->format('H:i') }}</td>
                                            <td>{{ $transaksi->pelanggan->nama ?? 'Pelanggan Umum' }}</td>
                                            <td>
                                                @if ($transaksi->status == 'pending')
                                                    <span class="badge bg-yellow">Pending</span>
                                                @elseif($transaksi->status == 'processing')
                                                    <span class="badge bg-blue">Processing</span>
                                                @elseif($transaksi->status == 'quality_check')
                                                    <span class="badge bg-indigo">Quality Check</span>
                                                @elseif($transaksi->status == 'completed')
                                                    <span class="badge bg-green">Completed</span>
                                                @elseif($transaksi->status == 'cancelled')
                                                    <span class="badge bg-red">Cancelled</span>
                                                @endif
                                            </td>
                                            <td>{{ ucfirst($transaksi->payment_method ?? '-') }}</td>
                                            <td class="text-end">Rp
                                                {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada transaksi pada tanggal ini</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const penjualanPerJamData = {
                labels: [
                    @foreach (range(0, 23) as $hour)
                        "{{ sprintf('%02d', $hour) }}:00",
                    @endforeach
                ],
                data: [
                    @foreach (range(0, 23) as $hour)
                        {{ $penjualanPerJam[$hour]['total_penjualan'] ?? 0 }},
                    @endforeach
                ]
            };

            // Initialize chart
            const options = {
                series: [{
                    name: 'Penjualan',
                    data: penjualanPerJamData.data
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: {
                        show: false,
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 3,
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: penjualanPerJamData.labels,
                },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            return "Rp " + val.toLocaleString('id-ID');
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return "Rp " + val.toLocaleString('id-ID');
                        }
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector("#chart-penjualan-per-jam"), options);
            chart.render();
        });
    </script>
@endpush
