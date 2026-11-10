@extends('layouts.layouts_dashboard')

@section('title', 'Tambah Transaksi')
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

                <form action="{{ route('transaksi.store') }}" method="POST" class="card" id="transaction-form"
                    onsubmit="return validateForm()">
                    @csrf
                    <div class="card-header">
                        <h3 class="card-title">Tambah Transaksi Baru</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Customer Section -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label required">Pelanggan</label>
                                    <div class="input-group">
                                        <select class="form-select @error('pelanggan_id') is-invalid @enderror"
                                            name="pelanggan_id" id="pelanggan-select" required>
                                            <option value="">Pilih Pelanggan</option>
                                            @foreach ($pelanggans as $pelanggan)
                                                <option value="{{ $pelanggan->id }}"
                                                    {{ old('pelanggan_id') == $pelanggan->id ? 'selected' : '' }}>
                                                    {{ $pelanggan->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <a href="{{ route('pelanggan.create') }}" class="btn btn-outline-secondary"
                                            target="_blank" data-bs-toggle="tooltip" title="Tambah Pelanggan Baru">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M12 5l0 14"></path>
                                                <path d="M5 12l14 0"></path>
                                            </svg>
                                        </a>
                                    </div>
                                    @error('pelanggan_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Payment Method Section -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label required">Metode Pembayaran</label>
                                    <select class="form-select @error('payment_method') is-invalid @enderror"
                                        name="payment_method" required>
                                        <option value="">Pilih Metode Pembayaran</option>
                                        @foreach ($paymentMethods as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('payment_method') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payment_method')
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
                                        value="{{ old('estimasi_selesai', date('Y-m-d', strtotime('+3 days'))) }}"
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
                                        placeholder="Masukkan catatan transaksi">{{ old('catatan') }}</textarea>
                                    @error('catatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Transaction Items Section -->
                        <div class="mt-4">
                            <h4>Item Transaksi</h4>
                            <p class="text-muted">Tambahkan produk yang dibeli dalam transaksi ini</p>

                            <div id="items-container">
                                <!-- Dynamic rows will be added here -->
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
                                    Tambah Item
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
                                                <td class="text-end" id="total-display">Rp 0</td>
                                                <input type="hidden" name="total_harga" id="total-input" value="0">
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
                            Simpan
                        </button>

                        <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">
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
                const itemsContainer = document.getElementById('items-container');
                const addItemButton = document.getElementById('add-item-row');
                let itemRowCount = 0;

                // Products data for dropdown and specifications
                const products = @json($produks);

                // Add item row
                addItemButton.addEventListener('click', function() {
                    addItemRow();
                });

                function addItemRow() {
                    const rowId = `item-row-${itemRowCount}`;

                    let productOptions = '<option value="">Pilih Produk</option>';
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

                    itemsContainer.insertAdjacentHTML('beforeend', html);
                    itemRowCount++;
                    calculateTotal();
                }

                // Add at least one item row by default
                addItemRow();

                // Make functions available globally
                window.removeItemRow = function(rowId) {
                    document.getElementById(rowId).remove();
                    calculateTotal();
                };

                window.loadProductSpecifications = function(rowId, productId) {
                    const specContainer = document.getElementById(`${rowId}-specifications`);
                    specContainer.innerHTML = '';

                    if (!productId) return;

                    const selectEl = document.querySelector(`#${rowId} .product-select`);
                    const selectedOption = selectEl.options[selectEl.selectedIndex];

                    if (!selectedOption) return;

                    let product;
                    try {
                        product = JSON.parse(selectedOption.dataset.product);
                    } catch (e) {
                        console.error("Error parsing product data:", e);
                        return;
                    }

                    if (!product || !product.spesifikasiProduk || product.spesifikasiProduk.length === 0) {
                        return;
                    }

                    let html = '<div class="mt-3"><h5>Spesifikasi Produk</h5><div class="row g-3">';

                    product.spesifikasiProduk.forEach((spec, index) => {
                        if (!spec.spesifikasi) return;

                        const specName = spec.spesifikasi.nama_spesifikasi || 'Spesifikasi';
                        const inputType = spec.spesifikasi.tipe_input || 'text';
                        const required = spec.wajib_diisi ? 'required' : '';
                        const itemIndex = rowId.split('-')[2]; // Extract item index from rowId

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
                window.validateForm = function() {
                    const itemRows = document.querySelectorAll('.item-row');

                    if (itemRows.length === 0) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Transaksi harus memiliki minimal 1 item',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }

                    // Validate required specifications
                    let isValid = true;

                    itemRows.forEach(row => {
                        const requiredInputs = row.querySelectorAll('input[required], select[required]');
                        requiredInputs.forEach(input => {
                            if (!input.value) {
                                input.classList.add('is-invalid');
                                isValid = false;
                            } else {
                                input.classList.remove('is-invalid');
                            }
                        });
                    });

                    if (!isValid) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Mohon lengkapi semua field yang wajib diisi',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }

                    // Show loading
                    Swal.fire({
                        title: 'Sedang Memproses',
                        text: 'Mohon tunggu...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    return true;
                };
            });
        </script>
    @endpush
@endsection
