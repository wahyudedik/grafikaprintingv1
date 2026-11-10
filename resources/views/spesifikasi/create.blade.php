@extends('layouts.layouts_dashboard')

@section('title', 'Tambah Spesifikasi')
@section('content')
    <div class="container-xl">
        <div class="row g-3">
            <div class="col-12">
                <form action="{{ route('spesifikasi.store') }}" method="POST" class="card"
                    onsubmit="showLoading('Menambahkan spesifikasi...')">
                    @csrf
                    <div class="card-header">
                        <h3 class="card-title">Tambah Spesifikasi Baru</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label required">Nama Spesifikasi</label>
                                    <input type="text"
                                        class="form-control @error('nama_spesifikasi') is-invalid @enderror"
                                        name="nama_spesifikasi" value="{{ old('nama_spesifikasi') }}"
                                        placeholder="Contoh: Ukuran, Warna, Jumlah Halaman">
                                    @error('nama_spesifikasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label required">Tipe Input</label>
                                    <select class="form-select @error('tipe_input') is-invalid @enderror" name="tipe_input"
                                        id="tipe_input">
                                        <option value="">Pilih tipe input</option>
                                        @foreach ($tipeInput as $key => $value)
                                            <option value="{{ $value }}"
                                                {{ old('tipe_input') == $value ? 'selected' : '' }}>
                                                {{ ucfirst($key) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tipe_input')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6" id="satuan_field">
                                <div class="form-group">
                                    <label class="form-label">Satuan</label>
                                    <input type="text" class="form-control @error('satuan') is-invalid @enderror"
                                        name="satuan" value="{{ old('satuan') }}" placeholder="Contoh: cm, pcs, halaman">
                                    <small class="form-hint">Opsional. Cocok untuk tipe number (contoh: cm, kg, pcs)</small>
                                    @error('satuan')
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

                        <a href="{{ route('spesifikasi.index') }}" class="btn btn-secondary">
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
            // Show/hide satuan field based on input type
            const tipeInputSelect = document.getElementById('tipe_input');
            const satuanField = document.getElementById('satuan_field');

            function toggleSatuanField() {
                if (tipeInputSelect.value === 'number') {
                    satuanField.style.display = 'block';
                } else {
                    satuanField.style.display = 'none';
                }
            }

            // Initial toggle on page load
            toggleSatuanField();

            // Toggle when selection changes
            tipeInputSelect.addEventListener('change', toggleSatuanField);
        </script>
    @endpush
@endsection
