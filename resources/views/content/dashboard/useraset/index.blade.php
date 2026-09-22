@extends('layouts/contentNavbarLayout')

@section('title', 'Data Karyawan')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HERO HEADER --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-primary me-3">
                            <span class="avatar-initial rounded">
                                <i class="bx bx-user-check fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">Data Karyawan</h3>
                            <small class="text-muted">Kelola master data karyawan dan divisi penerima aset</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalFilter">
                            <i class="bx bx-filter-alt me-1"></i> Filter
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKaryawan">
                            <i class="bx bx-user-plus me-1"></i> Tambah Karyawan
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

        <div class="card border-0 shadow-sm">
            <div class="card-body">

                @if(request()->filled('search') || request()->filled('perusahaan_id'))
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-label-primary px-3 py-2">
                            <i class="bx bx-filter-alt me-1"></i> Filter Aktif
                        </span>
                        <a href="{{ route('useraset.index') }}" class="btn btn-sm btn-outline-secondary">
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
                                <th width="120">UID</th>
                                <th>NAMA KARYAWAN</th>
                                <th>JABATAN</th>
                                <th>DIVISI</th>
                                @if ($isGrouped)
                                    <th>DIGUNAKAN PADA PERUSAHAAN</th>
                                    <th width="170">TOTAL DISTRIBUSI</th>
                                @else
                                    <th>PERUSAHAAN</th>
                                @endif
                                <th width="130">ACTION</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($karyawans as $index => $karyawan)
                                <tr>
                                    <td class="text-center fw-medium">
                                        {{ $karyawans->firstItem() + $index }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-primary fw-bold">
                                            {{ $karyawan->kode_karyawan }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs bg-label-primary me-2">
                                                <span class="avatar-initial rounded"><i class="bx bx-user fs-6"></i></span>
                                            </div>
                                            <span class="fw-semibold text-dark">{{ $karyawan->nama_karyawan }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $karyawan->jabatan ?: '-' }}</td>
                                    <td>{{ $karyawan->divisi ?: '-' }}</td>

                                    @if ($isGrouped)
                                        <td>
                                            @if ($karyawan->daftar_perusahaan)
                                                @php
                                                    $compList = explode('||', $karyawan->daftar_perusahaan);
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
                                                <i class="bx bx-buildings me-1"></i> {{ $karyawan->total_perusahaan }} Perusahaan
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-primary btn-sm btn-detail-karyawan"
                                                data-kode="{{ $karyawan->kode_karyawan }}"
                                                data-nama="{{ $karyawan->nama_karyawan }}"
                                                title="Kelola karyawan per perusahaan">
                                                <i class="bx bx-slider-alt me-1"></i> Kelola
                                            </button>
                                        </td>
                                    @else
                                        <td>
                                            <x-company-badge :perusahaan="$karyawan->perusahaan" />
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary btn-edit"
                                                    data-id="{{ $karyawan->id }}"
                                                    data-kode="{{ $karyawan->kode_karyawan }}"
                                                    data-nama="{{ $karyawan->nama_karyawan }}"
                                                    data-jabatan="{{ $karyawan->jabatan }}"
                                                    data-divisi="{{ $karyawan->divisi }}"
                                                    data-perusahaan_id="{{ $karyawan->id_perusahaan }}"
                                                    title="Edit Karyawan">
                                                    <i class="bx bx-edit"></i>
                                                </button>

                                                <form id="delete-form-{{ $karyawan->id }}"
                                                    action="{{ route('useraset.destroy', $karyawan->id) }}" method="POST"
                                                    style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>

                                                <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete"
                                                    data-id="{{ $karyawan->id }}"
                                                    title="Hapus Karyawan">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isGrouped ? 8 : 7 }}" class="text-center py-4 text-muted">
                                        <i class="bx bx-folder-open fs-2 d-block mb-1"></i>
                                        Data karyawan tidak ditemukan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="mt-4">
                    {{ $karyawans->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>

    {{-- ================= MODAL TAMBAH ================= --}}
    <div class="modal fade" id="modalTambahKaryawan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('useraset.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-user-plus me-1 text-primary"></i> Tambah Karyawan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        @if (auth()->user()->role === 'super_admin')
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Perusahaan <span class="text-danger">*</span></label>
                                <select name="id_perusahaan" id="tambah_perusahaan" class="form-select" required>
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
                            <label class="form-label fw-semibold">UID / Kode Karyawan <span class="text-danger">*</span></label>
                            <input type="text" name="kode_karyawan" id="tambah_kode_karyawan" class="form-control text-uppercase"
                                placeholder="Contoh: EMP001, NIK..." required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Karyawan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_karyawan" id="tambah_nama_karyawan" class="form-control text-uppercase"
                                placeholder="Nama lengkap karyawan" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jabatan</label>
                            <input type="text" name="jabatan" id="tambah_jabatan" class="form-control text-uppercase"
                                placeholder="Contoh: STAFF IT, MANAGER FINANCE">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Divisi</label>
                            <input type="text" name="divisi" id="tambah_divisi" class="form-control text-uppercase"
                                placeholder="Contoh: IT, HRD, FINANCE, OPERASIONAL">
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
    <div class="modal fade" id="modalEditKaryawan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="formEditKaryawan" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-edit-alt me-1 text-warning"></i> Edit Karyawan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        @if (auth()->user()->role === 'super_admin')
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Perusahaan <span class="text-danger">*</span></label>
                                <select name="id_perusahaan" id="edit_perusahaan" class="form-select" required>
                                    @foreach ($perusahaans as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-semibold">UID / Kode Karyawan <span class="text-danger">*</span></label>
                            <input type="text" id="edit_kode" name="kode_karyawan" class="form-control text-uppercase" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Karyawan <span class="text-danger">*</span></label>
                            <input type="text" id="edit_nama" name="nama_karyawan" class="form-control text-uppercase" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jabatan</label>
                            <input type="text" id="edit_jabatan" name="jabatan" class="form-control text-uppercase">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Divisi</label>
                            <input type="text" id="edit_divisi" name="divisi" class="form-control text-uppercase">
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
                <form method="GET" action="{{ route('useraset.index') }}">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-filter-alt me-2 text-primary"></i> Filter Data Karyawan
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
                                <label class="form-label fw-semibold">Pencarian Karyawan</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Cari nama / kode karyawan / jabatan..." value="{{ request('search') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('useraset.index') }}" class="btn btn-secondary">
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
    <div class="modal fade" id="modalDetailKaryawan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white d-flex align-items-center">
                        <i class="bx bx-user-check me-2 fs-4"></i> Distribusi Karyawan: <span id="judulKaryawan" class="fw-bold ms-2 badge bg-warning text-dark fs-6"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div class="text-muted small">
                            Daftar perusahaan tempat karyawan ini terdaftar beserta jumlah aset yang dialokasikan.
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
                                    <th>JABATAN</th>
                                    <th>DIVISI</th>
                                    <th width="140">ASET DIGUNAKAN</th>
                                    <th width="120">AKSI</th>
                                </tr>
                            </thead>
                            <tbody id="listKaryawan">
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
                        title: 'Yakin hapus karyawan ini?',
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

            // =====================================
            // EDIT (HALAMAN UTAMA)
            // =====================================
            const modalEditEl = document.getElementById('modalEditKaryawan');
            const modalEdit = modalEditEl ? new bootstrap.Modal(modalEditEl) : null;
            const formEdit = document.getElementById('formEditKaryawan');

            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    let id = this.dataset.id;
                    document.getElementById('edit_kode').value = this.dataset.kode;
                    document.getElementById('edit_nama').value = this.dataset.nama;
                    document.getElementById('edit_jabatan').value = this.dataset.jabatan ?? '';
                    document.getElementById('edit_divisi').value = this.dataset.divisi ?? '';

                    @if (auth()->user()->role === 'super_admin')
                        if (document.getElementById('edit_perusahaan') && this.dataset.perusahaan_id) {
                            document.getElementById('edit_perusahaan').value = this.dataset.perusahaan_id;
                        }
                    @endif

                    formEdit.action = `/dashboard/useraset/${id}`;
                    modalEdit.show();
                });
            });

            // =====================================
            // DETAIL KELOLA PER PERUSAHAAN (MODAL)
            // =====================================
            const modalDetailEl = document.getElementById('modalDetailKaryawan');
            const modalDetail = modalDetailEl ? new bootstrap.Modal(modalDetailEl) : null;
            let currentDetailKode = '';
            let currentDetailNama = '';
            let currentDetailJabatan = '';
            let currentDetailDivisi = '';

            document.querySelectorAll('.btn-detail-karyawan').forEach(btn => {
                btn.addEventListener('click', function() {
                    currentDetailKode = this.dataset.kode;
                    currentDetailNama = this.dataset.nama;

                    document.getElementById('judulKaryawan').innerText = `${currentDetailNama} (${currentDetailKode})`;
                    document.getElementById('listKaryawan').innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                Memuat data perusahaan...
                            </td>
                        </tr>
                    `;

                    modalDetail.show();

                    fetch(`/dashboard/useraset/detail-perusahaan?kode_karyawan=${encodeURIComponent(currentDetailKode)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (!data || data.length === 0) {
                                document.getElementById('listKaryawan').innerHTML = `
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-muted">Data tidak ditemukan.</td>
                                    </tr>
                                `;
                                return;
                            }

                            currentDetailJabatan = data[0].jabatan || '';
                            currentDetailDivisi = data[0].divisi || '';

                            let html = '';
                            data.forEach((item, index) => {
                                const mapingCount = item.mapings_count !== undefined ? item.mapings_count : 0;
                                html += `
                                    <tr>
                                        <td class="text-center fw-medium">${index + 1}</td>
                                        <td>
                                            <div class="fw-semibold text-dark">${item.perusahaan ? item.perusahaan.nama_perusahaan : '-'}</div>
                                        </td>
                                        <td>${item.jabatan || '-'}</td>
                                        <td>${item.divisi || '-'}</td>
                                        <td class="text-center">
                                            <span class="badge bg-label-${mapingCount > 0 ? 'success' : 'secondary'}">
                                                <i class="bx bx-laptop me-1"></i> ${mapingCount} Aset
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button type="button"
                                                    class="btn btn-sm btn-icon btn-outline-secondary btn-edit-modal"
                                                    data-id="${item.id}"
                                                    data-kode="${item.kode_karyawan}"
                                                    data-nama="${item.nama_karyawan}"
                                                    data-jabatan="${item.jabatan ?? ''}"
                                                    data-divisi="${item.divisi ?? ''}"
                                                    data-perusahaan_id="${item.id_perusahaan}"
                                                    title="Edit untuk perusahaan ini">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-icon btn-outline-danger btn-delete-modal"
                                                    data-id="${item.id}"
                                                    data-nama="${item.nama_karyawan}"
                                                    data-perusahaan="${item.perusahaan ? item.perusahaan.nama_perusahaan : ''}"
                                                    data-maping="${mapingCount}"
                                                    title="Hapus untuk perusahaan ini">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            });

                            document.getElementById('listKaryawan').innerHTML = html;

                            // Edit dari modal
                            document.querySelectorAll('.btn-edit-modal').forEach(b => {
                                b.addEventListener('click', function() {
                                    document.getElementById('edit_kode').value = this.dataset.kode;
                                    document.getElementById('edit_nama').value = this.dataset.nama;
                                    document.getElementById('edit_jabatan').value = this.dataset.jabatan;
                                    document.getElementById('edit_divisi').value = this.dataset.divisi;

                                    @if (auth()->user()->role === 'super_admin')
                                        if (document.getElementById('edit_perusahaan')) {
                                            document.getElementById('edit_perusahaan').value = this.dataset.perusahaan_id;
                                        }
                                    @endif

                                    formEdit.action = `/dashboard/useraset/${this.dataset.id}`;
                                    modalDetail.hide();
                                    modalEdit.show();
                                });
                            });

                            // Delete dari modal
                            document.querySelectorAll('.btn-delete-modal').forEach(b => {
                                b.addEventListener('click', function() {
                                    let id = this.dataset.id;
                                    let perName = this.dataset.perusahaan;
                                    let mapingCount = parseInt(this.dataset.maping || 0);

                                    modalDetail.hide();

                                    let warningText = `Karyawan "${currentDetailNama}" pada ${perName} akan dihapus.`;
                                    if (mapingCount > 0) {
                                        warningText += ` Perhatian: Karyawan ini sedang dialokasikan ${mapingCount} unit aset!`;
                                    }

                                    Swal.fire({
                                        title: 'Yakin hapus karyawan ini?',
                                        text: warningText,
                                        icon: mapingCount > 0 ? 'warning' : 'question',
                                        showCancelButton: true,
                                        confirmButtonColor: '#696cff',
                                        cancelButtonColor: '#8592a3',
                                        confirmButtonText: 'Ya, hapus!',
                                        cancelButtonText: 'Batal'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            const form = document.createElement('form');
                                            form.method = 'POST';
                                            form.action = `/dashboard/useraset/${id}`;
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
                            document.getElementById('listKaryawan').innerHTML = `
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
                    const inputKode = document.getElementById('tambah_kode_karyawan');
                    if (inputKode) {
                        inputKode.value = currentDetailKode;
                    }
                    const inputNama = document.getElementById('tambah_nama_karyawan');
                    if (inputNama) {
                        inputNama.value = currentDetailNama;
                    }
                    const inputJabatan = document.getElementById('tambah_jabatan');
                    if (inputJabatan) {
                        inputJabatan.value = currentDetailJabatan;
                    }
                    const inputDivisi = document.getElementById('tambah_divisi');
                    if (inputDivisi) {
                        inputDivisi.value = currentDetailDivisi;
                    }
                    new bootstrap.Modal(document.getElementById('modalTambahKaryawan')).show();
                });
            }

        });
    </script>
@endsection
