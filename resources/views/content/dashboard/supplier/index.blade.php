@extends('layouts/contentNavbarLayout')

@section('title', 'Supplier / Vendor')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HERO HEADER --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-primary me-3">
                            <span class="avatar-initial rounded">
                                <i class="bx bx-store-alt fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">Supplier / Vendor</h3>
                            <small class="text-muted">Kelola data vendor dan supplier penyedia aset</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalFilter">
                            <i class="bx bx-filter-alt me-1"></i> Filter
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSupplier">
                            <i class="bx bx-plus me-1"></i> Tambah Supplier
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
                        <a href="{{ route('supplier.index') }}" class="btn btn-sm btn-outline-secondary">
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
                                <th>NAMA SUPPLIER</th>
                                <th width="140">TELEPON / HP</th>
                                <th>ALAMAT</th>
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
                            @forelse($suppliers as $index => $supplier)
                                <tr>
                                    <td class="text-center fw-medium">
                                        {{ $suppliers->firstItem() + $index }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs bg-label-primary me-2">
                                                <span class="avatar-initial rounded"><i class="bx bx-store-alt fs-6"></i></span>
                                            </div>
                                            <span class="fw-bold text-dark">{{ strtoupper($supplier->nama_supplier) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $supplier->telepon ?: '-' }}
                                    </td>
                                    <td>
                                        {{ $supplier->alamat ?: '-' }}
                                    </td>

                                    @if ($isGrouped)
                                        <td>
                                            @if ($supplier->daftar_perusahaan)
                                                @php
                                                    $compList = explode('||', $supplier->daftar_perusahaan);
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
                                                <i class="bx bx-buildings me-1"></i> {{ $supplier->total_perusahaan }} Perusahaan
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-primary btn-sm btn-detail-supplier"
                                                data-nama="{{ $supplier->nama_supplier }}"
                                                title="Kelola supplier per perusahaan">
                                                <i class="bx bx-slider-alt me-1"></i> Kelola
                                            </button>
                                        </td>
                                    @else
                                        <td>
                                            <x-company-badge :perusahaan="$supplier->perusahaan" />
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary btn-edit"
                                                    data-id="{{ $supplier->id }}"
                                                    data-nama="{{ $supplier->nama_supplier }}"
                                                    data-telepon="{{ $supplier->telepon }}"
                                                    data-alamat="{{ $supplier->alamat }}"
                                                    data-perusahaan_id="{{ $supplier->perusahaan_id }}"
                                                    title="Edit Supplier">
                                                    <i class="bx bx-edit"></i>
                                                </button>

                                                <form id="delete-form-{{ $supplier->id }}"
                                                    action="{{ route('supplier.destroy', $supplier->id) }}" method="POST"
                                                    style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>

                                                <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete"
                                                    data-id="{{ $supplier->id }}"
                                                    title="Hapus Supplier">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isGrouped ? 7 : 6 }}" class="text-center py-4 text-muted">
                                        <i class="bx bx-folder-open fs-2 d-block mb-1"></i>
                                        Data supplier / vendor tidak ditemukan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="mt-4">
                    {{ $suppliers->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>

    {{-- ================= MODAL TAMBAH ================= --}}
    <div class="modal fade" id="modalTambahSupplier" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('supplier.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-plus-circle me-1 text-primary"></i> Tambah Supplier / Vendor
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
                            <label class="form-label fw-semibold">Nama Supplier <span class="text-danger">*</span></label>
                            <input type="text" name="nama_supplier" id="tambah_nama_supplier" class="form-control text-uppercase"
                                placeholder="Contoh: PT ANEKA KOMPUTER" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">No HP / Telepon</label>
                            <input type="text" name="telepon" id="tambah_telepon" class="form-control"
                                placeholder="Contoh: 08123456789">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea name="alamat" id="tambah_alamat" class="form-control" rows="3"
                                placeholder="Alamat kantor / toko"></textarea>
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
    <div class="modal fade" id="modalEditSupplier" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="formEditSupplier" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-edit-alt me-1 text-warning"></i> Edit Supplier / Vendor
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
                            <label class="form-label fw-semibold">Nama Supplier <span class="text-danger">*</span></label>
                            <input type="text" id="edit_nama" name="nama_supplier" class="form-control text-uppercase" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">No HP / Telepon</label>
                            <input type="text" id="edit_telepon" name="telepon" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea id="edit_alamat" name="alamat" class="form-control" rows="3"></textarea>
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
                <form method="GET" action="{{ route('supplier.index') }}">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-filter-alt me-2 text-primary"></i> Filter Data Supplier
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
                                <label class="form-label fw-semibold">Pencarian Supplier</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Cari nama, telepon, alamat supplier..." value="{{ request('search') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('supplier.index') }}" class="btn btn-secondary">
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
    <div class="modal fade" id="modalDetailSupplier" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white d-flex align-items-center">
                        <i class="bx bx-store-alt me-2 fs-4"></i> Distribusi Supplier: <span id="judulSupplier" class="fw-bold ms-2 badge bg-warning text-dark fs-6"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div class="text-muted small">
                            Daftar perusahaan yang bermitra dengan supplier ini beserta riwayat transaksi penerimaan aset.
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
                                    <th>TELEPON</th>
                                    <th width="150">TRANSAKSI ASET</th>
                                    <th width="120">AKSI</th>
                                </tr>
                            </thead>
                            <tbody id="listSupplier">
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
        document.addEventListener('DOMContentLoaded', function() {

            // =====================================
            // DELETE (HALAMAN UTAMA)
            // =====================================
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    let id = this.dataset.id;
                    Swal.fire({
                        title: 'Yakin hapus supplier ini?',
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
            const modalEditEl = document.getElementById('modalEditSupplier');
            const modalEdit = modalEditEl ? new bootstrap.Modal(modalEditEl) : null;
            const formEdit = document.getElementById('formEditSupplier');

            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    let id = this.dataset.id;
                    document.getElementById('edit_nama').value = this.dataset.nama;
                    document.getElementById('edit_telepon').value = this.dataset.telepon ?? '';
                    document.getElementById('edit_alamat').value = this.dataset.alamat ?? '';

                    @if (auth()->user()->role === 'super_admin')
                        if (document.getElementById('edit_perusahaan') && this.dataset.perusahaan_id) {
                            document.getElementById('edit_perusahaan').value = this.dataset.perusahaan_id;
                        }
                    @endif

                    formEdit.action = `/dashboard/supplier/${id}`;
                    modalEdit.show();
                });
            });

            // =====================================
            // DETAIL KELOLA PER PERUSAHAAN (MODAL)
            // =====================================
            const modalDetailEl = document.getElementById('modalDetailSupplier');
            const modalDetail = modalDetailEl ? new bootstrap.Modal(modalDetailEl) : null;
            let currentDetailNama = '';
            let currentDetailTelepon = '';
            let currentDetailAlamat = '';

            document.querySelectorAll('.btn-detail-supplier').forEach(btn => {
                btn.addEventListener('click', function() {
                    currentDetailNama = this.dataset.nama;

                    document.getElementById('judulSupplier').innerText = currentDetailNama;
                    document.getElementById('listSupplier').innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                Memuat data perusahaan...
                            </td>
                        </tr>
                    `;

                    modalDetail.show();

                    fetch(`/dashboard/supplier/detail-perusahaan?nama_supplier=${encodeURIComponent(currentDetailNama)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (!data || data.length === 0) {
                                document.getElementById('listSupplier').innerHTML = `
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">Data tidak ditemukan.</td>
                                    </tr>
                                `;
                                return;
                            }

                            currentDetailTelepon = data[0].telepon || '';
                            currentDetailAlamat = data[0].alamat || '';

                            let html = '';
                            data.forEach((item, index) => {
                                const masukCount = item.masuks_count !== undefined ? item.masuks_count : 0;
                                html += `
                                    <tr>
                                        <td class="text-center fw-medium">${index + 1}</td>
                                        <td>
                                            <div class="fw-semibold text-dark">${item.perusahaan ? item.perusahaan.nama_perusahaan : '-'}</div>
                                        </td>
                                        <td>${item.telepon || '-'}</td>
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
                                                    data-nama="${item.nama_supplier}"
                                                    data-telepon="${item.telepon ?? ''}"
                                                    data-alamat="${item.alamat ?? ''}"
                                                    data-perusahaan_id="${item.perusahaan_id}"
                                                    title="Edit untuk perusahaan ini">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-icon btn-outline-danger btn-delete-modal"
                                                    data-id="${item.id}"
                                                    data-nama="${item.nama_supplier}"
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

                            document.getElementById('listSupplier').innerHTML = html;

                            // Edit dari modal
                            document.querySelectorAll('.btn-edit-modal').forEach(b => {
                                b.addEventListener('click', function() {
                                    document.getElementById('edit_nama').value = this.dataset.nama;
                                    document.getElementById('edit_telepon').value = this.dataset.telepon;
                                    document.getElementById('edit_alamat').value = this.dataset.alamat;

                                    @if (auth()->user()->role === 'super_admin')
                                        if (document.getElementById('edit_perusahaan')) {
                                            document.getElementById('edit_perusahaan').value = this.dataset.perusahaan_id;
                                        }
                                    @endif

                                    formEdit.action = `/dashboard/supplier/${this.dataset.id}`;
                                    modalDetail.hide();
                                    modalEdit.show();
                                });
                            });

                            // Delete dari modal
                            document.querySelectorAll('.btn-delete-modal').forEach(b => {
                                b.addEventListener('click', function() {
                                    let id = this.dataset.id;
                                    let perName = this.dataset.perusahaan;
                                    let masukCount = parseInt(this.dataset.masuk || 0);

                                    modalDetail.hide();

                                    let warningText = `Supplier "${currentDetailNama}" pada ${perName} akan dihapus.`;
                                    if (masukCount > 0) {
                                        warningText += ` Perhatian: Terdapat ${masukCount} data penerimaan aset yang terkait dengan supplier ini!`;
                                    }

                                    Swal.fire({
                                        title: 'Yakin hapus supplier ini?',
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
                                            form.action = `/dashboard/supplier/${id}`;
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
                            document.getElementById('listSupplier').innerHTML = `
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-danger">
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
                    const inputNama = document.getElementById('tambah_nama_supplier');
                    if (inputNama) {
                        inputNama.value = currentDetailNama;
                    }
                    const inputTelp = document.getElementById('tambah_telepon');
                    if (inputTelp) {
                        inputTelp.value = currentDetailTelepon;
                    }
                    const inputAlamat = document.getElementById('tambah_alamat');
                    if (inputAlamat) {
                        inputAlamat.value = currentDetailAlamat;
                    }
                    new bootstrap.Modal(document.getElementById('modalTambahSupplier')).show();
                });
            }

        });
    </script>
@endsection
