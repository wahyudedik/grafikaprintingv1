@extends('layouts.layouts_dashboard')

@section('title', 'Detail Pelanggan')
@section('content')
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detail Pelanggan</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3"> 
                                    <label class="form-label">Kode Pelanggan</label>
                                    <div class="form-control-plaintext">{{ $pelanggan->kode }}</div>
                                </div>

                                <div class="mb-3"> 
                                    <label class="form-label">Nama Pelanggan</label>
                                    <div class="form-control-plaintext">{{ $pelanggan->nama }}</div>
                                </div>
                                
                                <div class="mb-3"> 
                                    <label class="form-label">Alamat</label>
                                    <div class="form-control-plaintext">{{ $pelanggan->alamat ?: '-' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <div class="form-control-plaintext">{{ $pelanggan->email ?: '-' }}</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Nomor Telepon</label>
                                    <div class="form-control-plaintext">{{ $pelanggan->no_telp ?: '-' }}</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Transaksi Terakhir</label>
                                    <div class="form-control-plaintext">
                                        @if($pelanggan->transaksi_terakhir)
                                            {{ $pelanggan->transaksi_terakhir->format('d M Y H:i') }}
                                        @else
                                            <span class="text-muted">Belum ada transaksi</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            @if($pelanggan->transaksi->count() > 0)
                            <div class="col-12 mt-4">
                                <h4>Riwayat Transaksi</h4>
                                <div class="table-responsive">
                                    <table class="table table-vcenter">
                                        <thead>
                                            <tr>
                                                <th>No. Transaksi</th>
                                                <th>Tanggal</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th class="w-1"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pelanggan->transaksi->take(5) as $transaksi)
                                            <tr>
                                                <td>{{ $transaksi->kode }}</td>
                                                <td>{{ $transaksi->created_at->format('d M Y') }}</td>
                                                <td>Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
                                                <td>
                                                    @if($transaksi->status == 'completed')
                                                        <span class="badge bg-success">Selesai</span>
                                                    @elseif($transaksi->status == 'pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                    @elseif($transaksi->status == 'cancelled')
                                                        <span class="badge bg-danger">Dibatalkan</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $transaksi->status }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-ghost-primary">Detail</a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($pelanggan->transaksi->count() > 5)
                                    <div class="text-center mt-3">
                                        <a href="#" class="btn btn-sm btn-secondary">Lihat Semua Transaksi</a>
                                    </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('pelanggan.edit', $pelanggan->id) }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24"
                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                                <path d="M16 5l3 3"></path>
                            </svg>
                            Edit
                        </a>

                        <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M5 12l14 0"></path>
                                <path d="M5 12l6 6"></path>
                                <path d="M5 12l6 -6"></path>
                            </svg>
                            Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
