@extends('layouts.layouts_dashboard')

@section('title', 'Shopping Cart')

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

    <div class="col-md-12 mt-3">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                {{-- Cart Details --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold text-black mb-0">Cart Details</h3>
                    <a href="{{ route('pos.index') }}" data-no-loading class="btn btn-outline-primary rounded-pill px-4">
                        <i class="fas fa-arrow-left me-2"></i>Back to Products
                    </a>
                </div>

                @if (empty($cartItems))
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h4>Your Cart is Empty</h4>
                        <p class="text-muted">Add some products to your cart to continue shopping</p>
                        <a href="{{ route('pos.index') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-shopping-bag me-2"></i>Browse Products
                        </a>
                    </div>
                @else
                    <div id="cartItems" class="space-y-4">
                        @foreach ($cartItems as $index => $item)
                            <div class="card shadow-sm border-0 rounded-4 mb-3">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="fw-bold text-dark mb-0">{{ $item['product_name'] }}</h5>
                                        <button class="btn btn-light btn-sm rounded-circle" type="button"
                                            onclick="removeItem({{ $index }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>

                                    <div class="d-flex justify-content-between border-bottom py-2">
                                        <span class="text-muted">Quantity</span>
                                        <span class="fw-medium">{{ $item['quantity'] }} pcs</span>
                                    </div>

                                    <!-- Specifications -->
                                    <div class="specifications-container">
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
                                                <span class="text-muted">
                                                    {{ $spec['nama_spesifikasi'] }}
                                                </span>
                                                <span class="fw-medium">
                                                    @if ($spec['input_type'] === 'select' && $bahan)
                                                        {{ $bahan->nama_bahan }}: {{ $item['quantity'] }} x Rp
                                                        {{ number_format($pricePerUnit, 0, ',', '.') }} = Rp
                                                        {{ number_format($spec['price'], 0, ',', '.') }}
                                                    @elseif ($bahan && $spesifikasiProduk && $spesifikasiProduk->spesifikasi)
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

                                        <!-- Production Time -->
                                        @php
                                            $product = \App\Models\Vendor\Produk::with('estimasiProduk.alat')->find(
                                                $item['product_id'],
                                            );
                                            $estimatedTime = $product
                                                ? $product->getEstimatedProductionTime($item['quantity'])
                                                : 0;
                                        @endphp
                                        <div class="d-flex justify-content-between border-bottom py-2">
                                            <span class="text-muted">Estimasi Waktu Produksi</span>
                                            <span class="fw-medium">{{ $estimatedTime }} menit</span>
                                        </div>

                                        <!-- Equipment Used -->
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

                                        <!-- Total Price -->
                                        <div class="d-flex justify-content-between pt-3">
                                            <span class="fw-bold">Total Item</span>
                                            <span class="fw-bold text-primary">
                                                Rp {{ number_format($item['total_price'], 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Cart Summary -->
                        <div class="mt-4 pt-4">
                            <!-- Order Summary -->
                            <div class="card shadow-sm border-0 rounded-4 mb-3">
                                <div class="card-body">
                                    <h5 class="fw-bold mb-3">Order Summary</h5>

                                    <!-- Total Items -->
                                    <div class="d-flex justify-content-between border-bottom py-2">
                                        <span class="text-muted">Total Items</span>
                                        <span class="fw-medium">{{ count($cartItems) }} items</span>
                                    </div>

                                    <!-- Total Quantity -->
                                    <div class="d-flex justify-content-between border-bottom py-2">
                                        <span class="text-muted">Total Quantity</span>
                                        <span class="fw-medium">
                                            {{ collect($cartItems)->sum('quantity') }} pcs
                                        </span>
                                    </div>

                                    <!-- Total Production Time -->
                                    <div class="d-flex justify-content-between border-bottom py-2">
                                        <span class="text-muted">Total Production Time</span>
                                        <span class="fw-medium">
                                            {{ collect($cartItems)->sum(function ($item) {
                                                $product = \App\Models\Vendor\Produk::with('estimasiProduk.alat')->find($item['product_id']);
                                                return $product ? $product->getEstimatedProductionTime($item['quantity']) : 0;
                                            }) }}
                                            minutes
                                        </span>
                                    </div>

                                    <!-- Total Price -->
                                    <div class="d-flex justify-content-between pt-3">
                                        <h4 class="fw-bold text-dark mb-0">Total</h4>
                                        <h4 class="fw-bold text-primary mb-0">
                                            Rp
                                            {{ number_format(collect($cartItems)->sum('total_price'), 0, ',', '.') }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <button class="btn btn-outline-danger rounded-pill px-4" type="button" onclick="clearCart()">
                                <i class="fas fa-trash me-2"></i>Clear Cart
                            </button>
                            <button class="btn btn-primary rounded-pill px-4" type="button" onclick="proceedToCheckout()">
                                <i class="fas fa-shopping-cart me-2"></i>Proceed to Checkout
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function removeItem(index) {
            Swal.fire({
                title: 'Remove Item?',
                text: "Are you sure you want to remove this item from your cart?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading('Removing item...');
                    window.location.href = `{{ route('pos.removeItem', '') }}/${index}`;
                }
            });
        }

        function clearCart() {
            Swal.fire({
                title: 'Clear Cart?',
                text: "Are you sure you want to clear your entire cart?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, clear it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading('Clearing cart...');
                    window.location.href = "{{ route('pos.clearCart') }}";
                }
            });
        }

        function proceedToCheckout() {
            showLoading('Proceeding to checkout...');
            window.location.href = "{{ route('pos.checkout') }}";
        }
    </script>
@endsection
