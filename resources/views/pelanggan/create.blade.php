@extends('layouts.layouts_dashboard')

@section('title', 'Tambah Pelanggan')
@section('content')
    <div class="container-xl">
        <div class="row g-3">
            <div class="col-12">
                <form action="{{ route('pelanggan.store') }}" method="POST" class="card"
                    onsubmit="showLoading('Menambahkan pelanggan...')" enctype="multipart/form-data"> 
                    @csrf
                    <div class="card-header">
                        <h3 class="card-title">Tambah Pelanggan Baru</h3> 
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6"> 
                                <div class="form-group">
                                    <label class="form-label required">Nama Pelanggan</label>
                                    <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama"
                                        value="{{ old('nama') }}" placeholder="Masukkan nama pelanggan">
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" placeholder="Masukkan email pelanggan">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nomor Telepon</label>
                                    <input type="text" class="form-control @error('no_telp') is-invalid @enderror" name="no_telp"
                                        value="{{ old('no_telp') }}" placeholder="Masukkan nomor telepon">
                                    @error('no_telp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Alamat</label>
                                    <textarea class="form-control @error('alamat') is-invalid @enderror" name="alamat"
                                        rows="3" placeholder="Masukkan alamat pelanggan">{{ old('alamat') }}</textarea>
                                    @error('alamat')
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
                            Simpan
                        </button>

                        <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">
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
