@extends('layouts.layouts_dashboard')

@section('title', 'Edit Alat')
@section('content')
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                @if ($errors->any())
                    <div class="alert alert-danger mb-3">
                        <h4 class="alert-title">Error!</h4>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('alat.update', $alat->id) }}" method="POST" class="card"
                    onsubmit="showLoading('Memperbarui alat...')" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-header">
                        <h3 class="card-title">Edit Alat</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Nama Alat</label>
                                    <input type="text" class="form-control @error('nama_alat') is-invalid @enderror"
                                        name="nama_alat" value="{{ old('nama_alat', $alat->nama_alat) }}"
                                        placeholder="Masukkan nama alat">
                                    @error('nama_alat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required">Merek</label>
                                    <input type="text" class="form-control @error('merek') is-invalid @enderror"
                                        name="merek" value="{{ old('merek', $alat->merek) }}"
                                        placeholder="Masukkan merek alat">
                                    @error('merek')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required">Model</label>
                                    <input type="text" class="form-control @error('model') is-invalid @enderror"
                                        name="model" value="{{ old('model', $alat->model) }}"
                                        placeholder="Masukkan model alat">
                                    @error('model')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required">Spesifikasi Alat</label>
                                    <textarea class="form-control @error('spesifikasi_alat') is-invalid @enderror" name="spesifikasi_alat" rows="4"
                                        placeholder="Masukkan spesifikasi alat">{{ old('spesifikasi_alat', $alat->spesifikasi_alat) }}</textarea>
                                    @error('spesifikasi_alat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" name="status">
                                        <option value="">Pilih status</option>
                                        <option value="aktif"
                                            {{ old('status', $alat->status) == 'aktif' ? 'selected' : '' }}>
                                            Aktif</option>
                                        <option value="maintenance"
                                            {{ old('status', $alat->status) == 'maintenance' ? 'selected' : '' }}>
                                            Maintenance
                                        </option>
                                        <option value="rusak"
                                            {{ old('status', $alat->status) == 'rusak' ? 'selected' : '' }}>
                                            Rusak</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required">Tanggal Pembelian</label>
                                    <input type="date"
                                        class="form-control @error('tanggal_pembelian') is-invalid @enderror"
                                        name="tanggal_pembelian"
                                        value="{{ old('tanggal_pembelian', $alat->tanggal_pembelian->format('Y-m-d')) }}">
                                    @error('tanggal_pembelian')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required">Kapasitas Cetak / Jam</label>
                                    <input type="number"
                                        class="form-control @error('kapasitas_cetak_per_jam') is-invalid @enderror"
                                        name="kapasitas_cetak_per_jam"
                                        value="{{ old('kapasitas_cetak_per_jam', $alat->kapasitas_cetak_per_jam) }}"
                                        placeholder="Masukkan kapasitas cetak per jam">
                                    @error('kapasitas_cetak_per_jam')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required">Tersedia</label>
                                    <select class="form-select @error('tersedia') is-invalid @enderror" name="tersedia">
                                        <option value="1"
                                            {{ old('tersedia', $alat->tersedia ? '1' : '0') == '1' ? 'selected' : '' }}>Ya
                                        </option>
                                        <option value="0"
                                            {{ old('tersedia', $alat->tersedia ? '1' : '0') == '0' ? 'selected' : '' }}>
                                            Tidak
                                        </option>
                                    </select>
                                    @error('tersedia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"></path>
                                <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                <path d="M14 4l0 4l-6 0l0 -4"></path>
                            </svg>
                            Update
                        </button>

                        <a href="{{ route('alat.index') }}" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24"
                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M18 6l-12 12"></path>
                                <path d="M6 6l12 12"></path>
                            </svg>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
