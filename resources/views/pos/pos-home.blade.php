@extends('layouts.layouts_dashboard')

@section('title', 'Point of Sale')

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

    {{-- navigation kategori --}}
    <div class="col-md-12 mt-3">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <div class="row align-items-center g-3">
                    {{-- category --}}
                    <div class="col-12 col-lg-5">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('pos.index') }}" data-no-loading
                                class="btn {{ request()->routeIs('pos.index') && !request()->has('search') ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-4 py-2">
                                <i class="fas fa-th-large me-2"></i>All Products
                            </a>
                            @foreach ($categories as $category)
                                @if ($category)
                                    <a href="{{ route('pos.category', ['slug' => $category->slug]) }}" data-no-loading
                                        class="btn {{ request()->is('*/pos/category/' . $category->slug) ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-4 py-2">
                                        <i class="fas fa-tag me-2"></i>{{ $category->nama_kategori }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- search --}}
                    <div class="col-12 col-lg-5">
                        <form action="{{ route('pos.search') }}" method="GET" class="d-flex gap-2" data-no-loading>
                            <div class="input-group input-group-merge shadow-sm rounded-pill">
                                <span class="input-group-text border-0 bg-transparent">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control form-control-lg border-0 ps-2"
                                    value="{{ request('search') }}" placeholder="Search products..." autocomplete="off"
                                    style="border-radius: 20px;">
                                @if (request('search'))
                                    <span class="input-group-text border-0 bg-transparent">
                                        <a href="{{ route('pos.index') }}" class="text-muted hover-danger" data-no-loading
                                            style="text-decoration: none">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                @endif
                            </div>
                        </form>
                    </div>

                    {{-- cart --}}
                    <div class="col-12 col-lg-2">
                        <a href="{{ route('pos.cart') }}" class="btn btn-primary rounded-pill px-4 py-2 w-100">
                            <i class="fas fa-shopping-cart me-2"></i>Cart
                            <span class="badge bg-light text-primary ms-2">{{ count(session('cart', [])) }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- produk --}}
    <div class="col-md-12 mt-3">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                @if ($products->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h4>No Products Found</h4>
                        <p class="text-muted">
                            {{ request('search') ? 'Try a different search term' : 'No products available in this category' }}
                        </p>
                        <a href="{{ route('pos.index') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-arrow-left me-2"></i>Back to All Products
                        </a>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach ($products as $product)
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="card h-100 border-0 shadow-hover rounded-4">
                                    <!-- Product image with fixed size -->
                                    <div class="product-image-wrapper"
                                        style="height: 180px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                        <img src="{{ asset($product->gambar[0] ?? 'images/no-image.jpg') }}"
                                            class="card-img-top"
                                            style="cursor: pointer; object-fit: cover; height: 100%; width: 100%;"
                                            data-bs-toggle="modal" data-bs-target="#productModal{{ $product->id }}">
                                    </div>

                                    <div class="card-body p-4">
                                        <h5 class="fw-bold mb-2">{{ $product->nama_produk }}</h5>
                                        <p class="text-muted mb-3">{!! Str::limit(strip_tags($product->deskripsi), 50) !!}</p>

                                        <button class="btn btn-outline-primary w-100 mb-3" data-bs-toggle="modal"
                                            data-bs-target="#productModal{{ $product->id }}">
                                            <i class="fas fa-shopping-cart me-2"></i>Order Now
                                        </button>
                                    </div>
                                </div>

                                <!-- Bootstrap Modal -->
                                <div class="modal fade" id="productModal{{ $product->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ $product->nama_produk }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('pos.addToCart') }}" method="POST" data-no-loading>
                                                @csrf
                                                <div class="modal-body">
                                                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                                                    <div class="mb-3">
                                                        <label class="form-label">Quantity</label>
                                                        <input type="number" name="quantity" class="form-control"
                                                            value="1" min="1">
                                                    </div>

                                                    @foreach ($product->spesifikasiProduk as $spec)
                                                        <div class="mb-3">
                                                            <label class="form-label">
                                                                {{ $spec->spesifikasi->nama_spesifikasi }}
                                                                @if ($spec->wajib_diisi)
                                                                    <span class="text-danger">*</span>
                                                                @endif
                                                            </label>

                                                            @if ($spec->spesifikasi->tipe_input === 'select')
                                                                <select name="specifications[{{ $spec->id }}]"
                                                                    class="form-select"
                                                                    {{ $spec->wajib_diisi ? 'required' : '' }}>
                                                                    @foreach ($spec->bahans as $bahan)
                                                                        <option value="{{ $bahan->id }}">
                                                                            {{ $bahan->nama_bahan }} -
                                                                            @if ($bahan->wholesalePrice->count() > 0)
                                                                                @foreach ($bahan->wholesalePrice as $price)
                                                                                    {{ $price->min_quantity }}-{{ $price->max_quantity }}
                                                                                    pcs: Rp
                                                                                    {{ number_format($price->harga) }}
                                                                                @endforeach
                                                                            @else
                                                                                Rp
                                                                                {{ number_format($bahan->hpp) }}
                                                                            @endif
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            @else
                                                                @if ($spec->spesifikasi->tipe_input === 'number')
                                                                    <div class="input-group">
                                                                        <input type="text"
                                                                            name="specifications[{{ $spec->id }}]"
                                                                            class="form-control decimal-input"
                                                                            {{ $spec->wajib_diisi ? 'required' : '' }}
                                                                            pattern="[0-9]*[.,]?[0-9]+"
                                                                            inputmode="decimal">
                                                                        @if ($spec->spesifikasi->satuan)
                                                                            <span
                                                                                class="input-group-text">{{ $spec->spesifikasi->satuan }}</span>
                                                                        @endif
                                                                    </div>
                                                                @else
                                                                    <div class="input-group">
                                                                        <input type="{{ $spec->spesifikasi->tipe_input }}"
                                                                            name="specifications[{{ $spec->id }}]"
                                                                            class="form-control"
                                                                            {{ $spec->wajib_diisi ? 'required' : '' }}>
                                                                        @if ($spec->spesifikasi->satuan)
                                                                            <span
                                                                                class="input-group-text">{{ $spec->spesifikasi->satuan }}</span>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    @endforeach

                                                    {{-- rincian harga produk --}}
                                                    <div class="mb-3">
                                                        <div id="priceDetails{{ $product->id }}" class="mt-3"></div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-info"
                                                        id="cekHarga{{ $product->id }}">
                                                        <i class="fas fa-calculator me-2"></i>Cek Harga
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tangani input desimal
        document.querySelectorAll('.decimal-input').forEach(input => {
            input.addEventListener('input', function(e) {
                // Ganti koma dengan titik untuk perhitungan
                this.value = this.value.replace(/,/g, '.');

                // Hanya izinkan angka dan satu titik desimal
                this.value = this.value.replace(/[^0-9.]/g, '');

                // Pastikan hanya ada satu titik desimal
                const parts = this.value.split('.');
                if (parts.length > 2) {
                    this.value = parts[0] + '.' + parts.slice(1).join('');
                }
            });
        });

        document.querySelectorAll('[id^="cekHarga"]').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('form');
                const productId = this.id.replace('cekHarga', '');
                const formData = new FormData(form);
                const data = {
                    product_id: formData.get('product_id'),
                    quantity: formData.get('quantity'),
                    specifications: {}
                };

                // Show loading state
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Calculating...';
                this.disabled = true;

                // Clear previous price details
                const priceDetails = document.querySelector(`#priceDetails${productId}`);
                priceDetails.innerHTML =
                    '<div class="alert alert-info">Calculating price...</div>';

                formData.forEach((value, key) => {
                    if (key.startsWith('specifications')) {
                        const matches = key.match(/\[(.*?)\]/);
                        if (matches) {
                            const specId = matches[1];
                            // Konversi ke float, bukan integer
                            const numValue = parseFloat(value);
                            data.specifications[specId] = isNaN(numValue) ? value :
                                numValue;
                        }
                    }
                });

                fetch(`{{ route('pos.checkPrice') }}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            priceDetails.innerHTML = `
                            <div class="alert alert-danger">
                                <p class="mb-0"><strong>Error:</strong> ${data.error}</p>
                            </div>
                        `;
                        } else {
                            priceDetails.innerHTML = `
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="mb-3 fw-bold"><i class="fas fa-receipt me-2"></i>Rincian Harga:</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Quantity:</span>
                                        <span class="fw-medium">${data.quantity}</span>
                                    </div>
                                    ${data.specifications.map(spec => `
                                                                                <div class="d-flex justify-content-between mb-2">
                                                                                    <span>${spec.name}:</span>
                                                                                    <span class="fw-medium">Rp ${spec.price}</span>
                                                                                </div>
                                                                            `).join('')}
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">Total Harga:</span>
                                        <span class="fw-bold text-primary">Rp ${data.totalPrice}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        priceDetails.innerHTML = `
                        <div class="alert alert-danger">
                            <p class="mb-0"><strong>Error:</strong> Failed to calculate price. Please try again.</p>
                        </div>
                    `;
                    })
                    .finally(() => {
                        // Reset button state
                        this.innerHTML = '<i class="fas fa-calculator me-2"></i>Cek Harga';
                        this.disabled = false;
                    });
            });
        });

        // Form validation before submission
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const requiredFields = this.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please fill in all required fields',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
        });
    });
</script>@endsection
