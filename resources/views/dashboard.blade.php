@extends('layouts.layouts_dashboard')

@section('title', 'Dashboard')
@section('content')
    <!-- User and Vendor Count Widgets -->
    <div class="row row-deck row-cards mb-4">
        <!-- Users Count Widget -->
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Users</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $userCount }}</div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="status-dot status-dot-animated bg-green d-block"></span>
                        </div>
                        <div class="col">
                            <a href="{{ route('pengguna.index') }}" class="text-decoration-none">Manage Users</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendors Count Widget -->
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Vendors</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $vendorCount }}</div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="status-dot status-dot-animated bg-blue d-block"></span>
                        </div>
                        <div class="col">
                            <a href="#" class="text-decoration-none">Manage Vendors</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Products Widget -->
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Products</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $productCount }}</div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="status-dot status-dot-animated bg-purple d-block"></span>
                        </div>
                        <div class="col">
                            <a href="{{ route('produk.index') }}" class="text-decoration-none">Manage Products</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Transactions Widget -->
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Today's Transactions</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $todayTransactions }}</div>
                        <div class="me-auto">
                            <span
                                class="{{ $todayGrowth >= 0 ? 'text-green' : 'text-red' }} d-inline-flex align-items-center lh-1">
                                {{ $todayGrowth }}%
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    @if ($todayGrowth >= 0)
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 17l6 -6l4 4l8 -8" />
                                        <path d="M14 7l7 0l0 7" />
                                    @else
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 7l6 6l4 -4l8 8" />
                                        <path d="M21 10l0 7l-7 0" />
                                    @endif
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="status-dot status-dot-animated bg-orange d-block"></span>
                        </div>
                        <div class="col">
                            <a href="{{ route('transaksi.index') }}" class="text-decoration-none">View Transactions</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Materials Widget -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Inventory Status</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Low Stock Materials</h4>
                        @if (count($lowStockMaterials) > 0)
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Material Name</th>
                                            <th>Current Stock</th>
                                            <th>Unit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lowStockMaterials as $material)
                                            <tr>
                                                <td>{{ $material->nama_bahan }}</td>
                                                <td>{!! $material->stock_status_label !!}</td>
                                                <td>{{ $material->satuan }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p>No materials with low stock.</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body p-3 text-center">
                                        <div class="h1 m-0 text-danger">{{ $outOfStockCount }}</div>
                                        <div class="text-muted mb-3">Out of Stock Items</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body p-3 text-center">
                                        <div class="h1 m-0">{{ $bahanCount }}</div>
                                        <div class="text-muted mb-3">Total Materials</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4>Order Status</h4>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-center mb-3">
                                                    <span
                                                        class="status-dot status-dot-animated bg-yellow d-block me-2"></span>
                                                    <div>Pending: <strong>{{ $pendingOrdersCount }}</strong></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-center mb-3">
                                                    <span
                                                        class="status-dot status-dot-animated bg-blue d-block me-2"></span>
                                                    <div>Processing: <strong>{{ $processingOrdersCount }}</strong></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-center mb-3">
                                                    <span class="status-dot bg-green d-block me-2"></span>
                                                    <div>Completed: <strong>{{ $completedOrdersCount }}</strong></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Stats Widgets -->
    <div class="row row-deck row-cards mb-4">
        <!-- Monthly Transactions Widget -->
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Monthly Transactions</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $monthlyTransactions }}</div>
                        <div class="me-auto">
                            <span
                                class="{{ $monthlyGrowth >= 0 ? 'text-green' : 'text-red' }} d-inline-flex align-items-center lh-1">
                                {{ $monthlyGrowth }}%
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    @if ($monthlyGrowth >= 0)
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 17l6 -6l4 4l8 -8" />
                                        <path d="M14 7l7 0l0 7" />
                                    @else
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 7l6 6l4 -4l8 8" />
                                        <path d="M21 10l0 7l-7 0" />
                                    @endif
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="status-dot status-dot-animated bg-yellow d-block"></span>
                        </div>
                        <div class="col">
                            <a href="{{ route('transaksi.index') }}" class="text-decoration-none">View Monthly Report</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue Widget -->
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Monthly Revenue</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">Rp {{ number_format($monthlyRevenue, 1) }}M</div>
                        <div class="me-auto">
                            <span
                                class="{{ $monthlyRevenueGrowth >= 0 ? 'text-green' : 'text-red' }} d-inline-flex align-items-center lh-1">
                                {{ $monthlyRevenueGrowth }}%
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    @if ($monthlyRevenueGrowth >= 0)
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 17l6 -6l4 4l8 -8" />
                                        <path d="M14 7l7 0l0 7" />
                                    @else
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 7l6 6l4 -4l8 8" />
                                        <path d="M21 10l0 7l-7 0" />
                                    @endif
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="status-dot status-dot-animated bg-teal d-block"></span>
                        </div>
                        <div class="col">
                            <a href="{{ route('laporan.penjualan-bulanan') }}" class="text-decoration-none">View
                                Financial Report</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Order Value Widget -->
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Average Order Value</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">Rp {{ number_format($averageOrderValue, 0) }}K</div>
                        <div class="me-auto">
                            <span
                                class="{{ $averageOrderValueGrowth >= 0 ? 'text-green' : 'text-red' }} d-inline-flex align-items-center lh-1">
                                {{ $averageOrderValueGrowth }}%
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    @if ($averageOrderValueGrowth >= 0)
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 17l6 -6l4 4l8 -8" />
                                        <path d="M14 7l7 0l0 7" />
                                    @else
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 7l6 6l4 -4l8 8" />
                                        <path d="M21 10l0 7l-7 0" />
                                    @endif
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="status-dot status-dot-animated bg-cyan d-block"></span>
                        </div>
                        <div class="col">
                            <a href="{{ route('laporan.penjualan-harian') }}" class="text-decoration-none">View
                                Analytics</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Transactions</h3>
                <div class="card-actions">
                    <a href="{{ route('transaksi.index') }}" class="btn btn-primary">
                        View All
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Transaction Code</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $recentTransactions = \App\Models\Vendor\Transaksi::with('pelanggan')
                                ->orderBy('tanggal_dibuat', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp

                        @forelse($recentTransactions as $transaction)
                            <tr>
                                <td>{{ $transaction->kode }}</td>
                                <td>{{ $transaction->pelanggan->nama ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($transaction->tanggal_dibuat)->format('d M Y') }}</td>
                                <td>Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</td>
                                <td>
                                    @if ($transaction->status == 'pending')
                                        <span class="badge bg-yellow text-white">Pending</span>
                                    @elseif($transaction->status == 'processing')
                                        <span class="badge bg-blue text-white">Processing</span>
                                    @elseif($transaction->status == 'quality_check')
                                        <span class="badge bg-purple text-white">Quality Check</span>
                                    @elseif($transaction->status == 'completed')
                                        <span class="badge bg-green text-white">Completed</span>
                                    @elseif($transaction->status == 'cancelled')
                                        <span class="badge bg-red text-white">Cancelled</span>
                                    @else
                                        <span
                                            class="badge bg-secondary text-white">{{ ucfirst($transaction->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('transaksi.generateInvoice', $transaction->id) }}"
                                        class="btn btn-sm btn-outline-primary">Invoice</a>
                                    <a href="{{ route('transaksi.show', $transaction->id) }}"
                                        class="btn btn-sm btn-outline-primary">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No transactions found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Order Progress -->
    <div class="col-md-12 mb-3">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Order Progress</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="progress mb-3">
                            @php
                                $pendingPercentage =
                                    $pendingOrdersCount + $processingOrdersCount + $completedOrdersCount > 0
                                        ? ($pendingOrdersCount /
                                                ($pendingOrdersCount +
                                                    $processingOrdersCount +
                                                    $completedOrdersCount)) *
                                            100
                                        : 0;

                                $processingPercentage =
                                    $pendingOrdersCount + $processingOrdersCount + $completedOrdersCount > 0
                                        ? ($processingOrdersCount /
                                                ($pendingOrdersCount +
                                                    $processingOrdersCount +
                                                    $completedOrdersCount)) *
                                            100
                                        : 0;

                                $completedPercentage =
                                    $pendingOrdersCount + $processingOrdersCount + $completedOrdersCount > 0
                                        ? ($completedOrdersCount /
                                                ($pendingOrdersCount +
                                                    $processingOrdersCount +
                                                    $completedOrdersCount)) *
                                            100
                                        : 0;
                            @endphp

                            <div class="progress-bar bg-yellow" style="width: {{ $pendingPercentage }}%"
                                role="progressbar" aria-valuenow="{{ $pendingPercentage }}" aria-valuemin="0"
                                aria-valuemax="100" aria-label="Pending">
                                <span class="visually-hidden">{{ $pendingPercentage }}% Pending</span>
                            </div>
                            <div class="progress-bar bg-blue" style="width: {{ $processingPercentage }}%"
                                role="progressbar" aria-valuenow="{{ $processingPercentage }}" aria-valuemin="0"
                                aria-valuemax="100" aria-label="Processing">
                                <span class="visually-hidden">{{ $processingPercentage }}% Processing</span>
                            </div>
                            <div class="progress-bar bg-green" style="width: {{ $completedPercentage }}%"
                                role="progressbar" aria-valuenow="{{ $completedPercentage }}" aria-valuemin="0"
                                aria-valuemax="100" aria-label="Completed">
                                <span class="visually-hidden">{{ $completedPercentage }}% Completed</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="legend me-2 bg-yellow"></span>
                                    <span>Pending ({{ $pendingOrdersCount }})</span>
                                    <span class="ms-auto">{{ number_format($pendingPercentage, 1) }}%</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="legend me-2 bg-blue"></span>
                                    <span>Processing ({{ $processingOrdersCount }})</span>
                                    <span class="ms-auto">{{ number_format($processingPercentage, 1) }}%</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="legend me-2 bg-green"></span>
                                    <span>Completed ({{ $completedOrdersCount }})</span>
                                    <span class="ms-auto">{{ number_format($completedPercentage, 1) }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row row-deck row-cards">
        <!-- Popular Products Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Popular Products</h3>
                </div>
                <div class="card-body">
                    <div id="popular-products-chart" style="height: 250px;"></div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Monthly Revenue (Last 6 Months)</h3>
                </div>
                <div class="card-body">
                    <div id="monthly-revenue-chart" style="height: 250px;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Popular Products Chart
            var popularProductsOptions = {
                series: [{
                    data: @json($popularProducts['data'])
                }],
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: {
                        show: false,
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: true,
                    }
                },
                colors: ['#206bc4'],
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: @json($popularProducts['labels']),
                }
            };

            var popularProductsChart = new ApexCharts(document.querySelector("#popular-products-chart"),
                popularProductsOptions);
            popularProductsChart.render();

            // Monthly Revenue Chart
            var monthlyRevenueOptions = {
                series: [{
                    name: 'Revenue',
                    data: @json($revenueData['data'])
                }],
                chart: {
                    height: 250,
                    type: 'line',
                    toolbar: {
                        show: false,
                    }
                },
                colors: ['#2fb344'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: @json($revenueData['labels']),
                },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            return "Rp " + val.toFixed(1) + "M";
                        }
                    }
                },
                markers: {
                    size: 5,
                    hover: {
                        size: 7
                    }
                }
            };

            var monthlyRevenueChart = new ApexCharts(document.querySelector("#monthly-revenue-chart"),
                monthlyRevenueOptions);
            monthlyRevenueChart.render();
        });
    </script>
@endpush
