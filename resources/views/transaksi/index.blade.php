@extends('layouts.layouts_dashboard')

@section('title', 'Manajemen Transaksi')
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-column flex-md-row gap-3">
                <div>
                    <h3 class="card-title">Daftar Transaksi</h3>
                </div>
                <div class="d-flex flex-column flex-grow-1">
                    <form action="{{ route('transaksi.index') }}" method="GET" id="filter-form">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <circle cx="10" cy="10" r="7" />
                                            <line x1="21" y1="21" x2="15" y2="15" />
                                        </svg>
                                    </span>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control" placeholder="Cari kode/pelanggan...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select"
                                    onchange="document.getElementById('filter-form').submit()">
                                    <option value="">Semua Status</option>
                                    @foreach ($statusOptions as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ request('status') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('transaksi.create') }}" class="btn btn-primary w-124">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <line x1="12" y1="5" x2="12" y2="19" />
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                    </svg>
                                    Tambah Transaksi
                                </a>
                            </div>
                        </div>
                        <div class="row g-2 mt-2">
                            <div class="col-md-5">
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ request('start_date') }}" placeholder="Tanggal Mulai">
                            </div>
                            <div class="col-md-5">
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}"
                                    placeholder="Tanggal Akhir">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-filter"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path
                                            d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227z">
                                        </path>
                                    </svg>
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table card-table table-vcenter text-nowrap">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Metode Pembayaran</th>
                        <th>Progres</th>
                        <th class="w-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaksis as $transaksi)
                        <tr>
                            <td>
                                <a href="{{ route('transaksi.show', $transaksi->id) }}" class="text-reset">
                                    {{ $transaksi->kode }}
                                </a>
                            </td>
                            <td>{{ $transaksi->pelanggan->nama ?? 'N/A' }}</td>
                            <td>{{ $transaksi->tanggal_dibuat->format('d/m/Y H:i') }}</td>
                            <td>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow',
                                        'processing' => 'bg-blue',
                                        'quality_check' => 'bg-purple',
                                        'completed' => 'bg-green',
                                        'cancelled' => 'bg-red',
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Pending',
                                        'processing' => 'Diproses',
                                        'quality_check' => 'QC',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan',
                                    ];
                                @endphp
                                <span class="badge text-white {{ $statusColors[$transaksi->status] }}">
                                    {{ $statusLabels[$transaksi->status] }}
                                </span>
                            </td>
                            <td>{{ $transaksi->payment_method }}</td>
                            <td>
                                <div class="row align-items-center">
                                    <div class="col-12 col-lg-auto">{{ $transaksi->progress_percentage }}%</div>
                                    <div class="col">
                                        <div class="progress" style="width: 5rem">
                                            <div class="progress-bar"
                                                style="width: {{ $transaksi->progress_percentage }}%" role="progressbar"
                                                aria-valuenow="{{ $transaksi->progress_percentage }}" aria-valuemin="0"
                                                aria-valuemax="100"
                                                aria-label="{{ $transaksi->progress_percentage }}% Complete">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="{{ route('transaksi.show', $transaksi->id) }}"
                                        class="btn btn-icon btn-ghost-info" data-bs-toggle="tooltip" title="View">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                            <path
                                                d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('transaksi.edit', $transaksi->id) }}"
                                        class="btn btn-icon btn-ghost-warning" data-bs-toggle="tooltip" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                            <path
                                                d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                        </svg>
                                    </a>
                                    <form id="delete-form-{{ $transaksi->id }}"
                                        action="{{ route('transaksi.destroy', $transaksi->id) }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" class="btn btn-icon btn-ghost-danger"
                                        onclick="confirmDelete('delete-form-{{ $transaksi->id }}')"
                                        data-bs-toggle="tooltip" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M4 7l16 0" />
                                            <path d="M10 11l0 6" />
                                            <path d="M14 11l0 6" />
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="empty">
                                    <div class="empty-img">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="128"
                                            height="128" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                            <path d="M9 10l.01 0" />
                                            <path d="M15 10l.01 0" />
                                            <path d="M9.5 15.25a3.5 3.5 0 0 1 5 0" />
                                        </svg>
                                    </div>
                                    <p class="empty-title">Tidak ada data transaksi</p>
                                    <p class="empty-subtitle text-muted">
                                        Silahkan tambahkan transaksi baru atau ubah filter pencarian
                                    </p>
                                    <div class="empty-action">
                                        <a href="{{ route('transaksi.create') }}" class="btn btn-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <line x1="12" y1="5" x2="12" y2="19" />
                                                <line x1="5" y1="12" x2="19" y2="12" />
                                            </svg>
                                            Tambah Transaksi
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex align-items-center">
            {{ $transaksis->links('dev.components.pagination') }}
        </div>
    </div>
@endsection
