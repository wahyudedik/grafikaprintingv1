@extends('layouts.layouts_dashboard')

@section('content')
    <div class="container-xl">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Laporan Penjualan Tahunan
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
                        <a href="{{ route('laporan.export-penjualan', ['type' => 'yearly', 'date' => $year]) }}"
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
                        <form action="{{ route('laporan.penjualan-tahunan') }}" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Pilih Tahun</label>
                                <select class="form-select" name="year">
                                    @foreach ($years as $y)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                            {{ $y }}</option>
                                    @endforeach
                                </select>
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
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ringkasan Penjualan {{ $year }}</h3>
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

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Penjualan Bulanan</h3>
                    </div>
                    <div class="card-body">
                        <div id="chart-penjualan-bulanan"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pelanggan Terbaik</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Pelanggan</th>
                                        <th class="text-end">Total Pembelian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pelangganTerbaik as $index => $pelanggan)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $pelanggan->nama }}</td>
                                            <td class="text-end">Rp
                                                {{ number_format($pelanggan->total_pembelian, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Distribusi Status Transaksi</h3>
                    </div>
                    <div class="card-body">
                        <div id="chart-status-distribution"></div>
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
            // Monthly sales chart
            const penjualanBulananData = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                data: [
                    @foreach (range(1, 12) as $month)
                        {{ $penjualanPerBulan[sprintf('%02d', $month)]['total_penjualan'] ?? 0 }},
                    @endforeach
                ]
            };

            const optionsMonthly = {
                series: [{
                    name: 'Penjualan',
                    data: penjualanBulananData.data
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: false,
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 3,
                        columnWidth: '50%',
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: penjualanBulananData.labels,
                    title: {
                        text: 'Bulan'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Total Penjualan (Rp)'
                    },
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

            const chartMonthly = new ApexCharts(document.querySelector("#chart-penjualan-bulanan"), optionsMonthly);
            chartMonthly.render();

            // Transaction status distribution chart
            const statusData = {
                labels: ['Pending', 'Processing', 'Quality Check', 'Completed', 'Cancelled'],
                series: [
                    {{ $transaksis->where('status', 'pending')->count() }},
                    {{ $transaksis->where('status', 'processing')->count() }},
                    {{ $transaksis->where('status', 'quality_check')->count() }},
                    {{ $transaksis->where('status', 'completed')->count() }},
                    {{ $transaksis->where('status', 'cancelled')->count() }}
                ]
            };

            const optionsStatus = {
                series: statusData.series,
                chart: {
                    width: 380,
                    type: 'pie',
                },
                labels: statusData.labels,
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                colors: ['#FFC107', '#3498DB', '#9B59B6', '#2ECC71', '#E74C3C']
            };

            const chartStatus = new ApexCharts(document.querySelector("#chart-status-distribution"), optionsStatus);
            chartStatus.render();
        });
    </script>
@endpush
