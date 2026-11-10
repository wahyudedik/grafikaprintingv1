@extends('layouts.layouts_dashboard')

@section('title', 'Detail Transaksi')
@section('content')
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Detail Transaksi: {{ $transaksi->kode }}</h3>
                        <div>
                            <a href="{{ route('transaksi.edit', $transaksi->id) }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                    <path d="M16 5l3 3" />
                                </svg>
                                Edit
                            </a>
                            <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M5 12l14 0" />
                                    <path d="M5 12l6 6" />
                                    <path d="M5 12l6 -6" />
                                </svg>
                                Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Transaction Details -->
                            <div class="col-md-6">
                                <h4>Info Transaksi</h4>
                                <table class="table table-vcenter">
                                    <tr>
                                        <th>Kode Transaksi</th>
                                        <td>{{ $transaksi->kode }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal</th>
                                        <td>{{ $transaksi->tanggal_dibuat->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'bg-yellow',
                                                    'processing' => 'bg-blue',
                                                    'quality_check' => 'bg-purple',
                                                    'completed' => 'bg-green',
                                                    'cancelled' => 'bg-red',
                                                ];
                                                $statusLabels = [
                                                    'pending' => 'Pending',
                                                    'processing' => 'Diproses',
                                                    'quality_check' => 'QC',
                                                    'completed' => 'Selesai',
                                                    'cancelled' => 'Dibatalkan',
                                                ];
                                            @endphp
                                            <span class="badge text-white {{ $statusColors[$transaksi->status] }}">
                                                {{ $statusLabels[$transaksi->status] }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Progress</th>
                                        <td>
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    {{ $transaksi->progress_percentage }}%
                                                </div>
                                                <div class="col">
                                                    <div class="progress">
                                                        <div class="progress-bar"
                                                            style="width: {{ $transaksi->progress_percentage }}%"
                                                            role="progressbar"
                                                            aria-valuenow="{{ $transaksi->progress_percentage }}"
                                                            aria-valuemin="0" aria-valuemax="100"
                                                            aria-label="{{ $transaksi->progress_percentage }}% Complete">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Metode Pembayaran</th>
                                        <td>{{ $transaksi->payment_method }}</td>
                                    </tr>
                                    <tr>
                                        <th>Estimasi Selesai</th>
                                        <td>{{ $transaksi->estimasi_selesai->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total Harga</th>
                                        <td>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Terbayar</th>
                                        <td>Rp {{ number_format($transaksi->terbayar ?? $transaksi->total_harga, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kembali</th>
                                        <td>Rp {{ number_format($transaksi->kembali ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Customer Details -->
                            <div class="col-md-6">
                                <h4>Info Pelanggan</h4>
                                <table class="table table-vcenter">
                                    <tr>
                                        <th>Nama</th>
                                        <td>{{ $transaksi->pelanggan->nama ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $transaksi->pelanggan->email ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Telepon</th>
                                        <td>{{ $transaksi->pelanggan->telepon ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Alamat</th>
                                        <td>{{ $transaksi->pelanggan->alamat ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Transaction Items -->
                            <div class="col-12 mt-4">
                                <h4>Item Transaksi</h4>
                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table">
                                        <thead>
                                            <tr>
                                                <th>Produk</th>
                                                <th>Kuantitas</th>
                                                <th>Harga Satuan</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($transaksi->transaksiItem as $item)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <strong>{{ $item->produk->nama_produk ?? 'Produk Tidak Ditemukan' }}</strong>
                                                            @if ($item->transaksiItemSpecifications->count() > 0)
                                                                <div class="mt-1">
                                                                    @foreach ($item->transaksiItemSpecifications as $spec)
                                                                        <div class="text-muted small">
                                                                            <strong>{{ $spec->spesifikasiProduk->spesifikasi->nama_spesifikasi ?? 'Spesifikasi' }}</strong>:
                                                                            {{ $spec->value }}
                                                                            @if ($spec->bahan)
                                                                                ({{ $spec->bahan->nama_bahan }})
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>{{ $item->kuantitas }}</td>
                                                    <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                                    <td>Rp
                                                        {{ number_format($item->kuantitas * $item->harga_satuan, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" class="text-end">Total:</th>
                                                <th>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- Additional Notes -->
                            @if ($transaksi->catatan)
                                <div class="col-12 mt-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Catatan</h4>
                                        </div>
                                        <div class="card-body">
                                            {{ $transaksi->catatan }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <div>
                            @if ($transaksi->status != 'completed' && $transaksi->status != 'cancelled')
                                <form action="{{ route('transaksi.update', $transaksi->id) }}" method="POST"
                                    class="d-inline" id="status-form">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="pelanggan_id" value="{{ $transaksi->pelanggan_id }}">
                                    <input type="hidden" name="payment_method" value="{{ $transaksi->payment_method }}">
                                    <input type="hidden" name="estimasi_selesai"
                                        value="{{ $transaksi->estimasi_selesai->format('Y-m-d') }}">
                                    <input type="hidden" name="catatan" value="{{ $transaksi->catatan }}">

                                    @foreach ($transaksi->transaksiItem as $index => $item)
                                        <input type="hidden" name="items[{{ $index }}][id]"
                                            value="{{ $item->id }}">
                                        <input type="hidden" name="items[{{ $index }}][produk_id]"
                                            value="{{ $item->produk_id }}">
                                        <input type="hidden" name="items[{{ $index }}][kuantitas]"
                                            value="{{ $item->kuantitas }}">
                                        <input type="hidden" name="items[{{ $index }}][harga_satuan]"
                                            value="{{ $item->harga_satuan }}">
                                    @endforeach

                                    <div class="btn-group">
                                        <button type="button" class="btn btn-outline-primary dropdown-toggle"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            Ubah Status
                                        </button>
                                        <div class="dropdown-menu">
                                            @if ($transaksi->status != 'processing')
                                                <button type="button" onclick="updateStatus('processing')"
                                                    class="dropdown-item">Proses</button>
                                            @endif
                                            @if ($transaksi->status != 'quality_check')
                                                <button type="button" onclick="updateStatus('quality_check')"
                                                    class="dropdown-item">Quality Check</button>
                                            @endif
                                            @if ($transaksi->status != 'completed')
                                                <button type="button" onclick="updateStatus('completed')"
                                                    class="dropdown-item">Selesai</button>
                                            @endif
                                            @if ($transaksi->status != 'cancelled')
                                                <button type="button" onclick="updateStatus('cancelled')"
                                                    class="dropdown-item text-danger">Batalkan</button>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            @endif
                        </div>
                        <div>
                            <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $transaksi->id }})">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M4 7l16 0" />
                                    <path d="M10 11l0 6" />
                                    <path d="M14 11l0 6" />
                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                </svg>
                                Hapus
                            </button>
                            <a href="{{ route('transaksi.generateInvoice', $transaksi->id) }}" class="btn btn-info"
                                target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-invoice"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                                    <path d="M9 7l6 0"></path>
                                    <path d="M9 13l6 0"></path>
                                    <path d="M13 17l2 0"></path>
                                </svg>
                                Cetak Invoice
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden delete form -->
    <form id="delete-form" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            function confirmDelete(id) {
                Swal.fire({
                    title: 'Anda yakin?',
                    text: "Data transaksi yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Sedang Memproses',
                            text: 'Mohon tunggu...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        const form = document.getElementById('delete-form');
                        form.action = `{{ route('transaksi.destroy', '') }}/${id}`;
                        form.submit();
                    }
                });
            }

            function updateStatus(status) {
                Swal.fire({
                    title: 'Ubah Status?',
                    text: `Apakah Anda yakin ingin mengubah status transaksi menjadi ${status}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, ubah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('status-form');
                        const statusInput = document.createElement('input');
                        statusInput.type = 'hidden';
                        statusInput.name = 'status';
                        statusInput.value = status;
                        form.appendChild(statusInput);

                        Swal.fire({
                            title: 'Sedang Memproses',
                            text: 'Mohon tunggu...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        form.submit();
                    }
                });
            }
        </script>
    @endpush
@endsection
