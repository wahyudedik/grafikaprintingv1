@extends('layouts.layouts_dashboard')

@section('title', 'Detail Bahan')
@section('content')
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detail Bahan</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3"> 
                                    <label class="form-label">Nama Bahan</label>
                                    <div class="form-control-plaintext">{{ $bahan->nama_bahan }}</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Satuan</label>
                                    <div class="form-control-plaintext">{{ $bahan->satuan }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Harga Pokok Produksi (HPP)</label>
                                    <div class="form-control-plaintext">Rp {{ number_format($bahan->hpp, 0, ',', '.') }}</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Stok</label>
                                    <div>
                                        @if($bahan->stok == 0)
                                            <span class="badge bg-danger text-white">Habis</span>
                                        @elseif($bahan->stok < 10)
                                            <span class="badge bg-warning text-white">Rendah ({{ $bahan->stok }})</span>
                                        @else
                                            <span class="badge bg-success text-white">{{ $bahan->stok }}</span>
                                        @endif
                                    </div> 
                                </div>
                            </div>
                            
                            @if($bahan->vendor)
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Vendor/Supplier</label>
                                    <div class="form-control-plaintext">{{ $bahan->vendor->name }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Wholesale Prices Section -->
                        @if($bahan->wholesalePrices && $bahan->wholesalePrices->count() > 0)
                        <div class="mt-4">
                            <h4 class="mb-3">Harga Grosir</h4>
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Min Quantity</th>
                                            <th>Max Quantity</th>
                                            <th>Harga</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bahan->wholesalePrices()->orderBy('min_quantity', 'asc')->get() as $price)
                                        <tr>
                                            <td>{{ $price->min_quantity }}</td>
                                            <td>{{ $price->max_quantity ?? 'Unlimited' }}</td>
                                            <td>Rp {{ number_format($price->harga, 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-info mt-4">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M12 9h.01"></path>
                                        <path d="M11 12h1v4h1"></path>
                                        <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="alert-title">Tidak ada harga grosir untuk bahan ini</h4>
                                    <div class="text-muted">Harga bahan akan menggunakan HPP standar untuk semua kuantitas.</div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('bahan.edit', $bahan->id) }}" class="btn btn-primary">
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

                        <a href="{{ route('bahan.index') }}" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M5 12l14 0"></path>
                                <path d="M5 12l6 6"></path>
                                <path d="M5 12l6 -6"></path>
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
