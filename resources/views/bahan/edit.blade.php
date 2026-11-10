@extends('layouts.layouts_dashboard')

@section('title', 'Edit Bahan')
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

                <form action="{{ route('bahan.update', $bahan->id) }}" method="POST" class="card"
                    onsubmit="showLoading('Memperbarui bahan...')">
                    @csrf
                    @method('PUT')
                    <div class="card-header">
                        <h3 class="card-title">Edit Bahan</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Nama Bahan</label>
                                    <input type="text" class="form-control @error('nama_bahan') is-invalid @enderror"
                                        name="nama_bahan" value="{{ old('nama_bahan', $bahan->nama_bahan) }}"
                                        placeholder="Masukkan nama bahan">
                                    @error('nama_bahan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required">Satuan</label>
                                    <input type="text" class="form-control @error('satuan') is-invalid @enderror"
                                        name="satuan" value="{{ old('satuan', $bahan->satuan) }}"
                                        placeholder="Contoh: lembar, meter, kg">
                                    @error('satuan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Harga Pokok Produksi (HPP)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" step="0.01"
                                            class="form-control @error('hpp') is-invalid @enderror" name="hpp"
                                            value="{{ old('hpp', $bahan->hpp) }}" placeholder="Masukkan HPP bahan">
                                    </div>
                                    @error('hpp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required">Stok</label>
                                    <input type="number" class="form-control @error('stok') is-invalid @enderror"
                                        name="stok" value="{{ old('stok', $bahan->stok) }}"
                                        placeholder="Masukkan jumlah stok">
                                    @error('stok')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Wholesale Prices Section -->
                        <div class="mt-4">
                            <h4>Harga Grosir</h4>
                            <p class="text-muted">Atur tier harga grosir untuk pembelian dalam jumlah banyak</p>

                            <div id="wholesale-container">
                                <!-- Existing wholesale prices -->
                                @if ($bahan->wholesalePrices && $bahan->wholesalePrices->count() > 0)
                                    @foreach ($bahan->wholesalePrices as $price)
                                        <div class="row g-3 mb-3 wholesale-row"
                                            id="wholesale-row-existing-{{ $price->id }}">
                                            <input type="hidden" name="wholesale_id[]" value="{{ $price->id }}">
                                            <div class="col-md-3">
                                                <label class="form-label">Minimum Qty</label>
                                                <input type="number" class="form-control" name="wholesale_min_qty[]"
                                                    value="{{ $price->min_quantity }}" placeholder="Min quantity"
                                                    min="1" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Maximum Qty</label>
                                                <input type="number" class="form-control" name="wholesale_max_qty[]"
                                                    value="{{ $price->max_quantity }}"
                                                    placeholder="Max quantity or leave empty">
                                                <small class="form-hint">Kosongkan untuk unlimited</small>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Harga (Rp)</label>
                                                <input type="number" step="0.01" class="form-control"
                                                    name="wholesale_price[]" value="{{ $price->harga }}"
                                                    placeholder="Wholesale price" required>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="button" class="btn btn-outline-danger remove-existing-row"
                                                    data-row="wholesale-row-existing-{{ $price->id }}"
                                                    data-id="{{ $price->id }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                        height="24" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M18 6l-12 12" />
                                                        <path d="M6 6l12 12" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <div id="new-wholesale-container">
                                <!-- New wholesale rows will be added here -->
                            </div>

                            <input type="hidden" name="deleted_wholesale_ids" id="deleted-wholesale-ids">

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
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"></path>
                                <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                <path d="M14 4l0 4l-6 0l0 -4"></path>
                            </svg>
                            Update
                        </button>

                        <a href="{{ route('bahan.index') }}" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24"
                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
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
                const newContainer = document.getElementById('new-wholesale-container');
                const addButton = document.getElementById('add-wholesale-row');
                const deletedIdsInput = document.getElementById('deleted-wholesale-ids');
                let rowCount = 0;
                let deletedIds = [];

                // Add new wholesale price row
                addButton.addEventListener('click', function() {
                    addWholesalePriceRow();
                });

                // Handle removal of existing rows
                document.querySelectorAll('.remove-existing-row').forEach(button => {
                    button.addEventListener('click', function() {
                        const rowId = this.getAttribute('data-row');
                        const priceId = this.getAttribute('data-id');

                        // Add ID to deleted list
                        deletedIds.push(priceId);
                        deletedIdsInput.value = deletedIds.join(',');

                        // Remove the row from DOM
                        document.getElementById(rowId).remove();
                    });
                });

                function addWholesalePriceRow(minQty = '', maxQty = '', price = '') {
                    const rowId = `new-wholesale-row-${rowCount}`;
                    const html = `
                    <div class="row g-3 mb-3 wholesale-row" id="${rowId}">
                        <div class="col-md-3">
                            <label class="form-label">Minimum Qty</label>
                            <input type="number" class="form-control" name="new_wholesale_min_qty[]" value="${minQty}" placeholder="Min quantity" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Maximum Qty</label>
                            <input type="number" class="form-control" name="new_wholesale_max_qty[]" value="${maxQty}" placeholder="Max quantity or leave empty">
                            <small class="form-hint">Kosongkan untuk unlimited</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harga (Rp)</label>
                            <input type="number" step="0.01" class="form-control" name="new_wholesale_price[]" value="${price}" placeholder="Wholesale price" required>
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

                    newContainer.insertAdjacentHTML('beforeend', html);
                    rowCount++;

                    // Add event listener to the new remove button
                    document.querySelectorAll('.remove-row').forEach(button => {
                        button.addEventListener('click', function() {
                            const rowId = this.getAttribute('data-row');
                            document.getElementById(rowId).remove();
                        });
                    });
                }
            });
        </script>
    @endpush
@endsection
