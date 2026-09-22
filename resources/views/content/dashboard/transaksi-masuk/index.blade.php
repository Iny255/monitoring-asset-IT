@extends('layouts/contentNavbarLayout')

@section('title', 'Penerimaan Aset')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HERO HEADER --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-primary me-3">
                            <span class="avatar-initial rounded">
                                <i class="bx bx-log-in-circle fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">Penerimaan Aset</h3>
                            <small class="text-muted">Kelola transaksi pengadaan dan barang masuk dari supplier</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalFilter">
                            <i class="bx bx-filter-alt me-1"></i> Filter
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bx bx-export me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('transaksi-masuk.cetak', request()->query()) }}" target="_blank">
                                        <i class="bx bxs-file-pdf text-danger me-2"></i> Export PDF
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('transaksi-masuk.exportExcel', request()->query()) }}">
                                        <i class="bx bxs-file-export text-success me-2"></i> Export Excel
                                    </a>
                                </li>
                            </ul>
                        </div>
                        @auth
                            @if (auth()->user()->role === 'petugas' || auth()->user()->role === 'super_admin')
                                <a href="{{ route('transaksi-masuk.create') }}" class="btn btn-primary">
                                    <i class="bx bx-plus me-1"></i> Input Barang Masuk
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        {{-- ALERT --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <x-company-filter-banner />

        <div class="card border-0 shadow-sm">
            <div class="card-body">

                @if(request()->anyFilled(['perusahaan_id', 'tanggal_awal', 'tanggal_akhir', 'supplier_id', 'jenis_masuk', 'status_aset', 'search']))
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-label-primary px-3 py-2">
                            <i class="bx bx-filter-alt me-1"></i> Filter Aktif
                        </span>
                        <a href="{{ route('transaksi-masuk.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bx bx-x me-1"></i> Reset Filter
                        </a>
                    </div>
                @endif


            <div class="table-responsive">
                <table class="table table-bordered table-hover">

                    <thead class="table-primary text-center">
                        <tr>

                            <th width="60">NO</th>
                            @if (auth()->user()->role === 'super_admin')
                                <th>PERUSAHAAN</th>
                            @endif
                            <th>DATA ASET</th>
                            <th>ASAL</th>
                            <th>TGL PEMBELIAN</th>
                            <th>JUMLAH</th>
                            <th>HARGA SATUAN</th>
                            <th>GARANSI</th>
                            <th>KET. PENERIMAAN</th>
                            <th width="150">AKSI</th>

                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($masuks as $index => $masuk)
                            <tr>

                                {{-- NO --}}
                                <td class="text-center">
                                    {{ $masuks->firstItem() + $index }}
                                </td>

                                {{-- PERUSAHAAN --}}
                                @if (auth()->user()->role === 'super_admin')
                                    <td>
                                        <x-company-badge :perusahaan="$masuk->perusahaan" />
                                    </td>
                                @endif

                                {{-- DATA ASET --}}
                                <td>

                                    @if ($masuk->inventaris && $masuk->inventaris->isNotEmpty())
                                        <div class="mb-1">
                                            @if ($masuk->inventaris->count() == 1)
                                                @php $inv = $masuk->inventaris->first(); @endphp
                                                @if ($inv->is_transfer)
                                                    <span class="badge bg-label-warning" title="Aset telah dimutasi ke perusahaan lain">
                                                        <i class="bx bx-transfer me-1"></i>{{ $inv->kode_aset }} (Dimutasi)
                                                    </span>
                                                @else
                                                    <span class="badge bg-label-primary">
                                                        {{ $inv->kode_aset }}
                                                    </span>
                                                @endif
                                            @elseif ($masuk->inventaris->count() <= 3)
                                                @foreach ($masuk->inventaris as $inv)
                                                    @if ($inv->is_transfer)
                                                        <span class="badge bg-label-warning me-1 mb-1" title="Aset telah dimutasi ke perusahaan lain">
                                                            <i class="bx bx-transfer me-1"></i>{{ $inv->kode_aset }} (Dimutasi)
                                                        </span>
                                                    @else
                                                        <span class="badge bg-label-primary me-1 mb-1">
                                                            {{ $inv->kode_aset }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            @else
                                                @php
                                                    $mutasiCount = $masuk->inventaris->where('is_transfer', true)->count();
                                                @endphp
                                                <span class="badge bg-label-primary mb-1"
                                                    title="{{ $masuk->inventaris->pluck('kode_aset')->implode(', ') }}">
                                                    {{ $masuk->inventaris->first()->kode_aset }} - {{ $masuk->inventaris->last()->kode_aset }}
                                                    ({{ $masuk->inventaris->count() }} Aset)
                                                </span>
                                                @if ($mutasiCount > 0)
                                                    <span class="badge bg-label-warning mb-1" title="{{ $mutasiCount }} aset telah dimutasi ke perusahaan lain">
                                                        <i class="bx bx-transfer me-1"></i>{{ $mutasiCount }} Dimutasi
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    @endif

                                    <span class="fw-semibold">
                                        {{ $masuk->dataAset->kategori->nama_barang ?? '-' }}
                                    </span>
                                    <br>

                                    <small class="text-muted">

                                        {{ $masuk->dataAset->merek ?? '-' }}
                                        -
                                        {{ $masuk->dataAset->type ?? '-' }}

                                    </small>

                                </td>

                                {{-- SUPPLIER --}}
                                <td>

                                    @if ($masuk->jenis_masuk == 'Pembelian')
                                        {{ $masuk->supplier->nama_supplier ?? '-' }}
                                    @else
                                        {{ $masuk->perusahaanAsal->nama_perusahaan ?? '-' }}
                                    @endif

                                </td>
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($masuk->tanggal_pembelian)->format('d-m-Y') }}
                                </td>

                                {{-- JUMLAH --}}
                                <td class="text-center">

                                    {{ number_format($masuk->jumlah) }}

                                </td>

                                {{-- HARGA --}}
                                <td class="text-end">

                                    @if ($masuk->jenis_masuk == 'Pembelian')
                                        Rp {{ number_format($masuk->harga_satuan, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif

                                </td>

                                {{-- GARANSI --}}
                                <td class="text-center">

                                    {{ $masuk->garansi }} Bulan

                                </td>

                                {{-- KETERANGAN --}}
                                <td class="text-center">

                                    @if ($masuk->ket_penerimaan == 'BAIK')
                                        <span class="badge bg-success">

                                            BAIK

                                        </span>
                                    @else
                                        <span class="badge bg-danger">

                                            RUSAK

                                        </span>
                                    @endif

                                </td>

                                {{-- AKSI --}}
                                <td class="text-center">
                                    @php
                                        $isMapped = $masuk->isMapped();
                                    @endphp

                                    @if ($isMapped)
                                        <div class="mb-1">
                                            <span class="badge bg-label-info" style="font-size: 10px;" title="Sebagian atau seluruh aset telah di-mapping">
                                                <i class="bx bx-check-double me-1"></i>Sudah Dimapping
                                            </span>
                                        </div>
                                    @else
                                        <div class="mb-1">
                                            <span class="badge bg-label-success" style="font-size: 10px;" title="Aset belum di-mapping, data dapat diedit">
                                                <i class="bx bx-edit me-1"></i>Belum Dimapping
                                            </span>
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-center gap-1">

                                        <a href="{{ route('transaksi-masuk.show', $masuk->id) }}"
                                            class="btn btn-sm btn-icon btn-outline-primary" title="Lihat Detail">
                                            <i class="bx bx-show"></i>
                                        </a>

                                        @if (in_array(auth()->user()->role, ['petugas', 'super_admin']))
                                            @if ($isMapped)
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary btn-edit-locked"
                                                    title="Aset sudah di-mapping ke pengguna/ruangan (Terkunci)">
                                                    <i class="bx bx-lock-alt"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary btn-delete-locked"
                                                    title="Aset sudah di-mapping (Tidak dapat dihapus)">
                                                    <i class="bx bx-lock-alt"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary btn-edit"
                                                    data-url="{{ route('transaksi-masuk.edit', $masuk->id) }}"
                                                    title="Edit Penerimaan Aset">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete"
                                                    data-id="{{ $masuk->id }}"
                                                    title="Hapus Penerimaan Aset">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            @endif
                                        @endif

                                    </div>

                                    @if (!$isMapped)
                                        <form id="delete-form-{{ $masuk->id }}"
                                            action="{{ route('transaksi-masuk.destroy', $masuk->id) }}" method="POST"
                                            style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="{{ auth()->user()->role === 'super_admin' ? 10 : 9 }}" class="text-center">

                                    Data penerimaan aset belum ada

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

                {{-- PAGINATION --}}
                <div class="mt-4">
                    {{ $masuks->links('pagination::bootstrap-4') }}
                </div>

            </div>
        </div>
    </div>

    <!-- ================= FILTER MODAL ================= -->
    <div class="modal fade" id="modalFilter" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="GET" action="{{ route('transaksi-masuk.index') }}">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-filter-alt me-2 text-primary"></i> Filter Data Penerimaan Aset
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            @if (auth()->user()->role === 'super_admin')
                                <div class="col-md-6">
                                    <label class="form-label">Perusahaan</label>
                                    <select name="perusahaan_id" class="form-select">
                                        <option value="">Semua Perusahaan</option>
                                        @foreach ($perusahaans as $p)
                                            <option value="{{ $p->id }}"
                                                {{ request('perusahaan_id') == $p->id ? 'selected' : '' }}>
                                                {{ $p->nama_perusahaan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="col-md-{{ auth()->user()->role === 'super_admin' ? '6' : '12' }}">
                                <label class="form-label">Supplier</label>
                                <select name="supplier_id" class="form-select">
                                    <option value="">Semua Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}"
                                            {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->nama_supplier }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Awal</label>
                                <input type="date" name="tanggal_awal" class="form-control"
                                    value="{{ request('tanggal_awal') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="tanggal_akhir" class="form-control"
                                    value="{{ request('tanggal_akhir') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Jenis Masuk</label>
                                <select name="jenis_masuk" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="Pembelian"
                                        {{ request('jenis_masuk') == 'Pembelian' ? 'selected' : '' }}>
                                        Pembelian
                                    </option>
                                    <option value="Mutasi" {{ request('jenis_masuk') == 'Mutasi' ? 'selected' : '' }}>
                                        Mutasi Antar Perusahaan
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status Aset</label>
                                <select name="status_aset" class="form-select">
                                    <option value="">Semua (Termasuk Dimutasi)</option>
                                    <option value="aktif" {{ request('status_aset') == 'aktif' ? 'selected' : '' }}>
                                        Aset Aktif di Perusahaan
                                    </option>
                                    <option value="dimutasi" {{ request('status_aset') == 'dimutasi' ? 'selected' : '' }}>
                                        Aset Telah Dimutasi Keluar
                                    </option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Pencarian</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Nama barang / merek / type / kode aset" value="{{ request('search') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('transaksi-masuk.index') }}" class="btn btn-secondary">
                            <i class="bx bx-refresh me-1"></i> Reset
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-search me-1"></i> Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


{{-- SCRIPT --}}
@auth
    @if (auth()->user()->role === 'petugas' || auth()->user()->role === 'super_admin')
        @section('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    document.querySelectorAll('.btn-delete').forEach(btn => {
                        btn.addEventListener('click', function() {

                            const id = this.dataset.id;

                            Swal.fire({
                                title: 'Yakin hapus data?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Ya',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    document.getElementById(`delete-form-${id}`).submit();
                                }
                            });

                        });
                    });

                });
                //edit
                document.querySelectorAll('.btn-edit').forEach(btn => {

                    btn.addEventListener('click', function() {

                        let url = this.dataset.url;

                        Swal.fire({

                            title: 'Edit Data?',
                            text: 'Anda akan masuk ke halaman edit data.',
                            icon: 'question',

                            showCancelButton: true,

                            confirmButtonColor: '#696cff',
                            cancelButtonColor: '#8592a3',

                            confirmButtonText: 'Ya, Edit',
                            cancelButtonText: 'Batal'

                        }).then((result) => {

                            if (result.isConfirmed) {

                                window.location.href = url;

                            }

                        });

                    });

                });

                // Handlers untuk tombol terkunci karena sudah di-mapping
                document.querySelectorAll('.btn-edit-locked').forEach(btn => {
                    btn.addEventListener('click', function() {
                        Swal.fire({
                            title: 'Data Terkunci!',
                            text: 'Data penerimaan ini tidak dapat diedit karena sebagian atau seluruh aset sudah di-mapping ke pengguna atau ruangan.',
                            icon: 'info',
                            confirmButtonColor: '#696cff'
                        });
                    });
                });

                document.querySelectorAll('.btn-delete-locked').forEach(btn => {
                    btn.addEventListener('click', function() {
                        Swal.fire({
                            title: 'Tidak Dapat Dihapus!',
                            text: 'Data penerimaan ini tidak dapat dihapus karena sebagian atau seluruh aset sudah di-mapping ke pengguna atau ruangan.',
                            icon: 'warning',
                            confirmButtonColor: '#696cff'
                        });
                    });
                });
            </script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    const perusahaanSelect = document.querySelector('[name="id_perusahaan"]');
                    const kategoriSelect = document.getElementById('kategori');

                    function loadKategori(id) {

                        if (!id) {
                            kategoriSelect.innerHTML = '<option value="">-- Pilih Barang --</option>';
                            return;
                        }

                        fetch('/dashboard/get-kategori/' + id)
                            .then(res => res.json())
                            .then(data => {

                                let html = '<option value="">-- Pilih Barang --</option>';

                                data.forEach(item => {
                                    html += `<option value="${item.id}">${item.nama_barang}</option>`;
                                });

                                kategoriSelect.innerHTML = html;
                            });
                    }

                    if (perusahaanSelect) {

                        perusahaanSelect.addEventListener('change', function() {
                            loadKategori(this.value);
                        });

                        // 🔥 auto load saat pertama
                        if (perusahaanSelect.value) {
                            loadKategori(perusahaanSelect.value);
                        }
                    }

                    const filterPerusahaanSelect = document.querySelector('form[action="{{ route('transaksi-masuk.index') }}"] select[name="perusahaan_id"]');
                    const filterSupplierSelect = document.querySelector('form[action="{{ route('transaksi-masuk.index') }}"] select[name="supplier_id"]');

                    if (filterPerusahaanSelect && filterSupplierSelect) {
                        filterPerusahaanSelect.addEventListener('change', function() {
                            const id = this.value;
                            if (!id) {
                                filterSupplierSelect.innerHTML = '<option value="">Semua Supplier</option>';
                                return;
                            }

                            fetch('/dashboard/get-supplier/' + id)
                                .then(res => res.json())
                                .then(data => {
                                    let html = '<option value="">Semua Supplier</option>';
                                    data.forEach(item => {
                                        html += `<option value="${item.id}">${item.nama_supplier}</option>`;
                                    });
                                    filterSupplierSelect.innerHTML = html;
                                });
                        });
                    }
                });
            </script>
        @endsection
    @endif
@endauth
