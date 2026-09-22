@extends('layouts/contentNavbarLayout')

@section('title', 'Lokasi Penempatan')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HERO HEADER --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-primary me-3">
                            <span class="avatar-initial rounded">
                                <i class="bx bx-map-pin fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">Lokasi Penempatan</h3>
                            <small class="text-muted">Kelola master lokasi gedung, ruangan, dan cabang penempatan aset</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalFilter">
                            <i class="bx bx-filter-alt me-1"></i> Filter
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahLokasi">
                            <i class="bx bx-plus me-1"></i> Tambah Lokasi
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
                        <a href="{{ route('lokasi.index') }}" class="btn btn-sm btn-outline-secondary">
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
                                <th>NAMA LOKASI</th>
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
                            @forelse ($lokasis as $index => $lokasi)
                                <tr>
                                    <td class="text-center fw-medium">
                                        {{ $lokasis->firstItem() + $index }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs bg-label-primary me-2">
                                                <span class="avatar-initial rounded"><i class="bx bx-map-pin fs-6"></i></span>
                                            </div>
                                            <span class="fw-semibold text-dark">{{ $lokasi->nama_lokasi }}</span>
                                        </div>
                                    </td>

                                    @if ($isGrouped)
                                        <td>
                                            @if ($lokasi->daftar_perusahaan)
                                                @php
                                                    $compList = explode('||', $lokasi->daftar_perusahaan);
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
                                                <i class="bx bx-buildings me-1"></i> {{ $lokasi->total_perusahaan }} Perusahaan
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-primary btn-sm btn-detail-lokasi"
                                                data-nama="{{ $lokasi->nama_lokasi }}"
                                                title="Kelola lokasi per perusahaan">
                                                <i class="bx bx-slider-alt me-1"></i> Kelola
                                            </button>
                                        </td>
                                    @else
                                        <td>
                                            <x-company-badge :perusahaan="$lokasi->perusahaan" />
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                {{-- EDIT --}}
                                                <button class="btn btn-sm btn-icon btn-outline-secondary btn-edit"
                                                    data-id="{{ $lokasi->id }}"
                                                    data-nama="{{ $lokasi->nama_lokasi }}"
                                                    data-perusahaan_id="{{ $lokasi->id_perusahaan }}"
                                                    title="Edit Lokasi">
                                                    <i class="bx bx-edit"></i>
                                                </button>

                                                {{-- DELETE --}}
                                                <form id="delete-form-{{ $lokasi->id }}"
                                                    action="{{ route('lokasi.destroy', $lokasi->id) }}" method="POST"
                                                    style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>

                                                <button class="btn btn-sm btn-icon btn-outline-danger btn-delete"
                                                    data-id="{{ $lokasi->id }}"
                                                    title="Hapus Lokasi">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isGrouped ? 5 : 4 }}" class="text-center py-4 text-muted">
                                        <i class="bx bx-folder-open fs-2 d-block mb-1"></i>
                                        Data lokasi tidak ditemukan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="mt-4">
                    {{ $lokasis->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH LOKASI --}}
    <div class="modal fade" id="modalTambahLokasi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('lokasi.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-plus-circle me-1 text-primary"></i> Tambah Lokasi
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        @if (auth()->user()->role === 'super_admin')
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Perusahaan <span class="text-danger">*</span></label>
                                <select name="id_perusahaan" id="perusahaanSelect" class="form-select" required>
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
                            <label class="form-label fw-semibold">Nama Lokasi <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lokasi" id="tambah_nama_lokasi" class="form-control text-uppercase"
                                placeholder="Contoh: RUANG SERVER, GUDANG, LANTAI 2" required>
                            <div class="form-text">Nama lokasi akan otomatis disimpan dalam huruf kapital.</div>
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

    {{-- MODAL EDIT LOKASI --}}
    <div class="modal fade" id="modalEditLokasi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="formEditLokasi" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-edit-alt me-1 text-warning"></i> Edit Lokasi
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
                            <label class="form-label fw-semibold">Nama Lokasi <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lokasi" id="edit_nama" class="form-control text-uppercase" required>
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
                <form method="GET" action="{{ route('lokasi.index') }}">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-filter-alt me-2 text-primary"></i> Filter Data Lokasi
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
                                <label class="form-label fw-semibold">Pencarian Lokasi</label>
                                <input type="text" name="search" class="form-control" placeholder="Cari nama lokasi..."
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('lokasi.index') }}" class="btn btn-secondary">
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

    {{-- MODAL DETAIL KELOLA LOKASI PER PERUSAHAAN --}}
    <div class="modal fade" id="modalDetailLokasi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white d-flex align-items-center">
                        <i class="bx bx-map-pin me-2 fs-4"></i> Distribusi Lokasi: <span id="judulLokasi" class="fw-bold ms-2 badge bg-warning text-dark fs-6"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div class="text-muted small">
                            Daftar perusahaan yang menggunakan nama lokasi ini beserta jumlah aset terkait.
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
                                    <th width="140">ASET TERKAIT</th>
                                    <th width="120">AKSI</th>
                                </tr>
                            </thead>
                            <tbody id="listLokasi">
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">
                                        Memuat data...
                                    </td>
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

            // ======================================
            // DELETE (HALAMAN UTAMA)
            // ======================================
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.onclick = function() {
                    let id = this.dataset.id;

                    Swal.fire({
                        title: 'Yakin hapus lokasi ini?',
                        text: "Data lokasi yang dihapus tidak bisa dikembalikan!",
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
                };
            });

            // ======================================
            // EDIT (HALAMAN UTAMA)
            // ======================================
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.onclick = function() {
                    let id = this.dataset.id;
                    document.getElementById('edit_nama').value = this.dataset.nama;

                    @if (auth()->user()->role === 'super_admin')
                        if (this.dataset.perusahaan_id && document.getElementById('edit_perusahaan')) {
                            document.getElementById('edit_perusahaan').value = this.dataset.perusahaan_id;
                        }
                    @endif

                    document.getElementById('formEditLokasi').action = `/dashboard/lokasi/${id}`;
                    new bootstrap.Modal(document.getElementById('modalEditLokasi')).show();
                };
            });

            // ======================================
            // DETAIL & KELOLA PERUSAHAAN (MODAL)
            // ======================================
            const modalDetailEl = document.getElementById('modalDetailLokasi');
            const modalDetail = modalDetailEl ? new bootstrap.Modal(modalDetailEl) : null;
            let currentActiveNamaLokasi = '';

            document.querySelectorAll('.btn-detail-lokasi').forEach(btn => {
                btn.addEventListener('click', function () {
                    currentActiveNamaLokasi = this.dataset.nama;
                    document.getElementById('judulLokasi').innerText = currentActiveNamaLokasi;

                    document.getElementById('listLokasi').innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                Memuat data perusahaan...
                            </td>
                        </tr>
                    `;

                    modalDetail.show();

                    fetch(`/dashboard/lokasi/detail-perusahaan?nama_lokasi=${encodeURIComponent(currentActiveNamaLokasi)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (!data || data.length === 0) {
                                document.getElementById('listLokasi').innerHTML = `
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">
                                            Data tidak ditemukan.
                                        </td>
                                    </tr>
                                `;
                                return;
                            }

                            let html = '';
                            data.forEach((item, index) => {
                                const asetCount = item.maping_count !== undefined ? item.maping_count : 0;
                                html += `
                                    <tr>
                                        <td class="text-center fw-medium">${index + 1}</td>
                                        <td>
                                            <div class="fw-semibold text-dark">${item.perusahaan ? item.perusahaan.nama_perusahaan : '-'}</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-${asetCount > 0 ? 'success' : 'secondary'}">
                                                <i class="bx bx-laptop me-1"></i> ${asetCount} Aset
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button type="button"
                                                    class="btn btn-sm btn-icon btn-outline-secondary btn-edit-modal"
                                                    data-id="${item.id}"
                                                    data-nama="${item.nama_lokasi}"
                                                    data-perusahaan_id="${item.id_perusahaan}"
                                                    title="Edit untuk perusahaan ini">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-icon btn-outline-danger btn-delete-modal"
                                                    data-id="${item.id}"
                                                    data-nama="${item.nama_lokasi}"
                                                    data-perusahaan="${item.perusahaan ? item.perusahaan.nama_perusahaan : ''}"
                                                    data-aset="${asetCount}"
                                                    title="Hapus untuk perusahaan ini">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            });

                            document.getElementById('listLokasi').innerHTML = html;

                            // Bind Edit Modal
                            document.querySelectorAll('.btn-edit-modal').forEach(b => {
                                b.addEventListener('click', function () {
                                    document.getElementById('edit_nama').value = this.dataset.nama;

                                    @if(auth()->user()->role === 'super_admin')
                                        if (document.getElementById('edit_perusahaan')) {
                                            document.getElementById('edit_perusahaan').value = this.dataset.perusahaan_id;
                                        }
                                    @endif

                                    document.getElementById('formEditLokasi').action = `/dashboard/lokasi/${this.dataset.id}`;
                                    modalDetail.hide();
                                    new bootstrap.Modal(document.getElementById('modalEditLokasi')).show();
                                });
                            });

                            // Bind Delete Modal
                            document.querySelectorAll('.btn-delete-modal').forEach(b => {
                                b.addEventListener('click', function () {
                                    let id = this.dataset.id;
                                    let perName = this.dataset.perusahaan;
                                    let asetCount = parseInt(this.dataset.aset || 0);

                                    modalDetail.hide();

                                    let warningText = `Lokasi "${currentActiveNamaLokasi}" pada ${perName} akan dihapus.`;
                                    if (asetCount > 0) {
                                        warningText += ` Perhatian: Terdapat ${asetCount} data aset yang tertaut ke lokasi ini!`;
                                    }

                                    Swal.fire({
                                        title: 'Yakin hapus lokasi ini?',
                                        text: warningText,
                                        icon: asetCount > 0 ? 'warning' : 'question',
                                        showCancelButton: true,
                                        confirmButtonColor: '#696cff',
                                        cancelButtonColor: '#8592a3',
                                        confirmButtonText: 'Ya, hapus!',
                                        cancelButtonText: 'Batal'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            const form = document.createElement('form');
                                            form.method = 'POST';
                                            form.action = `/dashboard/lokasi/${id}`;
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
                            document.getElementById('listLokasi').innerHTML = `
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-danger">
                                        Gagal memuat detail perusahaan. Silakan coba kembali.
                                    </td>
                                </tr>
                            `;
                        });
                });
            });

            // ======================================
            // SHORTCUT: TERAPKAN KE PERUSAHAAN LAIN
            // ======================================
            const btnTambahKePerusahaanLain = document.getElementById('btnTambahKePerusahaanLain');
            if (btnTambahKePerusahaanLain) {
                btnTambahKePerusahaanLain.addEventListener('click', function () {
                    if (modalDetail) {
                        modalDetail.hide();
                    }
                    const inputNama = document.getElementById('tambah_nama_lokasi');
                    if (inputNama) {
                        inputNama.value = currentActiveNamaLokasi;
                    }
                    const selectPerusahaan = document.getElementById('perusahaanSelect');
                    if (selectPerusahaan) {
                        selectPerusahaan.value = '';
                    }
                    new bootstrap.Modal(document.getElementById('modalTambahLokasi')).show();
                });
            }

        });
    </script>
@endsection
