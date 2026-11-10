@extends('layouts.layouts_dashboard')

@section('title', 'Spesifikasi Manajemen')
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center">
                <div>
                    <h3 class="card-title">Daftar Spesifikasi</h3>
                </div>
                <div class="d-flex gap-2 flex-grow-1 justify-content-end ">
                    <form action="{{ route('spesifikasi.index') }}" method="GET" class="flex-grow-1">
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
                                placeholder="Cari spesifikasi...">
                        </div>
                    </form>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Filter Tipe: {{ request('tipe_input') ? ucfirst(request('tipe_input')) : 'Semua' }}
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item"
                                href="{{ route('spesifikasi.index', array_merge(request()->except('tipe_input'), ['tipe_input' => ''])) }}">Semua</a>
                            <a class="dropdown-item"
                                href="{{ route('spesifikasi.index', array_merge(request()->except('tipe_input'), ['tipe_input' => 'number'])) }}">Number</a>
                            <a class="dropdown-item"
                                href="{{ route('spesifikasi.index', array_merge(request()->except('tipe_input'), ['tipe_input' => 'select'])) }}">Select</a>
                            <a class="dropdown-item"
                                href="{{ route('spesifikasi.index', array_merge(request()->except('tipe_input'), ['tipe_input' => 'text'])) }}">Text</a>
                        </div>
                    </div>
                    <a href="{{ route('spesifikasi.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Tambah Spesifikasi
                    </a>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table card-table table-vcenter text-nowrap">
                <thead>
                    <tr>
                        <th>Nama Spesifikasi</th>
                        <th>Tipe Input</th>
                        <th>Satuan</th>
                        <th class="w-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($spesifikasi as $item)
                        <tr>
                            <td class="font-medium">{{ $item->nama_spesifikasi }}</td>
                            <td>
                                <span
                                    class="badge bg-{{ $item->isNumeric() ? 'blue' : ($item->isSelect() ? 'purple' : 'green') }}-lt">
                                    {{ $item->tipe_input }}
                                </span>
                            </td>
                            <td>{{ $item->satuan ?? '-' }}</td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="{{ route('spesifikasi.show', $item->id) }}"
                                        class="btn btn-icon btn-ghost-info" data-bs-toggle="tooltip" title="Show">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                            <path
                                                d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('spesifikasi.edit', $item->id) }}"
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
                                    <form action="{{ route('spesifikasi.destroy', $item->id) }}" method="POST"
                                        class="inline" id="delete-form-{{ $item->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-icon btn-ghost-danger"
                                            data-bs-toggle="tooltip" title="Delete"
                                            onclick="confirmDelete('delete-form-{{ $item->id }}')">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-trash" width="24" height="24"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                fill="none">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 7l16 0" />
                                                <path d="M10 11l0 6" />
                                                <path d="M14 11l0 6" />
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    @if ($spesifikasi->count() == 0)
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="empty">
                                    <div class="empty-img">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-database-off" width="50"
                                            height="50" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path
                                                d="M12.983 8.978c3.955 -.182 7.017 -1.446 7.017 -2.978c0 -1.657 -3.582 -3 -8 -3c-1.661 0 -3.204 .19 -4.483 .515m-2.783 1.228c-.535 .337 -.734 .715 -.734 1.257c0 1.22 1.944 2.271 4.734 2.74">
                                            </path>
                                            <path
                                                d="M4 6v6c0 1.657 3.582 3 8 3c.986 0 1.93 -.067 2.802 -.19m3.187 -.82c1.251 -.53 2.011 -1.228 2.011 -1.99v-6">
                                            </path>
                                            <path
                                                d="M4 12v6c0 1.657 3.582 3 8 3c3.217 0 5.991 -.712 7.261 -1.74m.739 -3.26v-4">
                                            </path>
                                            <path d="M3 3l18 18"></path>
                                        </svg>
                                    </div>
                                    <p class="empty-title">Tidak ada data</p>
                                    <p class="empty-subtitle text-muted">
                                        Tidak ada data spesifikasi yang tersedia.
                                    </p>
                                    <div class="empty-action">
                                        <a href="{{ route('spesifikasi.create') }}" class="btn btn-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <line x1="12" y1="5" x2="12" y2="19" />
                                                <line x1="5" y1="12" x2="19" y2="12" />
                                            </svg>
                                            Tambah Spesifikasi
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex align-items-center">
            {{ $spesifikasi->links('dev.components.pagination') }}
        </div>
    </div>

    @push('scripts')
        <script>
            // Confirm single item delete
            function confirmDelete(formId) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        showLoading('Deleting...');
                        document.getElementById(formId).submit();
                    }
                });
            }
        </script>
    @endpush
@endsection
