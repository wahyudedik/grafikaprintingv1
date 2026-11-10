@extends('layouts.layouts_dashboard')

@section('title', 'Tambah Bahan')
@section('content')
    <div class="container-xl">
        <div class="row g-3">
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

                <form action="{{ route('bahan.store') }}" method="POST" class="card"
                    onsubmit="showLoading('Menambahkan bahan...')">
                    @csrf
                    <div class="card-header">
                        <h3 class="card-title">Tambah Bahan Baru</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label required">Nama Bahan</label>
                                    <input type="text" class="form-control @error('nama_bahan') is-invalid @enderror"
                                        name="nama_bahan" value="{{ old('nama_bahan') }}" placeholder="Masukkan nama bahan">
                                    @error('nama_bahan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label required">Satuan</label>
                                    <input type="text" class="form-control @error('satuan') is-invalid @enderror"
                                        name="satuan" value="{{ old('satuan') }}" placeholder="Contoh: lembar, meter, kg">
                                    @error('satuan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label required">Harga Pokok Produksi (HPP)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" step="0.01"
                                            class="form-control @error('hpp') is-invalid @enderror" name="hpp"
                                            value="{{ old('hpp') }}" placeholder="Masukkan HPP bahan">
                                    </div>
                                    @error('hpp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label required">Stok</label>
                                    <input type="number" class="form-control @error('stok') is-invalid @enderror"
                                        name="stok" value="{{ old('stok', 0) }}" placeholder="Masukkan jumlah stok">
                                    @error('stok')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Wholesale Prices Section -->
                        <div class="mt-4">
                            <h4>Harga Grosir (Opsional)</h4>
                            <p class="text-muted">Tambahkan tier harga grosir untuk pembelian dalam jumlah banyak</p>

                            <div id="wholesale-container">
                                <!-- Dynamic rows will be added here -->
                            </div>

                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-primary" id="add-wholesale-row">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 5l0 14" />
                                        <path d="M5 12l14 0" />
                                    </svg>
                                    Tambah Tier Harga Grosir
                                </button>
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

                        <a href="{{ route('bahan.index') }}" class="btn btn-secondary">
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('wholesale-container');
                const addButton = document.getElementById('add-wholesale-row');
                let rowCount = 0;

                // Add new wholesale price row
                addButton.addEventListener('click', function() {
                    addWholesalePriceRow();
                });

                function addWholesalePriceRow(minQty = '', maxQty = '', price = '') {
                    const rowId = `wholesale-row-${rowCount}`;
                    const html = `
                    <div class="row g-3 mb-3 wholesale-row" id="${rowId}">
                        <div class="col-md-3">
                            <label class="form-label">Minimum Qty</label>
                            <input type="number" class="form-control" name="wholesale_min_qty[]" value="${minQty}" placeholder="Min quantity" min="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Maximum Qty</label>
                            <input type="number" class="form-control" name="wholesale_max_qty[]" value="${maxQty}" placeholder="Max quantity or leave empty">
                            <small class="form-hint">Kosongkan untuk unlimited</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harga (Rp)</label>
                            <input type="number" step="0.01" class="form-control" name="wholesale_price[]" value="${price}" placeholder="Wholesale price">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger remove-row" data-row="${rowId}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M18 6l-12 12" />
                                    <path d="M6 6l12 12" />
                                </svg>
                                Hapus
                            </button>
                        </div>
                    </div>
                `;

                    container.insertAdjacentHTML('beforeend', html);
                    rowCount++;

                    // Add event listener to the new remove button
                    document.querySelector(`#${rowId} .remove-row`).addEventListener('click', function() {
                        document.getElementById(rowId).remove();
                    });
                }
            });
        </script>
    @endpush
@endsection
