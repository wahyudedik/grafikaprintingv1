@extends('layouts.layouts_dashboard')

@section('title', 'Edit Transaksi')
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

                <form action="{{ route('transaksi.update', $transaksi->id) }}" method="POST" class="card"
                    id="transaction-form" onsubmit="showLoading('Memperbarui transaksi...')">
                    @csrf
                    @method('PUT')
                    <div class="card-header">
                        <h3 class="card-title">Edit Transaksi: {{ $transaksi->kode }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Customer Section -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label required">Pelanggan</label>
                                    <select class="form-select @error('pelanggan_id') is-invalid @enderror"
                                        name="pelanggan_id" required>
                                        <option value="">Pilih Pelanggan</option>
                                        @foreach ($pelanggans as $pelanggan)
                                            <option value="{{ $pelanggan->id }}"
                                                {{ old('pelanggan_id', $transaksi->pelanggan_id) == $pelanggan->id ? 'selected' : '' }}>
                                                {{ $pelanggan->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('pelanggan_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Payment Method Section -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label required">Metode Pembayaran</label>
                                    <select class="form-select @error('payment_method') is-invalid @enderror"
                                        name="payment_method" required>
                                        <option value="">Pilih Metode Pembayaran</option>
                                        @foreach ($paymentMethods as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('payment_method', $transaksi->payment_method) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status Section -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label required">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" name="status"
                                        required>
                                        @foreach ($statusOptions as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('status', $transaksi->status) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Estimated Date Section -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label required">Estimasi Selesai</label>
                                    <input type="date"
                                        class="form-control @error('estimasi_selesai') is-invalid @enderror"
                                        name="estimasi_selesai"
                                        value="{{ old('estimasi_selesai', $transaksi->estimasi_selesai->format('Y-m-d')) }}"
                                        required>
                                    @error('estimasi_selesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Notes Section -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Catatan</label>
                                    <textarea class="form-control @error('catatan') is-invalid @enderror" name="catatan" rows="3"
                                        placeholder="Masukkan catatan transaksi">{{ old('catatan', $transaksi->catatan) }}</textarea>
                                    @error('catatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Transaction Items Section -->
                        <div class="mt-4">
                            <h4>Item Transaksi</h4>
                            <p class="text-muted">Edit produk yang dibeli dalam transaksi ini</p>

                            <div id="items-container">
                                <!-- Existing Items -->
                                @foreach ($transaksi->transaksiItem as $index => $item)
                                    <div class="row g-3 mb-4 item-row" id="item-row-existing-{{ $item->id }}">
                                        <div class="col-md-12 border rounded p-3 position-relative">
                                            <button type="button"
                                                class="btn btn-sm btn-ghost-danger position-absolute top-0 end-0 mt-1 me-1"
                                                onclick="removeItemRow('item-row-existing-{{ $item->id }}')">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M18 6l-12 12" />
                                                    <path d="M6 6l12 12" />
                                                </svg>
                                            </button>

                                            <input type="hidden" name="items[{{ $index }}][id]"
                                                value="{{ $item->id }}">

                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label required">Produk</label>
                                                    <select class="form-select product-select"
                                                        name="items[{{ $index }}][produk_id]" required
                                                        onchange="loadProductSpecifications('item-row-existing-{{ $item->id }}', this.value, true)">
                                                        <option value="">Pilih Produk</option>
                                                        @foreach ($produks as $produk)
                                                            <option value="{{ $produk->id }}"
                                                                data-product='@json($produk)'
                                                                {{ $item->produk_id == $produk->id ? 'selected' : '' }}>
                                                                {{ $produk->nama_produk }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label required">Kuantitas</label>
                                                    <input type="number" class="form-control item-quantity"
                                                        name="items[{{ $index }}][kuantitas]" min="1"
                                                        value="{{ $item->kuantitas }}" required
                                                        onchange="calculateSubtotal('item-row-existing-{{ $item->id }}')">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label required">Harga Satuan</label>
                                                    <input type="number" class="form-control item-price"
                                                        name="items[{{ $index }}][harga_satuan]" min="0"
                                                        step="0.01" value="{{ $item->harga_satuan }}" required
                                                        onchange="calculateSubtotal('item-row-existing-{{ $item->id }}')">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Subtotal</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" class="form-control item-subtotal" readonly
                                                            value="{{ number_format($item->kuantitas * $item->harga_satuan, 0, ',', '.') }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <div id="item-row-existing-{{ $item->id }}-specifications"
                                                        class="specifications-container">
                                                        <!-- Existing specifications -->
                                                        @if ($item->transaksiItemSpecifications->count() > 0)
                                                            <div class="mt-3">
                                                                <h5>Spesifikasi Produk</h5>
                                                                <div class="row g-3">
                                                                    @foreach ($item->transaksiItemSpecifications as $spec)
                                                                        <div class="col-md-6">
                                                                            <div class="mb-3">
                                                                                <label class="form-label">
                                                                                    {{ $spec->spesifikasiProduk->spesifikasi->nama_spesifikasi ?? 'Spesifikasi' }}
                                                                                </label>
                                                                                <input type="text" class="form-control"
                                                                                    name="items[{{ $index }}][specifications][{{ $spec->spesifikasi_produk_id }}][value]"
                                                                                    value="{{ $spec->value }}"
                                                                                    {{ $spec->spesifikasiProduk->wajib_diisi ? 'required' : '' }}>

                                                                                <input type="hidden"
                                                                                    name="items[{{ $index }}][specifications][{{ $spec->spesifikasi_produk_id }}][input_type]"
                                                                                    value="{{ $spec->input_type }}">
                                                                                <input type="hidden"
                                                                                    name="items[{{ $index }}][specifications][{{ $spec->spesifikasi_produk_id }}][price]"
                                                                                    value="{{ $spec->price }}">

                                                                                @if ($spec->bahan)
                                                                                    <div class="mt-2">
                                                                                        <label
                                                                                            class="form-label">Bahan</label>
                                                                                        <select class="form-select"
                                                                                            name="items[{{ $index }}][specifications][{{ $spec->spesifikasi_produk_id }}][bahan_id]">
                                                                                            <option value="">Pilih
                                                                                                Bahan</option>
                                                                                            @foreach ($spec->spesifikasiProduk->bahanSpesifikasiProduk as $bahan)
                                                                                                <option
                                                                                                    value="{{ $bahan->id }}"
                                                                                                    {{ $spec->bahan_id == $bahan->id ? 'selected' : '' }}>
                                                                                                    {{ $bahan->nama_bahan }}
                                                                                                    ({{ $bahan->satuan }})
                                                                                                </option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div id="new-items-container">
                                <!-- New items will be added here -->
                            </div>

                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-primary" id="add-item-row">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 5l0 14" />
                                        <path d="M5 12l14 0" />
                                    </svg>
                                    Tambah Item Baru
                                </button>
                            </div>
                        </div>

                        <!-- Total Section -->
                        <div class="mt-4 border-top pt-3">
                            <div class="row">
                                <div class="col-md-6 ms-auto">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <tr>
                                                <th class="text-end">Total:</th>
                                                <td class="text-end" id="total-display">
                                                    Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                                                </td>
                                                <input type="hidden" name="total_harga" id="total-input"
                                                    value="{{ $transaksi->total_harga }}">
                                            </tr>
                                        </table>
                                    </div>
                                </div>
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
                            Simpan Perubahan
                        </button>

                        <a href="{{ route('transaksi.show', $transaksi->id) }}" class="btn btn-secondary">
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
                const newItemsContainer = document.getElementById('new-items-container');
                const addItemButton = document.getElementById('add-item-row');
                let itemRowCount = {{ count($transaksi->transaksiItem) }};

                // Products data for dropdown and specifications
                const products = @json($produks);

                // Add item row
                addItemButton.addEventListener('click', function() {
                    addItemRow();
                });

                function addItemRow() {
                    const rowId = `item-row-new-${itemRowCount}`;

                    let productOptions = '';
                    products.forEach(product => {
                        productOptions +=
                            `<option value="${product.id}" data-product='${JSON.stringify(product)}'>${product.nama_produk}</option>`;
                    });

                    const html = `
                    <div class="row g-3 mb-4 item-row" id="${rowId}">
                        <div class="col-md-12 border rounded p-3 position-relative">
                            <button type="button" class="btn btn-sm btn-ghost-danger position-absolute top-0 end-0 mt-1 me-1"
                                onclick="removeItemRow('${rowId}')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M18 6l-12 12" />
                                    <path d="M6 6l12 12" />
                                </svg>
                            </button>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Produk</label>
                                    <select class="form-select product-select" name="items[${itemRowCount}][produk_id]" required
                                        onchange="loadProductSpecifications('${rowId}', this.value)">
                                        <option value="">Pilih Produk</option>
                                        ${productOptions}
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label required">Kuantitas</label>
                                    <input type="number" class="form-control item-quantity" name="items[${itemRowCount}][kuantitas]"
                                        min="1" value="1" required onchange="calculateSubtotal('${rowId}')">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label required">Harga Satuan</label>
                                    <input type="number" class="form-control item-price" name="items[${itemRowCount}][harga_satuan]"
                                        min="0" step="0.01" value="0" required onchange="calculateSubtotal('${rowId}')">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Subtotal</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control item-subtotal" readonly value="0">
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <div id="${rowId}-specifications" class="specifications-container">
                                        <!-- Specifications will be loaded here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    `;

                    newItemsContainer.insertAdjacentHTML('beforeend', html);
                    itemRowCount++;
                    calculateTotal();
                }

                // Initialize existing items
                document.querySelectorAll('.item-row').forEach(row => {
                    const productSelect = row.querySelector('.product-select');
                    if (productSelect && productSelect.value) {
                        // Recalculate subtotals
                        calculateSubtotal(row.id);
                    }
                });

                // Make functions available globally
                window.removeItemRow = function(rowId) {
                    document.getElementById(rowId).remove();
                    calculateTotal();
                };

                window.loadProductSpecifications = function(rowId, productId, isExisting = false) {
                    const specContainer = document.getElementById(`${rowId}-specifications`);
                    specContainer.innerHTML = '';

                    if (!productId) return;

                    const product = products.find(p => p.id == productId);
                    if (!product || !product.spesifikasiProduk || product.spesifikasiProduk.length === 0) {
                        return;
                    }

                    let html = '<div class="mt-3"><h5>Spesifikasi Produk</h5><div class="row g-3">';

                    product.spesifikasiProduk.forEach((spec, index) => {
                        const specName = spec.spesifikasi ? spec.spesifikasi.nama_spesifikasi :
                            'Spesifikasi';
                        const inputType = spec.spesifikasi ? spec.spesifikasi.tipe_input : 'text';
                        const required = spec.wajib_diisi ? 'required' : '';

                        // Get the item index from rowId (different for existing vs new items)
                        let itemIndex;
                        if (isExisting) {
                            const match = rowId.match(/item-row-existing-(\d+)/);
                            itemIndex = match ? match[1] : 0;
                        } else {
                            const match = rowId.match(/item-row-new-(\d+)/);
                            itemIndex = match ? match[1] : 0;
                        }

                        html += `<div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label ${required ? 'required' : ''}">${specName}</label>`;

                        // Different input types
                        if (inputType === 'select' || inputType === 'radio') {
                            if (inputType === 'select') {
                                html += `<select class="form-select" name="items[${itemIndex}][specifications][${spec.id}][value]" ${required}>
                                    <option value="">Pilih ${specName}</option>`;

                                if (spec.pilihan && spec.pilihan.length > 0) {
                                    spec.pilihan.forEach(option => {
                                        html += `<option value="${option}">${option}</option>`;
                                    });
                                }

                                html += `</select>`;
                            } else { // radio buttons
                                if (spec.pilihan && spec.pilihan.length > 0) {
                                    spec.pilihan.forEach((option, optIndex) => {
                                        html += `
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="items[${itemIndex}][specifications][${spec.id}][value]"
                                                id="${rowId}-spec-${spec.id}-option-${optIndex}" value="${option}" ${optIndex === 0 && required ? 'checked' : ''}>
                                            <label class="form-check-label" for="${rowId}-spec-${spec.id}-option-${optIndex}">
                                                ${option}
                                            </label>
                                        </div>`;
                                    });
                                }
                            }

                            // Add bahan selection if associated with this specification
                            if (spec.bahanSpesifikasiProduk && spec.bahanSpesifikasiProduk.length > 0) {
                                html += `
                                <div class="mt-2">
                                    <label class="form-label">Bahan</label>
                                    <select class="form-select" name="items[${itemIndex}][specifications][${spec.id}][bahan_id]">
                                        <option value="">Pilih Bahan</option>`;

                                spec.bahanSpesifikasiProduk.forEach(bahan => {
                                    html +=
                                        `<option value="${bahan.id}">${bahan.nama_bahan} (${bahan.satuan})</option>`;
                                });

                                html += `</select>
                                </div>`;
                            }
                        } else if (inputType === 'number') {
                            html += `<input type="number" class="form-control" name="items[${itemIndex}][specifications][${spec.id}][value]"
                                placeholder="Masukkan ${specName}" ${required}>`;
                        } else { // text input default
                            html += `<input type="text" class="form-control" name="items[${itemIndex}][specifications][${spec.id}][value]"
                                placeholder="Masukkan ${specName}" ${required}>`;
                        }

                        html += `
                            <input type="hidden" name="items[${itemIndex}][specifications][${spec.id}][input_type]" value="${inputType}">
                            <input type="hidden" name="items[${itemIndex}][specifications][${spec.id}][price]" value="0">
                            </div>
                        </div>`;
                    });

                    html += '</div></div>';
                    specContainer.innerHTML = html;
                };

                window.calculateSubtotal = function(rowId) {
                    const row = document.getElementById(rowId);
                    const quantityInput = row.querySelector('.item-quantity');
                    const priceInput = row.querySelector('.item-price');
                    const subtotalInput = row.querySelector('.item-subtotal');

                    const quantity = parseFloat(quantityInput.value) || 0;
                    const price = parseFloat(priceInput.value) || 0;
                    const subtotal = quantity * price;

                    subtotalInput.value = subtotal.toLocaleString('id-ID');
                    calculateTotal();
                };

                window.calculateTotal = function() {
                    const subtotalInputs = document.querySelectorAll('.item-subtotal');
                    let total = 0;

                    subtotalInputs.forEach(input => {
                        // Remove thousand separators and convert to number
                        const value = parseFloat(input.value.replace(/\./g, '').replace(',', '.')) || 0;
                        total += value;
                    });

                    document.getElementById('total-display').innerText = 'Rp ' + total.toLocaleString('id-ID');
                    document.getElementById('total-input').value = total;
                };

                // Form validation
                const form = document.getElementById('transaction-form');
                form.addEventListener('submit', function(e) {
                    const itemRows = document.querySelectorAll('.item-row');

                    if (itemRows.length === 0) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Error!',
                            text: 'Transaksi harus memiliki minimal 1 item',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }

                    // Additional validation can be added here

                    return true;
                });

                // Calculate initial total
                calculateTotal();
            });
        </script>
    @endpush
@endsection
