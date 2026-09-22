@extends('layouts/contentNavbarLayout')

@section('title', 'Kategori Barang')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HERO HEADER --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-primary me-3">
                            <span class="avatar-initial rounded">
                                <i class="bx bx-category-alt fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">Kategori Barang</h3>
                            <small class="text-muted">Kelola master kategori barang (Laptop, Printer, PC, Monitor, dll)</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalFilter">
                            <i class="bx bx-filter-alt me-1"></i> Filter
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
                            <i class="bx bx-plus me-1"></i> Tambah Kategori
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ALERT --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bx bx-check-circle fs-4 me-2"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error-circle fs-4 me-2"></i>
                    <div>{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <x-company-filter-banner />

        <div class="card shadow-sm border-0">
            <div class="card-body">
                @if(request()->filled('search') || request()->filled('perusahaan_id'))
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-label-primary px-3 py-2">
                            <i class="bx bx-filter-alt me-1"></i> Filter Aktif
                        </span>
                        <a href="{{ route('aset.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bx bx-x me-1"></i> Reset Filter
                        </a>
                    </div>
                @endif

                {{-- TABLE --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th width="60">NO</th>
                                <th width="120">KODE</th>
                                <th>NAMA BARANG</th>
                                @if ($isGrouped)
                                    <th>DIGUNAKAN PADA PERUSAHAAN</th>
                                    <th width="170">TOTAL DISTRIBUSI</th>
                                @else
                                    <th>PERUSAHAAN</th>
                                @endif
                                <th width="130">AKSI</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($kategoris as $index => $kategori)
                                <tr>
                                    <td class="text-center fw-medium">{{ $kategoris->firstItem() + $index }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-label-primary fw-bold">
                                            {{ $kategori->kode_barang }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs bg-label-primary me-2">
                                                <span class="avatar-initial rounded"><i class="bx bx-purchase-tag fs-6"></i></span>
                                            </div>
                                            <span class="fw-semibold text-dark">{{ $kategori->nama_barang }}</span>
                                        </div>
                                    </td>

                                    @if ($isGrouped)
                                        <td>
                                            @if ($kategori->daftar_perusahaan)
                                                @php
                                                    $compList = explode('||', $kategori->daftar_perusahaan);
                                                @endphp
                                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                                    @foreach ($compList as $cIdx => $cName)
                                                        @if ($cIdx < 3)
                                                            <span class="badge bg-label-info">{{ $cName }}</span>
                                                        @endif
                                                    @endforeach
                                                    @if (count($compList) > 3)
                                                        <span class="badge bg-label-secondary" title="{{ implode(', ', array_slice($compList, 3)) }}">
                                                            +{{ count($compList) - 3 }} lainnya
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted fst-italic">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-primary rounded-pill px-3 py-2">
                                                <i class="bx bx-buildings me-1"></i> {{ $kategori->total_perusahaan }} Perusahaan
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-primary btn-sm btn-detail-perusahaan"
                                                data-nama="{{ $kategori->nama_barang }}"
                                                data-kode="{{ $kategori->kode_barang }}"
                                                title="Kelola kategori per perusahaan">
                                                <i class="bx bx-slider-alt me-1"></i> Kelola
                                            </button>
                                        </td>
                                    @else
                                        <td>
                                            <x-company-badge :perusahaan="$kategori->perusahaan" />
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary btn-edit"
                                                    data-id="{{ $kategori->id }}"
                                                    data-kode="{{ $kategori->kode_barang }}"
                                                    data-nama="{{ $kategori->nama_barang }}"
                                                    data-perusahaan_id="{{ $kategori->perusahaan_id }}"
                                                    title="Edit Kategori">
                                                    <i class="bx bx-edit"></i>
                                                </button>

                                                <form id="delete-form-{{ $kategori->id }}"
                                                    action="{{ route('aset.destroy', $kategori->id) }}" method="POST"
                                                    style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>

                                                <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete"
                                                    data-id="{{ $kategori->id }}"
                                                    title="Hapus Kategori">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isGrouped ? 6 : 5 }}" class="text-center py-4 text-muted">
                                        <i class="bx bx-folder-open fs-2 d-block mb-1"></i>
                                        Data kategori barang tidak ditemukan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="mt-4">
                    {{ $kategoris->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>

    {{-- ================= MODAL TAMBAH ================= --}}
    <div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('aset.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-plus-circle me-1 text-primary"></i> Tambah Kategori Barang
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        @if (auth()->user()->role === 'super_admin')
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Perusahaan <span class="text-danger">*</span></label>
                                <select name="perusahaan_id" id="tambah_perusahaan" class="form-select" required>
                                    <option value="">-- Pilih Perusahaan --</option>
                                    @foreach ($perusahaans as $p)
                                        <option value="{{ $p->id }}" {{ request('perusahaan_id') == $p->id ? 'selected' : '' }}>
                                            {{ $p->nama_perusahaan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kode Barang <span class="text-danger">*</span></label>
                            <input type="text" name="kode_barang" id="tambah_kode_barang" class="form-control text-uppercase" maxlength="10"
                                placeholder="Contoh : LP, PR, PC, MN" required>
                            <small class="text-muted">Kode unik kategori aset untuk penomoran.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="nama_barang" id="tambah_nama_barang" class="form-control text-uppercase"
                                placeholder="Contoh : LAPTOP, PRINTER, MONITOR" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================= MODAL EDIT ================= --}}
    <div class="modal fade" id="modalEditKategori" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="formEditKategori" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-edit-alt me-1 text-warning"></i> Edit Kategori Barang
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        @if (auth()->user()->role === 'super_admin')
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Perusahaan <span class="text-danger">*</span></label>
                                <select name="perusahaan_id" id="edit_perusahaan" class="form-select" required>
                                    @foreach ($perusahaans as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kode Barang <span class="text-danger">*</span></label>
                            <input type="text" id="edit_kode_barang" name="kode_barang" class="form-control text-uppercase"
                                maxlength="10" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" id="edit_nama_barang" name="nama_barang" class="form-control text-uppercase"
                                required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bx bx-save me-1"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ================= FILTER MODAL ================= -->
    <div class="modal fade" id="modalFilter" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form method="GET" action="{{ route('aset.index') }}">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-filter-alt me-2 text-primary"></i> Filter Data Kategori
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            @if (auth()->user()->role === 'super_admin')
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Perusahaan</label>
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

                            <div class="col-12">
                                <label class="form-label fw-semibold">Pencarian Kategori</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Cari nama / kode barang..." value="{{ request('search') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('aset.index') }}" class="btn btn-secondary">
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

    {{-- ================= MODAL DETAIL PERUSAHAAN ================= --}}
    <div class="modal fade" id="modalDetailPerusahaan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white d-flex align-items-center">
                        <i class="bx bx-purchase-tag me-2 fs-4"></i> Distribusi Kategori: <span id="judulBarang" class="fw-bold ms-2 badge bg-warning text-dark fs-6"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div class="text-muted small">
                            Daftar perusahaan yang menggunakan master kategori ini beserta jumlah jenis perangkat/tipe terkait.
                        </div>
                        @if (auth()->user()->role === 'super_admin')
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnTambahKePerusahaanLain">
                                <i class="bx bx-plus me-1"></i> Terapkan ke Perusahaan Lain
                            </button>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th width="50">NO</th>
                                    <th width="120">KODE</th>
                                    <th>NAMA PERUSAHAAN</th>
                                    <th width="150">TIPE ASET TERKAIT</th>
                                    <th width="120">AKSI</th>
                                </tr>
                            </thead>
                            <tbody id="listPerusahaan">
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // DELETE (HALAMAN UTAMA)
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            let id = this.dataset.id;
            Swal.fire({
                title: 'Yakin hapus kategori ini?',
                text: 'Data yang dihapus tidak bisa dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#696cff',
                cancelButtonColor: '#8592a3',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        });
    });

    // EDIT (HALAMAN UTAMA)
    const modalEditEl = document.getElementById('modalEditKategori');
    const formEdit = document.getElementById('formEditKategori');
    const inputEditKode = document.getElementById('edit_kode_barang');
    const inputEditNama = document.getElementById('edit_nama_barang');
    const selectEditPerusahaan = document.getElementById('edit_perusahaan');

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function () {
            inputEditKode.value = this.dataset.kode;
            inputEditNama.value = this.dataset.nama;
            if (selectEditPerusahaan && this.dataset.perusahaan_id) {
                selectEditPerusahaan.value = this.dataset.perusahaan_id;
            }
            formEdit.action = `/dashboard/aset/${this.dataset.id}`;
            new bootstrap.Modal(modalEditEl).show();
        });
    });

    // MODAL DETAIL KELOLA PER PERUSAHAAN
    const modalDetailEl = document.getElementById('modalDetailPerusahaan');
    const modalDetail = modalDetailEl ? new bootstrap.Modal(modalDetailEl) : null;
    let currentDetailNama = '';
    let currentDetailKode = '';

    document.querySelectorAll('.btn-detail-perusahaan').forEach(btn => {
        btn.addEventListener('click', function () {
            currentDetailNama = this.dataset.nama;
            currentDetailKode = this.dataset.kode;

            document.getElementById('judulBarang').innerText = currentDetailNama;
            document.getElementById('listPerusahaan').innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        Memuat data perusahaan...
                    </td>
                </tr>
            `;

            modalDetail.show();

            fetch(`/dashboard/aset/detail-perusahaan?nama_barang=${encodeURIComponent(currentDetailNama)}`)
                .then(res => res.json())
                .then(data => {
                    if (!data || data.length === 0) {
                        document.getElementById('listPerusahaan').innerHTML = `
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">Data tidak ditemukan.</td>
                            </tr>
                        `;
                        return;
                    }

                    let html = '';
                    data.forEach((item, index) => {
                        const tipeCount = item.data_asets_count !== undefined ? item.data_asets_count : 0;
                        html += `
                        <tr>
                            <td class="text-center fw-medium">${index + 1}</td>
                            <td class="text-center">
                                <span class="badge bg-label-primary">${item.kode_barang}</span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">${item.perusahaan ? item.perusahaan.nama_perusahaan : '-'}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-label-${tipeCount > 0 ? 'success' : 'secondary'}">
                                    <i class="bx bx-package me-1"></i> ${tipeCount} Tipe Aset
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <button type="button"
                                        class="btn btn-sm btn-icon btn-outline-secondary btn-edit-modal"
                                        data-id="${item.id}"
                                        data-kode="${item.kode_barang}"
                                        data-nama="${item.nama_barang}"
                                        data-perusahaan_id="${item.perusahaan_id}"
                                        title="Edit untuk perusahaan ini">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm btn-icon btn-outline-danger btn-delete-modal"
                                        data-id="${item.id}"
                                        data-nama="${item.nama_barang}"
                                        data-perusahaan="${item.perusahaan ? item.perusahaan.nama_perusahaan : ''}"
                                        data-tipe="${tipeCount}"
                                        title="Hapus untuk perusahaan ini">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        `;
                    });

                    document.getElementById('listPerusahaan').innerHTML = html;

                    // Edit dari modal
                    document.querySelectorAll('.btn-edit-modal').forEach(b => {
                        b.addEventListener('click', function () {
                            inputEditKode.value = this.dataset.kode;
                            inputEditNama.value = this.dataset.nama;
                            if (selectEditPerusahaan && this.dataset.perusahaan_id) {
                                selectEditPerusahaan.value = this.dataset.perusahaan_id;
                            }
                            formEdit.action = `/dashboard/aset/${this.dataset.id}`;
                            modalDetail.hide();
                            new bootstrap.Modal(modalEditEl).show();
                        });
                    });

                    // Delete dari modal
                    document.querySelectorAll('.btn-delete-modal').forEach(b => {
                        b.addEventListener('click', function () {
                            let id = this.dataset.id;
                            let perName = this.dataset.perusahaan;
                            let tipeCount = parseInt(this.dataset.tipe || 0);

                            modalDetail.hide();

                            let warningText = `Kategori "${currentDetailNama}" pada ${perName} akan dihapus.`;
                            if (tipeCount > 0) {
                                warningText += ` Perhatian: Terdapat ${tipeCount} data merek/tipe aset yang terikat ke kategori ini!`;
                            }

                            Swal.fire({
                                title: 'Yakin hapus kategori ini?',
                                text: warningText,
                                icon: tipeCount > 0 ? 'warning' : 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#696cff',
                                cancelButtonColor: '#8592a3',
                                confirmButtonText: 'Ya, hapus!',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    const form = document.createElement('form');
                                    form.method = 'POST';
                                    form.action = `/dashboard/aset/${id}`;
                                    form.innerHTML = `
                                        @csrf
                                        <input type="hidden" name="_method" value="DELETE">
                                    `;
                                    document.body.appendChild(form);
                                    form.submit();
                                } else {
                                    modalDetail.show();
                                }
                            });
                        });
                    });

                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('listPerusahaan').innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center py-3 text-danger">
                                Gagal memuat data perusahaan.
                            </td>
                        </tr>
                    `;
                });
        });
    });

    // Shortcut: Terapkan ke perusahaan lain
    const btnTambahKePerusahaanLain = document.getElementById('btnTambahKePerusahaanLain');
    if (btnTambahKePerusahaanLain) {
        btnTambahKePerusahaanLain.addEventListener('click', function () {
            if (modalDetail) {
                modalDetail.hide();
            }
            const inputNama = document.getElementById('tambah_nama_barang');
            if (inputNama) {
                inputNama.value = currentDetailNama;
            }
            const inputKode = document.getElementById('tambah_kode_barang');
            if (inputKode) {
                inputKode.value = currentDetailKode;
            }
            new bootstrap.Modal(document.getElementById('modalTambahKategori')).show();
        });
    }

});
</script>
@endsection