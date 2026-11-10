@extends('layouts.layouts_dashboard')

@section('title', 'Detail Produk')
@section('content')
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detail Produk</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Product gallery -->
                                <div class="mb-4">
                                    @if (!empty($produk->gambar))
                                        <div id="carousel-produk" class="carousel slide" data-bs-ride="false">
                                            <div class="carousel-inner">
                                                @foreach ($produk->gambar as $index => $image)
                                                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                        <img src="{{ asset($image) }}" class="d-block w-100 rounded"
                                                            alt="{{ $produk->nama_produk }}"
                                                            style="max-height: 350px; object-fit: cover;">
                                                    </div>
                                                @endforeach
                                            </div>
                                            @if (count($produk->gambar) > 1)
                                                <button class="carousel-control-prev" type="button"
                                                    data-bs-target="#carousel-produk" data-bs-slide="prev">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                    <span class="visually-hidden">Previous</span>
                                                </button>
                                                <button class="carousel-control-next" type="button"
                                                    data-bs-target="#carousel-produk" data-bs-slide="next">
                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                    <span class="visually-hidden">Next</span>
                                                </button>
                                            @endif
                                        </div>
                                    @else
                                        <div class="empty">
                                            <div class="empty-img">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="icon icon-tabler icon-tabler-photo" width="128" height="128"
                                                    viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"
                                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M15 8h.01"></path>
                                                    <path
                                                        d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z">
                                                    </path>
                                                    <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5"></path>
                                                    <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3"></path>
                                                </svg>
                                            </div>
                                            <p class="empty-title">Tidak ada gambar produk</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h2>{{ $produk->nama_produk }}</h2>
                                    <span class="badge bg-primary text-white">{{ $produk->kategori->nama_kategori }}</span>
                                </div>

                                <div class="mb-3">
                                    <h4>Deskripsi</h4>
                                    <p>{{ $produk->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Specifications Section -->
                        <div class="mt-4">
                            <h4 class="mb-3">Spesifikasi Produk</h4>
                            @if ($produk->spesifikasiProduk->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table">
                                        <thead>
                                            <tr>
                                                <th>Nama Spesifikasi</th>
                                                <th>Tipe Input</th>
                                                <th>Wajib Diisi</th>
                                                <th>Pilihan</th>
                                                <th>Bahan Digunakan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($produk->spesifikasiProduk as $spec)
                                                <tr>
                                                    <td>{{ $spec->spesifikasi->nama_spesifikasi }}</td>
                                                    <td>{{ ucfirst($spec->spesifikasi->tipe_input) }}</td>
                                                    <td>
                                                        @if ($spec->wajib_diisi)
                                                            <span class="badge bg-success text-white">Ya</span>
                                                        @else
                                                            <span class="badge bg-secondary text-white">Tidak</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (!empty($spec->pilihan))
                                                            <ul class="list-unstyled mb-0">
                                                                @foreach ($spec->pilihan as $pilihan)
                                                                    <li>{{ $pilihan }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($spec->bahanSpesifikasiProduk->count() > 0)
                                                            <ul class="list-unstyled mb-0">
                                                                @foreach ($spec->bahanSpesifikasiProduk as $bahan)
                                                                    <li>{{ $bahan->nama_bahan }} ({{ $bahan->satuan }})
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    Produk ini belum memiliki spesifikasi.
                                </div>
                            @endif
                        </div>

                        <!-- Production Estimates Section -->
                        <div class="mt-4">
                            <h4 class="mb-3">Estimasi Produksi</h4>
                            @if ($produk->estimasiProduk->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table">
                                        <thead>
                                            <tr>
                                                <th>Alat</th>
                                                <th>Waktu Persiapan</th>
                                                <th>Waktu Produksi per Unit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($produk->estimasiProduk as $estimasi)
                                                <tr>
                                                    <td>{{ $estimasi->alat->nama_alat }}</td>
                                                    <td>{{ $estimasi->waktu_persiapan }} menit</td>
                                                    <td>{{ $estimasi->waktu_produksi_per_unit }} menit</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    Produk ini belum memiliki estimasi produksi.
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('produk.edit', $produk->id) }}" class="btn btn-primary">
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

                        <a href="{{ route('produk.index') }}" class="btn btn-secondary">
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
