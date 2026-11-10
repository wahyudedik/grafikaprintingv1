@extends('layouts.layouts_dashboard')

@section('title', 'Edit Produk')
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

                <form action="{{ route('produk.update', $produk->id) }}" method="POST" class="card"
                    enctype="multipart/form-data" onsubmit="showLoading('Memperbarui produk...')">
                    @csrf
                    @method('PUT')
                    <div class="card-header">
                        <h3 class="card-title">Edit Produk</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Nama Produk</label>
                                    <input type="text" class="form-control @error('nama_produk') is-invalid @enderror"
                                        name="nama_produk" value="{{ old('nama_produk', $produk->nama_produk) }}"
                                        placeholder="Masukkan nama produk">
                                    @error('nama_produk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required">Kategori</label>
                                    <div class="input-group">
                                        <select class="form-select @error('kategori_id') is-invalid @enderror"
                                            name="kategori_id" id="kategori-select">
                                            <option value="">Pilih Kategori</option>
                                            @foreach ($kategories as $kategori)
                                                <option value="{{ $kategori->id }}"
                                                    {{ old('kategori_id', $produk->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                                    {{ $kategori->nama_kategori }}
                                                </option>
                                            @endforeach
                                            <option value="new">+ Tambah Kategori Baru</option>
                                        </select>
                                        <button type="button" class="btn btn-outline-secondary" id="toggle-new-category">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M12 5l0 14" />
                                                <path d="M5 12l14 0" />
                                            </svg>
                                        </button>
                                    </div>
                                    @error('kategori_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- New category input (initially hidden) -->
                                <div class="mb-3" id="new-category-container" style="display: none;">
                                    <label class="form-label">Nama Kategori Baru</label>
                                    <input type="text" class="form-control @error('new_kategori') is-invalid @enderror"
                                        name="new_kategori" value="{{ old('new_kategori') }}"
                                        placeholder="Masukkan nama kategori baru">
                                    @error('new_kategori')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" name="deskripsi" rows="5"
                                        placeholder="Masukkan deskripsi produk">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                                    @error('deskripsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Product Images Section -->
                        <div class="mt-4">
                            <h4>Gambar Produk</h4>
                            <div class="mb-3">
                                <label class="form-label">Tambah Gambar Baru</label>
                                <input type="file" class="form-control @error('gambar.*') is-invalid @enderror"
                                    name="gambar[]" multiple accept="image/*">
                                <div class="form-text">
                                    Anda dapat memilih beberapa gambar. Format yang didukung: JPG, PNG, GIF. Maks 2MB per
                                    file.
                                </div>
                                @error('gambar.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if (!empty($produk->gambar))
                                <div class="mb-3">
                                    <label class="form-label">Gambar Saat Ini</label>
                                    <div class="row g-2">
                                        @foreach ($produk->gambar as $index => $image)
                                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                                <div class="card card-sm">
                                                    <img src="{{ asset($image) }}" class="card-img-top" alt="Product Image"
                                                        style="height: 100px; object-fit: cover;">
                                                    <div class="card-body p-2 text-center">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input"
                                                                name="delete_image[]" value="{{ $index }}">
                                                            <label class="form-check-label">Hapus</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Specifications Section -->
                        <div class="mt-4">
                            <h4>Spesifikasi Produk</h4>
                            <p class="text-muted">Kelola spesifikasi yang dapat dipilih pelanggan saat memesan produk ini
                            </p>

                            <div id="specifications-container">
                                <!-- Existing specifications -->
                                @foreach ($produk->spesifikasiProduk as $index => $spec)
                                    <div class="row g-3 mb-3 spec-row" id="spec-row-existing-{{ $spec->id }}">
                                        <input type="hidden" name="spesifikasi[{{ $index }}][id]"
                                            value="{{ $spec->id }}">
                                        <div class="col-md-3">
                                            <label class="form-label">Jenis Spesifikasi</label>
                                            <select class="form-select spec-select"
                                                name="spesifikasi[{{ $index }}][spesifikasi_id]" required
                                                onchange="updateSpecOptions('spec-row-existing-{{ $spec->id }}')">
                                                <option value="">Pilih Spesifikasi</option>
                                                @foreach ($spesifikasis as $spesifikasi)
                                                    <option value="{{ $spesifikasi->id }}"
                                                        {{ $spec->spesifikasi_id == $spesifikasi->id ? 'selected' : '' }}>
                                                        {{ $spesifikasi->nama_spesifikasi }}
                                                        ({{ $spesifikasi->tipe_input }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Wajib Diisi</label>
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox"
                                                    name="spesifikasi[{{ $index }}][wajib_diisi]" value="1"
                                                    {{ $spec->wajib_diisi ? 'checked' : '' }}>
                                                <label class="form-check-label">Ya</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Pilihan (untuk dropdown/radio)</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control"
                                                    id="spec-row-existing-{{ $spec->id }}-option-input"
                                                    placeholder="Tambahkan pilihan">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    onclick="addOption('spec-row-existing-{{ $spec->id }}')">+</button>
                                            </div>
                                            <small class="form-hint">Tekan + untuk menambahkan setiap pilihan</small>
                                            <div class="mt-2"
                                                id="spec-row-existing-{{ $spec->id }}-options-container">
                                                <!-- Existing options -->
                                                @if (!empty($spec->pilihan))
                                                    @foreach ($spec->pilihan as $pilihan)
                                                        <div class="d-flex align-items-center mt-1 option-item">
                                                            <input type="hidden"
                                                                name="spesifikasi[{{ $index }}][pilihan][]"
                                                                value="{{ $pilihan }}">
                                                            <span class="badge bg-primary me-2">{{ $pilihan }}</span>
                                                            <button type="button" class="btn btn-sm btn-ghost-danger"
                                                                onclick="this.parentElement.remove()">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    class="icon icon-tabler icon-tabler-x" width="24"
                                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                                    stroke="currentColor" fill="none"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                        fill="none" />
                                                                    <path d="M18 6l-12 12" />
                                                                    <path d="M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Bahan yang Digunakan</label>
                                            <select class="form-select"
                                                name="spesifikasi[{{ $index }}][bahan_ids][]" multiple>
                                                @foreach ($bahans as $bahan)
                                                    <option value="{{ $bahan->id }}"
                                                        {{ $spec->bahanSpesifikasiProduk->contains($bahan->id) ? 'selected' : '' }}>
                                                        {{ $bahan->nama_bahan }} ({{ $bahan->satuan }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-hint">Tahan Ctrl untuk memilih beberapa bahan</small>
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-outline-danger remove-existing-spec"
                                                data-id="{{ $spec->id }}"
                                                data-row="spec-row-existing-{{ $spec->id }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M18 6l-12 12" />
                                                    <path d="M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div id="new-specs-container">
                                <!-- New specifications will be added here -->
                            </div>

                            <input type="hidden" name="deleted_spec_ids" id="deleted-spec-ids">

                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-primary" id="add-specification-row">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 5l0 14" />
                                        <path d="M5 12l14 0" />
                                    </svg>
                                    Tambah Spesifikasi Baru
                                </button>
                            </div>
                        </div>

                        <!-- Production Estimates Section -->
                        <div class="mt-4">
                            <h4>Estimasi Produksi</h4>
                            <p class="text-muted">Kelola estimasi waktu produksi untuk setiap alat yang digunakan</p>

                            <div id="estimates-container">
                                <!-- Existing estimates -->
                                @foreach ($produk->estimasiProduk as $index => $estimasi)
                                    <div class="row g-3 mb-3 estimate-row"
                                        id="estimate-row-existing-{{ $estimasi->id }}">
                                        <input type="hidden" name="estimasi[{{ $index }}][id]"
                                            value="{{ $estimasi->id }}">
                                        <div class="col-md-4">
                                            <label class="form-label">Alat Produksi</label>
                                            <select class="form-select" name="estimasi[{{ $index }}][alat_id]"
                                                required>
                                                <option value="">Pilih Alat</option>
                                                @foreach ($alats as $alat)
                                                    <option value="{{ $alat->id }}"
                                                        {{ $estimasi->alat_id == $alat->id ? 'selected' : '' }}>
                                                        {{ $alat->nama_alat }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Waktu Persiapan (menit)</label>
                                            <input type="number" class="form-control"
                                                name="estimasi[{{ $index }}][waktu_persiapan]" step="0.01"
                                                min="0" required placeholder="Waktu setup"
                                                value="{{ $estimasi->waktu_persiapan }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Waktu Per Unit (menit)</label>
                                            <input type="number" class="form-control"
                                                name="estimasi[{{ $index }}][waktu_produksi_per_unit]"
                                                step="0.01" min="0" required
                                                placeholder="Waktu produksi per unit"
                                                value="{{ $estimasi->waktu_produksi_per_unit }}">
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-outline-danger remove-existing-estimate"
                                                data-id="{{ $estimasi->id }}"
                                                data-row="estimate-row-existing-{{ $estimasi->id }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M18 6l-12 12" />
                                                    <path d="M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div id="new-estimates-container">
                                <!-- New estimates will be added here -->
                            </div>

                            <input type="hidden" name="deleted_estimate_ids" id="deleted-estimate-ids">

                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-primary" id="add-estimate-row">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 5l0 14" />
                                        <path d="M5 12l14 0" />
                                    </svg>
                                    Tambah Estimasi Baru
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

                        <a href="{{ route('produk.index') }}" class="btn btn-secondary">
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
                const newSpecsContainer = document.getElementById('new-specs-container');
                const addSpecButton = document.getElementById('add-specification-row');
                const newEstimatesContainer = document.getElementById('new-estimates-container');
                const addEstimateButton = document.getElementById('add-estimate-row');
                const deletedSpecIds = document.getElementById('deleted-spec-ids');
                const deletedEstimateIds = document.getElementById('deleted-estimate-ids');
                const kategoriSelect = document.getElementById('kategori-select');
                const newCategoryContainer = document.getElementById('new-category-container');
                const toggleNewCategoryBtn = document.getElementById('toggle-new-category');
                const newKategoriInput = document.querySelector('input[name="new_kategori"]');

                let specRowCount = 0;
                let estimateRowCount = 0;
                let deletedSpecs = [];
                let deletedEstimates = [];

                // Category handling
                kategoriSelect.addEventListener('change', function() {
                    if (this.value === 'new') {
                        newCategoryContainer.style.display = 'block';
                        newKategoriInput.setAttribute('required', 'required');
                    } else {
                        newCategoryContainer.style.display = 'none';
                        newKategoriInput.removeAttribute('required');
                    }
                });

                toggleNewCategoryBtn.addEventListener('click', function() {
                    if (newCategoryContainer.style.display === 'none') {
                        newCategoryContainer.style.display = 'block';
                        kategoriSelect.value = 'new';
                        newKategoriInput.setAttribute('required', 'required');
                    } else {
                        newCategoryContainer.style.display = 'none';
                        kategoriSelect.value = '';
                        newKategoriInput.removeAttribute('required');
                    }
                });

                // Initialize functionality for existing specs and estimates
                document.querySelectorAll('.remove-existing-spec').forEach(button => {
                    button.addEventListener('click', function() {
                        const rowId = this.getAttribute('data-row');
                        const specId = this.getAttribute('data-id');

                        // Add ID to deleted list
                        deletedSpecs.push(specId);
                        deletedSpecIds.value = deletedSpecs.join(',');

                        // Remove the row
                        document.getElementById(rowId).remove();
                    });
                });

                document.querySelectorAll('.remove-existing-estimate').forEach(button => {
                    button.addEventListener('click', function() {
                        const rowId = this.getAttribute('data-row');
                        const estimateId = this.getAttribute('data-id');

                        // Add ID to deleted list
                        deletedEstimates.push(estimateId);
                        deletedEstimateIds.value = deletedEstimates.join(',');

                        // Remove the row
                        document.getElementById(rowId).remove();
                    });
                });

                // Add new specification row
                addSpecButton.addEventListener('click', function() {
                    addSpecificationRow();
                });

                // Add new estimate row
                addEstimateButton.addEventListener('click', function() {
                    addEstimateRow();
                });

                function addSpecificationRow() {
                    const rowId = `new-spec-row-${specRowCount}`;
                    const spesifikasis = @json($spesifikasis);
                    const bahans = @json($bahans);

                    let spesifikasiOptions = '';
                    spesifikasis.forEach(spec => {
                        spesifikasiOptions +=
                            `<option value="${spec.id}">${spec.nama_spesifikasi} (${spec.tipe_input})</option>`;
                    });

                    let bahanOptions = '';
                    bahans.forEach(bahan => {
                        bahanOptions +=
                            `<option value="${bahan.id}">${bahan.nama_bahan} (${bahan.satuan})</option>`;
                    });

                    const html = `
                            <div class="row g-3 mb-3 spec-row" id="${rowId}">
                                <div class="col-md-3">
                                    <label class="form-label">Jenis Spesifikasi</label>
                                    <select class="form-select spec-select" name="new_spesifikasi[${specRowCount}][spesifikasi_id]" required onchange="updateSpecOptions('${rowId}')">
                                        <option value="">Pilih Spesifikasi</option>
                                        ${spesifikasiOptions}
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Wajib Diisi</label>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="new_spesifikasi[${specRowCount}][wajib_diisi]" value="1">
                                        <label class="form-check-label">Ya</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Pilihan (untuk dropdown/radio)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="${rowId}-option-input" placeholder="Tambahkan pilihan">
                                        <button type="button" class="btn btn-outline-secondary" onclick="addOption('${rowId}')">+</button>
                                    </div>
                                    <small class="form-hint">Tekan + untuk menambahkan setiap pilihan</small>
                                    <div class="mt-2" id="${rowId}-options-container">
                                        <!-- Options will be added here -->
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Bahan yang Digunakan</label>
                                    <select class="form-select" name="new_spesifikasi[${specRowCount}][bahan_ids][]" multiple>
                                        ${bahanOptions}
                                    </select>
                                    <small class="form-hint">Tahan Ctrl untuk memilih beberapa bahan</small>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger" onclick="removeSpecRow('${rowId}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M18 6l-12 12" />
                                            <path d="M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            `;

                    newSpecsContainer.insertAdjacentHTML('beforeend', html);
                    specRowCount++;
                }

                function addEstimateRow() {
                    const rowId = `new-estimate-row-${estimateRowCount}`;
                    const alats = @json($alats);

                    let alatOptions = '';
                    alats.forEach(alat => {
                        alatOptions += `<option value="${alat.id}">${alat.nama_alat}</option>`;
                    });

                    const html = `
                            <div class="row g-3 mb-3 estimate-row" id="${rowId}">
                                <div class="col-md-4">
                                    <label class="form-label">Alat Produksi</label>
                                    <select class="form-select" name="new_estimasi[${estimateRowCount}][alat_id]" required>
                                        <option value="">Pilih Alat</option>
                                        ${alatOptions}
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Waktu Persiapan (menit)</label>
                                    <input type="number" class="form-control" name="new_estimasi[${estimateRowCount}][waktu_persiapan]" 
                                        step="0.01" min="0" required placeholder="Waktu setup">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Waktu Per Unit (menit)</label>
                                    <input type="number" class="form-control" name="new_estimasi[${estimateRowCount}][waktu_produksi_per_unit]" 
                                        step="0.01" min="0" required placeholder="Waktu produksi per unit">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger" onclick="removeEstimateRow('${rowId}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M18 6l-12 12" />
                                            <path d="M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            `;

                    newEstimatesContainer.insertAdjacentHTML('beforeend', html);
                    estimateRowCount++;
                }

                // Initialize spec options display state based on type
                document.querySelectorAll('.spec-select').forEach(select => {
                    const rowId = select.closest('.spec-row').id;
                    updateSpecOptions(rowId);
                });
            });

            // Function to remove specification row
            function removeSpecRow(rowId) {
                document.getElementById(rowId).remove();
            }

            // Function to remove estimate row
            function removeEstimateRow(rowId) {
                document.getElementById(rowId).remove();
            }

            // Function to add option to a specification
            function addOption(rowId) {
                const optionInput = document.getElementById(`${rowId}-option-input`);
                const optionsContainer = document.getElementById(`${rowId}-options-container`);

                if (!optionInput.value.trim()) return;

                // Determine if this is an existing or new specification
                let name;
                if (rowId.startsWith('spec-row-existing-')) {
                    // For existing specifications, get the index from the hidden input
                    const hiddenInput = document.querySelector(`#${rowId} input[name^="spesifikasi"][name$="[id]"]`);
                    const match = hiddenInput.name.match(/spesifikasi\[(\d+)\]/);
                    const index = match ? match[1] : 0;
                    name = `spesifikasi[${index}][pilihan][]`;
                } else {
                    // For new specifications, extract the index from the rowId
                    const index = rowId.split('-')[3];
                    name = `new_spesifikasi[${index}][pilihan][]`;
                }

                const html = `
                <div class="d-flex align-items-center mt-1 option-item">
                    <input type="hidden" name="${name}" value="${optionInput.value.trim()}">
                    <span class="badge bg-primary me-2">${optionInput.value.trim()}</span>
                    <button type="button" class="btn btn-sm btn-ghost-danger" onclick="this.parentElement.remove()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M18 6l-12 12" />
                            <path d="M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                `;

                optionsContainer.insertAdjacentHTML('beforeend', html);
                optionInput.value = '';
            }

            // Function to update specification options based on spec type
            function updateSpecOptions(rowId) {
                const row = document.getElementById(rowId);
                if (!row) return;

                const specSelect = row.querySelector('.spec-select');
                if (!specSelect) return;

                const optionsSection = row.querySelector('[id$="-options-container"]').parentElement;
                const bahanSection = optionsSection.nextElementSibling;

                // Get the selected specification
                const spesifikasis = @json($spesifikasis);
                const selectedSpec = spesifikasis.find(spec => spec.id == specSelect.value);

                if (selectedSpec) {
                    // Show options section only for select or radio types
                    if (selectedSpec.tipe_input === 'select' || selectedSpec.tipe_input === 'radio') {
                        optionsSection.style.display = 'block';
                    } else {
                        optionsSection.style.display = 'none';
                    }

                    // Always show bahan selection
                    bahanSection.style.display = 'block';
                }
            }
        </script>
    @endpush
@endsection
