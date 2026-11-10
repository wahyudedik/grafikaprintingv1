@extends('layouts.layouts_dashboard')

@section('title', 'Detail Kategori Produk')
@section('content')
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detail Kategori Produk</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3"> 
                                    <label class="form-label">Nama Kategori</label>
                                    <div class="form-control-plaintext">{{ $kategoriProduk->nama_kategori }}</div>
                                </div> 

                                <div class="mb-3">
                                    <label class="form-label">Slug</label>
                                    <div class="form-control-plaintext">{{ $kategoriProduk->slug }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Dibuat</label>
                                    <div class="form-control-plaintext">{{ $kategoriProduk->created_at->format('d M Y, H:i') }}</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Terakhir Diperbarui</label>
                                    <div class="form-control-plaintext">{{ $kategoriProduk->updated_at->format('d M Y, H:i') }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Related Products Section -->
                        <div class="mt-4">
                            <h4 class="mb-3">Produk dalam Kategori Ini</h4>
                            
                            @if($kategoriProduk->produk && $kategoriProduk->produk->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Nama Produk</th>
                                            {{-- <th>Harga</th>
                                            <th>Status</th> --}}
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($kategoriProduk->produk as $produk)
                                        <tr>
                                            <td>{{ $produk->nama_produk }}</td>
                                            {{-- <td>Rp {{ number_format($produk->harga, 0, ',', '.') }}</td> --}}
                                            {{-- <td>
                                                @if($produk->is_active)
                                                    <span class="badge bg-success ">Aktif</span>
                                                @else
                                                    <span class="badge bg-danger">Nonaktif</span>
                                                @endif
                                            </td> --}}
                                            <td>
                                                <a href="{{ route('produk.show', $produk->id) }}" class="btn btn-sm btn-primary">
                                                    Lihat
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="alert alert-info mt-3">
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
                                        <h4 class="alert-title">Tidak ada produk dalam kategori ini</h4>
                                        <div class="text-muted">Kategori ini belum memiliki produk yang terkait.</div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('kategori-produk.edit', $kategoriProduk->id) }}" class="btn btn-primary">
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

                        <a href="{{ route('kategori-produk.index') }}" class="btn btn-secondary">
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
