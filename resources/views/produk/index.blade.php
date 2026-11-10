@extends('layouts.layouts_dashboard')

@section('title', 'Manajemen Produk')
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center">
                <div>
                    <h3 class="card-title">Daftar Produk</h3>
                </div>
                <div class="d-flex gap-2 flex-grow-1 justify-content-end">
                    <form action="{{ route('produk.index') }}" method="GET" class="flex-grow-1">
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <circle cx="10" cy="10" r="7" />
                                    <line x1="21" y1="21" x2="15" y2="15" />
                                </svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                placeholder="Cari produk...">
                        </div>
                    </form>

                    <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Kategori: {{ $selectedCategory ? $selectedCategory->nama_kategori : 'Semua' }}
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item"
                                href="{{ route('produk.index', array_merge(request()->except('kategori_id'), ['kategori_id' => ''])) }}">Semua</a>
                            @foreach ($kategories as $kategori)
                                <a class="dropdown-item"
                                    href="{{ route('produk.index', array_merge(request()->except('kategori_id'), ['kategori_id' => $kategori->id])) }}">
                                    {{ $kategori->nama_kategori }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <a href="{{ route('produk.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Tambah Produk
                    </a>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table card-table table-vcenter text-nowrap">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Spesifikasi</th>
                        <th>Estimasi Waktu</th>
                        <th class="w-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($produks as $produk)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if (!empty($produk->gambar) && isset($produk->gambar[0]))
                                        <span class="avatar me-2"
                                            style="background-image: url({{ asset($produk->gambar[0]) }})"></span>
                                    @else
                                        <span class="avatar me-2">{{ substr($produk->nama_produk, 0, 2) }}</span>
                                    @endif
                                    <div>
                                        <div class="font-weight-medium">{{ $produk->nama_produk }}</div>
                                        <div class="text-muted">{{ Str::limit($produk->deskripsi, 50) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $produk->kategori->nama_kategori ?? 'Tidak ada kategori' }}</td>
                            <td>{{ $produk->spesifikasiProduk->count() }} spesifikasi</td>
                            <td>
                                @if ($produk->estimasiProduk->count() > 0)
                                    <span class="text-success">Tersedia</span>
                                @else
                                    <span class="text-muted">Belum diatur</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="{{ route('produk.show', $produk->id) }}" class="btn btn-icon btn-ghost-info"
                                        data-bs-toggle="tooltip" title="View">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                            <path
                                                d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('produk.edit', $produk->id) }}"
                                        class="btn btn-icon btn-ghost-warning" data-bs-toggle="tooltip" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                            <path
                                                d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                        </svg>
                                    </a>
                                    <button type="button" class="btn btn-icon btn-ghost-danger delete-btn"
                                        data-id="{{ $produk->id }}" data-bs-toggle="tooltip" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M4 7l16 0" />
                                            <path d="M10 11l0 6" />
                                            <path d="M14 11l0 6" />
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="empty">
                                    <div class="empty-img">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="128"
                                            height="128" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                            <path d="M9 10l.01 0" />
                                            <path d="M15 10l.01 0" />
                                            <path d="M9.5 15.25a3.5 3.5 0 0 1 5 0" />
                                        </svg>
                                    </div>
                                    <p class="empty-title">Tidak ada data produk</p>
                                    <p class="empty-subtitle text-muted">
                                        Silahkan tambahkan produk baru atau ubah filter pencarian
                                    </p>
                                    <div class="empty-action">
                                        <a href="{{ route('produk.create') }}" class="btn btn-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <line x1="12" y1="5" x2="12" y2="19" />
                                                <line x1="5" y1="12" x2="19" y2="12" />
                                            </svg>
                                            Tambah Produk
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex align-items-center">
            {{ $produks->links('dev.components.pagination') }}
        </div>
    </div>

    <!-- Hidden delete form -->
    <form id="delete-form" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const deleteForm = document.getElementById('delete-form');
                const deleteButtons = document.querySelectorAll('.delete-btn');

                // Setup delete buttons
                deleteButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        confirmDelete(id);
                    });
                });

                // Individual delete confirmation
                window.confirmDelete = function(id) {
                    Swal.fire({
                        title: 'Anda yakin?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            showLoading('Menghapus...');
                            deleteForm.action = `{{ route('produk.destroy', '') }}/${id}`;
                            deleteForm.submit();
                        }
                    });
                };
            });
        </script>
    @endpush
@endsection
