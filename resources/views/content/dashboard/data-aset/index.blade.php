@extends('layouts/contentNavbarLayout')

@section('title', 'Master Merek & Type')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HERO HEADER --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-primary me-3">
                            <span class="avatar-initial rounded">
                                <i class="bx bx-package fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">Master Merek & Type</h3>
                            <small class="text-muted">Kelola master merek, type, dan spesifikasi barang</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalFilter">
                            <i class="bx bx-filter-alt me-1"></i> Filter
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahAset">
                            <i class="bx bx-plus me-1"></i> Tambah Merek & Type
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
                        <a href="{{ route('data-aset.index') }}" class="btn btn-sm btn-outline-secondary">
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
                                <th>KATEGORI</th>
                                <th>MEREK</th>
                                <th>TYPE</th>
                                @if ($isGrouped)
                                    <th>DIGUNAKAN PADA PERUSAHAAN</th>
                                    <th width="170">TOTAL DISTRIBUSI</th>
                                @else
                                    <th>WARNA</th>
                                    <th>PERUSAHAAN</th>
                                @endif
                                <th width="130">ACTION</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($dataAsets as $index => $aset)
                                <tr>
                                    <td class="text-center fw-medium">
                                        {{ $dataAsets->firstItem() + $index }}
                                    </td>

                                    {{-- KATEGORI --}}
                                    <td>
                                        <span class="badge bg-label-info">
                                            {{ strtoupper($aset->nama_barang ?? ($aset->kategori->nama_barang ?? '-')) }}
                                        </span>
                                    </td>

                                    {{-- MEREK --}}
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs bg-label-primary me-2">
                                                <span class="avatar-initial rounded"><i class="bx bx-package fs-6"></i></span>
                                            </div>
                                            <span class="fw-bold text-dark">{{ strtoupper($aset->merek ?? '-') }}</span>
                                        </div>
                                    </td>

                                    {{-- TYPE --}}
                                    <td>
                                        <span class="fw-semibold">{{ strtoupper($aset->type ?? '-') }}</span>
                                    </td>

                                    @if ($isGrouped)
                                        {{-- DAFTAR PERUSAHAAN --}}
                                        <td>
                                            @if ($aset->daftar_perusahaan)
                                                @php
                                                    $compList = explode('||', $aset->daftar_perusahaan);
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

                                        {{-- TOTAL PERUSAHAAN --}}
                                        <td class="text-center">
                                            <span class="badge bg-label-primary rounded-pill px-3 py-2">
                                                <i class="bx bx-buildings me-1"></i> {{ $aset->total_perusahaan }} Perusahaan
                                            </span>
                                        </td>

                                        {{-- AKSI --}}
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-primary btn-sm btn-detail-data-aset"
                                                data-merek="{{ $aset->merek }}"
                                                data-type="{{ $aset->type }}"
                                                title="Kelola per perusahaan">
                                                <i class="bx bx-slider-alt me-1"></i> Kelola
                                            </button>
                                        </td>
                                    @else
                                        {{-- WARNA --}}
                                        <td>
                                            {{ $aset->warna ? strtoupper($aset->warna) : '-' }}
                                        </td>

                                        {{-- PERUSAHAAN --}}
                                        <td>
                                            <x-company-badge :perusahaan="$aset->perusahaan" />
                                        </td>

                                        {{-- ACTION --}}
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary btn-edit"
                                                    data-id="{{ $aset->id }}"
                                                    data-perusahaan="{{ $aset->perusahaan_id }}"
                                                    data-kategori="{{ $aset->kategori_id }}"
                                                    data-merek="{{ $aset->merek }}"
                                                    data-type="{{ $aset->type }}"
                                                    data-warna="{{ $aset->warna }}"
                                                    title="Edit Merek & Type">
                                                    <i class="bx bx-edit"></i>
                                                </button>

                                                <form id="delete-form-{{ $aset->id }}"
                                                    action="{{ route('data-aset.destroy', $aset->id) }}" method="POST"
                                                    style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>

                                                <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete"
                                                    data-id="{{ $aset->id }}"
                                                    title="Hapus Merek & Type">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isGrouped ? 7 : 7 }}" class="text-center py-4 text-muted">
                                        <i class="bx bx-folder-open fs-2 d-block mb-1"></i>
                                        Data merek & type tidak ditemukan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="mt-4">
                    {{ $dataAsets->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>

    {{-- ================= MODAL TAMBAH ================= --}}
    <div class="modal fade" id="modalTambahAset" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('data-aset.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-plus-circle me-1 text-primary"></i> Tambah Merek & Type
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            @if (auth()->user()->role === 'super_admin')
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Perusahaan <span class="text-danger">*</span></label>
                                    <select name="perusahaan_id" id="perusahaan_modal" class="form-select" required>
                                        <option value="">-- Pilih Perusahaan --</option>
                                        @foreach ($perusahaans as $p)
                                            <option value="{{ $p->id }}" {{ request('perusahaan_id') == $p->id ? 'selected' : '' }}>
                                                {{ $p->nama_perusahaan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="col-md-{{ auth()->user()->role === 'super_admin' ? '6' : '12' }} mb-3">
                                <label class="form-label fw-semibold">Kategori Aset <span class="text-danger">*</span></label>
                                <select name="kategori_id" id="kategori_id" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @if (auth()->user()->role !== 'super_admin')
                                        @foreach ($kategoris as $kategori)
                                            <option value="{{ $kategori->id }}">
                                                {{ strtoupper($kategori->nama_barang) }}
                                            </option>
                                        @endforeach
                                    @else
                                        @foreach ($kategoris as $kategori)
                                            <option value="{{ $kategori->id }}" data-perusahaan="{{ $kategori->perusahaan_id }}">
                                                {{ strtoupper($kategori->nama_barang) }} ({{ $kategori->perusahaan->nama_perusahaan ?? '-' }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Merek <span class="text-danger">*</span></label>
                                <input type="text" name="merek" id="tambah_merek" class="form-control text-uppercase"
                                    placeholder="Contoh: LENOVO, HP, DELL, EPSON" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                                <input type="text" name="type" id="tambah_type" class="form-control text-uppercase"
                                    placeholder="Contoh: THINKPAD T480, L3210" required>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold">Warna</label>
                                <input type="text" name="warna" id="tambah_warna" class="form-control text-uppercase"
                                    placeholder="Contoh: HITAM, SILVER (Opsional)">
                            </div>
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
    <div class="modal fade" id="modalEditAset" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="formEditAset" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-edit-alt me-1 text-warning"></i> Edit Merek & Type
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Kategori Aset <span class="text-danger">*</span></label>
                                <select id="edit_kategori" name="kategori_id" class="form-select" required>
                                    @foreach ($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}">
                                            {{ strtoupper($kategori->nama_barang) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Merek <span class="text-danger">*</span></label>
                                <input type="text" id="edit_merek" name="merek" class="form-control text-uppercase" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                                <input type="text" id="edit_type" name="type" class="form-control text-uppercase" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Warna</label>
                                <input type="text" id="edit_warna" name="warna" class="form-control text-uppercase">
                            </div>
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
                <form method="GET" action="{{ route('data-aset.index') }}">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-filter-alt me-2 text-primary"></i> Filter Data Merek & Type
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
                                <label class="form-label fw-semibold">Pencarian Merek & Type</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Cari merek / type / kategori..." value="{{ request('search') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('data-aset.index') }}" class="btn btn-secondary">
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

    {{-- ================= MODAL DETAIL KELOLA PERUSAHAAN ================= --}}
    <div class="modal fade" id="modalDetailDataAset" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white d-flex align-items-center">
                        <i class="bx bx-package me-2 fs-4"></i> Distribusi Aset: <span id="judulDataAset" class="fw-bold ms-2 badge bg-warning text-dark fs-6"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div class="text-muted small">
                            Daftar perusahaan yang memiliki master merek & type ini beserta data penerimaan aset terkait.
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
                                    <th>NAMA PERUSAHAAN</th>
                                    <th>KATEGORI</th>
                                    <th>WARNA</th>
                                    <th width="140">TRANSAKSI ASET</th>
                                    <th width="120">AKSI</th>
                                </tr>
                            </thead>
                            <tbody id="listDataAset">
                                <tr>
                                    <td colspan="6" class="text-center py-3 text-muted">Memuat data...</td>
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
        document.addEventListener('DOMContentLoaded', function() {

            // =====================================
            // DELETE (HALAMAN UTAMA)
            // =====================================
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    let id = this.dataset.id;
                    Swal.fire({
                        title: 'Yakin hapus data?',
                        text: 'Data tidak dapat dikembalikan.',
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

            // =====================================
            // MODAL EDIT (HALAMAN UTAMA)
            // =====================================
            const modalEditEl = document.getElementById('modalEditAset');
            const modalEdit = modalEditEl ? new bootstrap.Modal(modalEditEl) : null;
            const formEdit = document.getElementById('formEditAset');

            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    let id = this.dataset.id;
                    let perusahaanId = this.dataset.perusahaan;
                    let kategoriId = this.dataset.kategori;

                    document.getElementById('edit_merek').value = this.dataset.merek;
                    document.getElementById('edit_type').value = this.dataset.type;
                    document.getElementById('edit_warna').value = this.dataset.warna ?? '';

                    formEdit.action = `/dashboard/data-aset/${id}`;

                    fetch('/dashboard/get-kategori/' + perusahaanId)
                        .then(response => response.json())
                        .then(data => {
                            let html = '<option value="">Pilih Kategori</option>';
                            data.forEach(item => {
                                html += `<option value="${item.id}" ${item.id == kategoriId ? 'selected' : ''}>${item.nama_barang.toUpperCase()}</option>`;
                            });
                            document.getElementById('edit_kategori').innerHTML = html;
                            modalEdit.show();
                        })
                        .catch(error => {
                            console.error(error);
                            modalEdit.show();
                        });
                });
            });

            // =====================================
            // DETAIL KELOLA PER PERUSAHAAN (MODAL)
            // =====================================
            const modalDetailEl = document.getElementById('modalDetailDataAset');
            const modalDetail = modalDetailEl ? new bootstrap.Modal(modalDetailEl) : null;
            let currentDetailMerek = '';
            let currentDetailType = '';

            document.querySelectorAll('.btn-detail-data-aset').forEach(btn => {
                btn.addEventListener('click', function() {
                    currentDetailMerek = this.dataset.merek;
                    currentDetailType = this.dataset.type;

                    document.getElementById('judulDataAset').innerText = `${currentDetailMerek} - ${currentDetailType}`;
                    document.getElementById('listDataAset').innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                Memuat data perusahaan...
                            </td>
                        </tr>
                    `;

                    modalDetail.show();

                    fetch(`/dashboard/data-aset/detail-perusahaan?merek=${encodeURIComponent(currentDetailMerek)}&type=${encodeURIComponent(currentDetailType)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (!data || data.length === 0) {
                                document.getElementById('listDataAset').innerHTML = `
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-muted">Data tidak ditemukan.</td>
                                    </tr>
                                `;
                                return;
                            }

                            let html = '';
                            data.forEach((item, index) => {
                                const masukCount = item.masuks_count !== undefined ? item.masuks_count : 0;
                                html += `
                                    <tr>
                                        <td class="text-center fw-medium">${index + 1}</td>
                                        <td>
                                            <div class="fw-semibold text-dark">${item.perusahaan ? item.perusahaan.nama_perusahaan : '-'}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-info">${item.kategori ? item.kategori.nama_barang : '-'}</span>
                                        </td>
                                        <td>${item.warna ? item.warna : '-'}</td>
                                        <td class="text-center">
                                            <span class="badge bg-label-${masukCount > 0 ? 'success' : 'secondary'}">
                                                <i class="bx bx-log-in-circle me-1"></i> ${masukCount} Transaksi
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button type="button"
                                                    class="btn btn-sm btn-icon btn-outline-secondary btn-edit-modal"
                                                    data-id="${item.id}"
                                                    data-perusahaan="${item.perusahaan_id}"
                                                    data-kategori="${item.kategori_id}"
                                                    data-merek="${item.merek}"
                                                    data-type="${item.type}"
                                                    data-warna="${item.warna ?? ''}"
                                                    title="Edit untuk perusahaan ini">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-icon btn-outline-danger btn-delete-modal"
                                                    data-id="${item.id}"
                                                    data-perusahaan="${item.perusahaan ? item.perusahaan.nama_perusahaan : ''}"
                                                    data-masuk="${masukCount}"
                                                    title="Hapus untuk perusahaan ini">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            });

                            document.getElementById('listDataAset').innerHTML = html;

                            // Edit dari modal
                            document.querySelectorAll('.btn-edit-modal').forEach(b => {
                                b.addEventListener('click', function() {
                                    let id = this.dataset.id;
                                    let perusahaanId = this.dataset.perusahaan;
                                    let kategoriId = this.dataset.kategori;

                                    document.getElementById('edit_merek').value = this.dataset.merek;
                                    document.getElementById('edit_type').value = this.dataset.type;
                                    document.getElementById('edit_warna').value = this.dataset.warna ?? '';

                                    formEdit.action = `/dashboard/data-aset/${id}`;

                                    modalDetail.hide();

                                    fetch('/dashboard/get-kategori/' + perusahaanId)
                                        .then(response => response.json())
                                        .then(kategoris => {
                                            let htmlCat = '<option value="">Pilih Kategori</option>';
                                            kategoris.forEach(c => {
                                                htmlCat += `<option value="${c.id}" ${c.id == kategoriId ? 'selected' : ''}>${c.nama_barang.toUpperCase()}</option>`;
                                            });
                                            document.getElementById('edit_kategori').innerHTML = htmlCat;
                                            modalEdit.show();
                                        })
                                        .catch(err => {
                                            console.error(err);
                                            modalEdit.show();
                                        });
                                });
                            });

                            // Delete dari modal
                            document.querySelectorAll('.btn-delete-modal').forEach(b => {
                                b.addEventListener('click', function() {
                                    let id = this.dataset.id;
                                    let perName = this.dataset.perusahaan;
                                    let masukCount = parseInt(this.dataset.masuk || 0);

                                    modalDetail.hide();

                                    let warningText = `Data "${currentDetailMerek} - ${currentDetailType}" pada ${perName} akan dihapus.`;
                                    if (masukCount > 0) {
                                        warningText += ` Perhatian: Terdapat ${masukCount} transaksi penerimaan aset yang menggunakan tipe ini!`;
                                    }

                                    Swal.fire({
                                        title: 'Yakin hapus data ini?',
                                        text: warningText,
                                        icon: masukCount > 0 ? 'warning' : 'question',
                                        showCancelButton: true,
                                        confirmButtonColor: '#696cff',
                                        cancelButtonColor: '#8592a3',
                                        confirmButtonText: 'Ya, hapus!',
                                        cancelButtonText: 'Batal'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            const form = document.createElement('form');
                                            form.method = 'POST';
                                            form.action = `/dashboard/data-aset/${id}`;
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
                            document.getElementById('listDataAset').innerHTML = `
                                <tr>
                                    <td colspan="6" class="text-center py-3 text-danger">
                                        Gagal memuat data perusahaan.
                                    </td>
                                </tr>
                            `;
                        });
                });
            });

            // =====================================
            // SHORTCUT: TERAPKAN KE PERUSAHAAN LAIN
            // =====================================
            const btnTambahKePerusahaanLain = document.getElementById('btnTambahKePerusahaanLain');
            if (btnTambahKePerusahaanLain) {
                btnTambahKePerusahaanLain.addEventListener('click', function() {
                    if (modalDetail) {
                        modalDetail.hide();
                    }
                    const inputMerek = document.getElementById('tambah_merek');
                    if (inputMerek) {
                        inputMerek.value = currentDetailMerek;
                    }
                    const inputType = document.getElementById('tambah_type');
                    if (inputType) {
                        inputType.value = currentDetailType;
                    }
                    new bootstrap.Modal(document.getElementById('modalTambahAset')).show();
                });
            }

            // =====================================
            // LOAD KATEGORI SAAT TAMBAH DATA
            // =====================================
            const perusahaanTambah = document.getElementById('perusahaan_modal');
            const kategoriTambah = document.getElementById('kategori_id');

            if (perusahaanTambah && kategoriTambah) {
                perusahaanTambah.addEventListener('change', function() {
                    let perusahaanId = this.value;
                    if (!perusahaanId) {
                        kategoriTambah.innerHTML = '<option value="">-- Pilih Kategori --</option>';
                        return;
                    }

                    kategoriTambah.innerHTML = '<option value="">Loading...</option>';

                    fetch('/dashboard/get-kategori/' + perusahaanId)
                        .then(response => response.json())
                        .then(data => {
                            let html = '<option value="">-- Pilih Kategori --</option>';
                            data.forEach(item => {
                                html += `<option value="${item.id}">${item.nama_barang.toUpperCase()}</option>`;
                            });
                            kategoriTambah.innerHTML = html;
                        })
                        .catch(error => {
                            console.error(error);
                            kategoriTambah.innerHTML = '<option value="">Gagal memuat kategori</option>';
                        });
                });
            }

        });
    </script>
@endsection
