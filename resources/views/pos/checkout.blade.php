@extends('layouts.layouts_dashboard')

@section('title', 'Checkout')

@section('content')
    {{-- Add CSRF token meta tag --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- header --}}
    <div class="col-md-12 mt-4">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-0 py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0 fw-bold text-primary">Point of Sale</h2>
                        <p class="text-muted mb-0 mt-2"><i class="fas fa-calendar-alt me-2"></i>{{ date('Y-m-d') }}</p>
                    </div>
                    {{-- <div class="text-end">
                        <h5 class="mb-2 text-dark"><i class="fas fa-user-circle me-2"></i>{{ Auth::user()->name }}</h5>
                        <p class="mb-0 text-muted"><i
                                class="fas fa-store-alt me-2"></i>{{ Auth::user()->vendorUser->first()->name ?? 'Vendor' }}
                        </p>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 mt-4">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <form id="checkoutForm" action="{{ route('pos.checkout.process') }}" method="POST" data-no-loading>
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="mb-4">Customer Information</h4>
                            <div class="mb-3">
                                <label class="form-label">Customer</label>
                                <div class="d-flex gap-2">
                                    <select name="pelanggan_id" class="form-select" required id="customerSelect">
                                        <option value="">Select Customer</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->nama }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#newCustomerModal">
                                        <i class="fas fa-plus"></i> Add Customer
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <select name="payment_method" class="form-select" required id="paymentMethodSelect">
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Bank Transfer</option>
                                    <option value="qris">QRIS</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="debit_card">Debit Card</option>
                                    <option value="e_wallet">E-Wallet</option>
                                </select>
                            </div>

                            <!-- Tambahkan input jumlah pembayaran (akan muncul jika metode pembayaran cash) -->
                            <div class="mb-3" id="paymentAmountContainer" style="display: none;">
                                <label class="form-label">Jumlah Pembayaran</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="payment_amount" id="paymentAmount" class="form-control" placeholder="Masukkan jumlah pembayaran">
                                </div>
                                <div class="mt-2">
                                    <span class="text-muted">Total: Rp <span id="totalAmountDisplay">{{ number_format($totalAmount, 0, ',', '.') }}</span></span>
                                </div>
                                <div class="mt-2" id="changeContainer" style="display: none;">
                                    <span class="fw-bold">Kembalian: Rp <span id="changeAmount">0</span></span>
                                </div>
                            </div>

                            <!-- Tambahkan tombol shortcut pembayaran -->
                            <div class="mb-3" id="paymentShortcutsContainer" style="display: none;">
                                <label class="form-label">Shortcut Pembayaran</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-outline-primary payment-shortcut" data-amount="{{ $totalAmount }}">Uang Pas</button>
                                    <button type="button" class="btn btn-outline-primary payment-shortcut" data-amount="{{ $totalAmount + 10000 }}">+10rb</button>
                                    <button type="button" class="btn btn-outline-primary payment-shortcut" data-amount="{{ $totalAmount + 50000 }}">+50rb</button>
                                    <button type="button" class="btn btn-outline-primary payment-shortcut" data-amount="100000">100rb</button>
                                    <button type="button" class="btn btn-outline-primary payment-shortcut" data-amount="200000">200rb</button>
                                    <button type="button" class="btn btn-outline-primary payment-shortcut" data-amount="500000">500rb</button>
                                </div>
                            </div>

                            <!-- Tambahkan script untuk menangani tampilan input pembayaran -->
                            <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const paymentMethodSelect = document.getElementById('paymentMethodSelect');
                                const paymentAmountContainer = document.getElementById('paymentAmountContainer');
                                const paymentShortcutsContainer = document.getElementById('paymentShortcutsContainer');
                                const paymentAmount = document.getElementById('paymentAmount');
                                const changeContainer = document.getElementById('changeContainer');
                                const changeAmount = document.getElementById('changeAmount');
                                const totalAmount = {{ $totalAmount }};

                                // Fungsi untuk menghitung kembalian
                                function calculateChange() {
                                    const amount = parseFloat(paymentAmount.value) || 0;
                                    const change = amount - totalAmount;

                                    if (change >= 0) {
                                        changeAmount.textContent = change.toLocaleString('id-ID');
                                        changeContainer.style.display = 'block';
                                    } else {
                                        changeContainer.style.display = 'none';
                                    }
                                }

                                // Event listener untuk perubahan metode pembayaran
                                paymentMethodSelect.addEventListener('change', function() {
                                    if (this.value === 'cash') {
                                        paymentAmountContainer.style.display = 'block';
                                        paymentShortcutsContainer.style.display = 'block';
                                    } else {
                                        paymentAmountContainer.style.display = 'none';
                                        paymentShortcutsContainer.style.display = 'none';
                                    }
                                });

                                // Event listener untuk input jumlah pembayaran
                                paymentAmount.addEventListener('input', calculateChange);

                                // Event listener untuk tombol shortcut pembayaran
                                document.querySelectorAll('.payment-shortcut').forEach(button => {
                                    button.addEventListener('click', function() {
                                        paymentAmount.value = this.dataset.amount;
                                        calculateChange();
                                    });
                                });

                                // Trigger change event untuk menampilkan/menyembunyikan input pembayaran sesuai metode awal
                                paymentMethodSelect.dispatchEvent(new Event('change'));
                            });
                            </script>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="catatan" class="form-control" rows="3"
                                    placeholder="Add any special instructions or notes here..."></textarea>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <h4 class="mb-4">Order Summary</h4>
                            @foreach ($cartItems as $index => $item)
                                <div class="card shadow-sm border-0 rounded-4 mb-3">
                                    <div class="card-body">
                                        <h5 class="fw-bold mb-3">{{ $item['product_name'] }} (x{{ $item['quantity'] }})</h5>

                                        <!-- Specifications -->
                                        @foreach ($item['specifications'] as $specId => $spec)
                                            @php
                                                $spesifikasiProduk = \App\Models\Vendor\SpesifikasiProduk::with([
                                                    'spesifikasi',
                                                    'bahans',
                                                ])->find($specId);
                                                $bahan = \App\Models\Vendor\Bahan::with('wholesalePrice')->find(
                                                    $spec['bahan_id'],
                                                );
                                                $wholesalePrice = new \App\Models\Vendor\WholesalePrice();

                                                // Calculate price based on input type
                                                if ($spec['input_type'] === 'select' && $bahan) {
                                                    $pricePerUnit = $wholesalePrice->calculateFinalPrice(
                                                        $bahan->hpp,
                                                        $item['quantity'],
                                                        $bahan->id,
                                                    );
                                                } elseif ($bahan) {
                                                    $pricePerUnit = $wholesalePrice->calculateFinalPrice(
                                                        $bahan->hpp,
                                                        $spec['value'],
                                                        $bahan->id,
                                                    );
                                                } else {
                                                    $pricePerUnit = 0;
                                                }
                                            @endphp
                                            <div class="d-flex justify-content-between border-bottom py-2">
                                                <span class="text-muted">{{ $spec['nama_spesifikasi'] }}</span>
                                                <span class="fw-medium">
                                                    @if ($spec['input_type'] === 'select' && $bahan)
                                                        {{ $bahan->nama_bahan }}: {{ $item['quantity'] }} x Rp
                                                        {{ number_format($pricePerUnit, 0, ',', '.') }} = Rp
                                                        {{ number_format($spec['price'], 0, ',', '.') }}
                                                    @elseif ($spesifikasiProduk && $spesifikasiProduk->spesifikasi && $bahan)
                                                        {{ number_format($spec['value'], 2, ',', '.') }}
                                                        {{ $spesifikasiProduk->spesifikasi->satuan }}
                                                        x Rp {{ number_format($pricePerUnit, 0, ',', '.') }} = Rp
                                                        {{ number_format($spec['price'], 0, ',', '.') }}
                                                    @else
                                                        Rp {{ number_format($spec['price'], 0, ',', '.') }}
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach

                                        <!-- Production Details -->
                                        @php
                                            $product = \App\Models\Vendor\Produk::with('estimasiProduk.alat')->find(
                                                $item['product_id'],
                                            );
                                            $estimatedTime = $product
                                                ? $product->getEstimatedProductionTime($item['quantity'])
                                                : 0;
                                        @endphp
                                        <div class="d-flex justify-content-between border-bottom py-2">
                                            <span class="text-muted">Estimasi Waktu</span>
                                            <span class="fw-medium">{{ $estimatedTime }} menit</span>
                                        </div>

                                        <div class="d-flex justify-content-between border-bottom py-2">
                                            <span class="text-muted">Alat Produksi</span>
                                            <span class="fw-medium">
                                                @if ($product && $product->estimasiProduk)
                                                    {{ $product->estimasiProduk->pluck('alat.nama_alat')->filter()->implode(', ') ?: 'Tidak ada alat' }}
                                                @else
                                                    Tidak ada alat
                                                @endif
                                            </span>
                                        </div>

                                        <!-- Item Total -->
                                        <div class="d-flex justify-content-between pt-3">
                                            <span class="fw-bold">Subtotal</span>
                                            <span class="fw-bold text-primary">
                                                Rp {{ number_format($item['total_price'], 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden inputs for form submission -->
                                <input type="hidden" name="items[{{ $index }}][product_id]"
                                    value="{{ $item['product_id'] }}">
                                <input type="hidden" name="items[{{ $index }}][quantity]"
                                    value="{{ $item['quantity'] }}">
                                @foreach ($item['specifications'] as $specId => $spec)
                                    <input type="hidden"
                                        name="items[{{ $index }}][specifications][{{ $specId }}][bahan_id]"
                                        value="{{ $spec['bahan_id'] }}">
                                    <input type="hidden"
                                        name="items[{{ $index }}][specifications][{{ $specId }}][value]"
                                        value="{{ $spec['value'] }}">
                                    <input type="hidden"
                                        name="items[{{ $index }}][specifications][{{ $specId }}][input_type]"
                                        value="{{ $spec['input_type'] }}">
                                    <input type="hidden"
                                        name="items[{{ $index }}][specifications][{{ $specId }}][price]"
                                        value="{{ $spec['price'] }}">
                                @endforeach
                            @endforeach

                            <!-- Order Totals -->
                            <div class="card shadow-sm border-0 rounded-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between border-bottom py-2">
                                        <span class="text-muted">Total Items</span>
                                        <span class="fw-medium">{{ count($cartItems) }} items</span>
                                    </div>
                                    <div class="d-flex justify-content-between border-bottom py-2">
                                        <span class="text-muted">Total Quantity</span>
                                        <span class="fw-medium">{{ collect($cartItems)->sum('quantity') }} pcs</span>
                                    </div>
                                    <div class="d-flex justify-content-between border-bottom py-2">
                                        <span class="text-muted">Total Production Time</span>
                                        <span class="fw-medium">{{ $totalTime }} minutes</span>
                                    </div>
                                    <div class="d-flex justify-content-between pt-3">
                                        <h4 class="fw-bold mb-0">Grand Total</h4>
                                        <h4 class="fw-bold text-primary mb-0">
                                            Rp {{ number_format($totalAmount, 0, ',', '.') }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('pos.cart') }}" class="btn btn-outline-primary rounded-pill px-4"
                                data-no-loading>
                                <i class="fas fa-arrow-left me-2"></i>Back to Cart
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="fas fa-check me-2"></i>Complete Order
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- New Customer Modal -->
    <div class="modal fade" id="newCustomerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('pos.customer.create') }}" method="POST" id="newCustomerForm" data-no-loading>
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No Telp</label>
                            <input type="tel" name="no_telp" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle new customer form submission
            const newCustomerForm = document.getElementById('newCustomerForm');
            if (newCustomerForm) {
                newCustomerForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const submitButton = this.querySelector('button[type="submit"]');
                    const originalButtonText = submitButton.innerHTML;

                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
                    submitButton.disabled = true;

                    try {
                        const response = await fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Add new customer to select dropdown
                            const customerSelect = document.getElementById('customerSelect');
                            const option = document.createElement('option');
                            option.value = data.customer.id;
                            option.text = data.customer.nama;
                            option.selected = true;
                            customerSelect.appendChild(option);

                            // Close modal and show success message
                            const modal = bootstrap.Modal.getInstance(document.getElementById(
                                'newCustomerModal'));
                            modal.hide();

                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Customer added successfully',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                        } else {
                            throw new Error(data.message || 'Failed to add customer');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: error.message || 'Failed to add customer. Please try again.',
                            confirmButtonColor: '#3085d6'
                        });
                    } finally {
                        submitButton.innerHTML = originalButtonText;
                        submitButton.disabled = false;
                    }
                });
            }

            // Handle checkout form submission
            const checkoutForm = document.getElementById('checkoutForm');
            if (checkoutForm) {
                checkoutForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    // Validate customer selection
                    const customerSelect = document.getElementById('customerSelect');
                    if (!customerSelect.value) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'Please select a customer before proceeding',
                            confirmButtonColor: '#3085d6'
                        });
                        customerSelect.focus();
                        return;
                    }

                    // Show loading state
                    const submitButton = this.querySelector('button[type="submit"]');
                    const originalButtonText = submitButton.innerHTML;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
                    submitButton.disabled = true;

                    try {
                        const formData = new FormData(this);

                        const response = await fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Order Completed!',
                                text: 'Your order has been processed successfully.',
                                confirmButtonColor: '#3085d6'
                            }).then(() => {
                                // Open web view in new tab
                                window.open(data.invoiceUrl, '_blank');

                                // Download PDF version
                                fetch(data.downloadUrl)
                                    .then(response => response.blob())
                                    .then(blob => {
                                        const url = window.URL.createObjectURL(blob);
                                        const a = document.createElement('a');
                                        a.href = url;
                                        a.download = "invoice.pdf";
                                        document.body.appendChild(a);
                                        a.click();
                                        window.URL.revokeObjectURL(url);
                                    });

                                // Redirect main window
                                window.location.href = data.redirectUrl;
                            });
                        } else {
                            throw new Error(data.message ||
                                'An error occurred during checkout. Please try again.');
                        }
                    } catch (error) {
                        console.error('Checkout error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Checkout Failed',
                            text: error.message ||
                                'An unexpected error occurred. Please try again later.',
                            confirmButtonColor: '#3085d6'
                        });
                    } finally {
                        // Reset button state
                        submitButton.innerHTML = originalButtonText;
                        submitButton.disabled = false;
                    }
                });
            }
        });
    </script>
@endsection
