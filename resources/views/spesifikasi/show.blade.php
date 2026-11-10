@extends('layouts.layouts_dashboard')

@section('title', 'Detail Spesifikasi')
@section('content')
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detail Spesifikasi</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Spesifikasi</label>
                                    <div class="form-control-plaintext">{{ $spesifikasi->nama_spesifikasi }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tipe Input</label>
                                    <div>
                                        <span class="badge bg-{{ $spesifikasi->isNumeric() ? 'blue' : ($spesifikasi->isSelect() ? 'purple' : 'green') }}-lt">
                                            {{ $spesifikasi->tipe_input }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Satuan</label>
                                    <div class="form-control-plaintext">{{ $spesifikasi->satuan ?? '-' }}</div>
                                </div>
                            </div>

                            @if($spesifikasi->spesifikasiProduk->count() > 0)
                            <div class="col-12 mt-4">
                                <h4>Produk yang Menggunakan Spesifikasi Ini</h4>
                                <div class="table-responsive">
                                    <table class="table table-vcenter table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Nama Produk</th>
                                                <th>Nilai</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($spesifikasi->spesifikasiProduk as $spek)
                                            <tr>
                                                <td>{{ $spek->produk->nama_produk }}</td>
                                                <td>{{ $spek->nilai }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('spesifikasi.edit', $spesifikasi->id) }}" class="btn btn-primary">
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

                        <a href="{{ route('spesifikasi.index') }}" class="btn btn-secondary">
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
