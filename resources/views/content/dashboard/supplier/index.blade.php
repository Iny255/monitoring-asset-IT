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
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSupplier">
                            <i class="bx bx-plus me-1"></i> Tambah Supplier
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ALERT --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">

                {{-- FILTER + SEARCH --}}
                <form method="GET" class="row g-3 align-items-end mb-4">
                    @if (auth()->user()->role === 'super_admin')
                        <div class="col-md-4">
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

                    <div class="col-md-5">
                        <label class="form-label">Pencarian</label>
                        <input type="text" name="search" class="form-control" placeholder="Cari supplier..."
                            value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-primary w-100">
                            <i class="bx bx-search"></i> Cari
                        </button>
                        <a href="{{ route('supplier.index') }}" class="btn btn-secondary w-100">
                            Reset
                        </a>
                    </div>
                </form>

                {{-- TABLE --}}
                <div class="table-responsive">

                    <table class="table table-bordered">

                        <thead class="table-primary text-center">

                            <tr>

                                <th width="60">NO</th>
                                <th>NAMA SUPPLIER</th>
                                <th width="150">NO HP</th>
                                <th>ALAMAT</th>

                                @if (auth()->user()->role == 'super_admin')

                                    @if (request('perusahaan_id'))
                                        <th>PERUSAHAAN</th>
                                    @else
                                        <th>DIGUNAKAN DI</th>
                                    @endif
                                @else
                                    <th>PERUSAHAAN</th>

                                @endif

                                @if (!(auth()->user()->role == 'super_admin' && empty(request('perusahaan_id'))))
                                    <th width="150">ACTION</th>
                                @endif

                            </tr>

                        </thead>
                        <tbody>

                            @forelse($suppliers as $index => $supplier)
                                <tr>

                                    <td class="text-center">
                                        {{ $suppliers->firstItem() + $index }}
                                    </td>

                                    <td>
                                        {{ strtoupper($supplier->nama_supplier) }}
                                    </td>

                                    <td>
                                        {{ $supplier->telepon ?: '-' }}
                                    </td>

                                    <td>
                                        {{ $supplier->alamat ?: '-' }}
                                    </td>
                                    @if (auth()->user()->role == 'super_admin')
                                        @if (request('perusahaan_id'))
                                            <td>
                                                {{ $supplier->perusahaan->nama_perusahaan }}
                                            </td>
                                        @else
                                            <td>

                                                <span class="badge bg-label-primary btn-detail-supplier"
                                                    style="cursor:pointer" data-nama="{{ $supplier->nama_supplier }}">

                                                    {{ $supplier->total_perusahaan }} Perusahaan

                                                </span>

                                            </td>
                                        @endif
                                    @else
                                        <td>
                                            {{ $supplier->perusahaan->nama_perusahaan ?? '-' }}
                                        </td>
                                    @endif
                                    @if (!(auth()->user()->role == 'super_admin' && empty(request('perusahaan_id'))))
                                        <td class="text-center">

                                            <button type="button" class="btn btn-warning btn-sm btn-edit"
                                                data-id="{{ $supplier->id }}" data-nama="{{ $supplier->nama_supplier }}"
                                                data-telepon="{{ $supplier->telepon }}"
                                                data-alamat="{{ $supplier->alamat }}"
                                                data-perusahaan="{{ $supplier->perusahaan->nama_perusahaan ?? '-' }}"
                                                data-perusahaan_id="{{ $supplier->perusahaan_id }}">

                                                <i class="bx bx-edit-alt"></i>

                                            </button>

                                            <form id="delete-form-{{ $supplier->id }}"
                                                action="{{ route('supplier.destroy', $supplier->id) }}" method="POST"
                                                style="display:none;">

                                                @csrf
                                                @method('DELETE')

                                            </form>

                                            <button type="button" class="btn btn-danger btn-sm btn-delete"
                                                data-id="{{ $supplier->id }}">

                                                <i class="bx bx-trash"></i>

                                            </button>

                                        </td>
                                    @endif

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="{{ auth()->user()->role == 'super_admin' && empty(request('perusahaan_id')) ? 5 : 6 }}"
                                        class="text-center">
                                        Data tidak ditemukan
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="mt-3">
                    {{ $suppliers->links('pagination::bootstrap-5') }}
                </div>

            </div>

        </div>

    </div>

    {{-- MODAL TAMBAH --}}
    <div class="modal fade" id="modalTambahSupplier">

        <div class="modal-dialog">

            <div class="modal-content">

                <form action="{{ route('supplier.store') }}" method="POST">

                    @csrf

                    <div class="modal-header">
                        <h5>Tambah Supplier</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        @if (auth()->user()->role === 'super_admin')

                            <div class="mb-3">

                                <label>Perusahaan</label>

                                <select name="perusahaan_id" class="form-control" required>

                                    <option value="">
                                        Pilih Perusahaan
                                    </option>

                                    @foreach ($perusahaans as $p)
                                        <option value="{{ $p->id }}">
                                            {{ $p->nama_perusahaan }}
                                        </option>
                                    @endforeach

                                </select>

                            </div>

                        @endif

                        <div class="mb-3">
                            <label>Nama Supplier</label>
                            <input type="text" name="nama_supplier" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>No HP / Telepon</label>
                            <input type="text" name="telepon" class="form-control" placeholder="Contoh: 081234567890">
                        </div>

                        <div class="mb-3">
                            <label>Alamat</label>
                            <textarea name="alamat" rows="3" class="form-control"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">
                            Simpan
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>

    {{-- MODAL EDIT --}}
    <div class="modal fade" id="modalEditSupplier">

        <div class="modal-dialog">

            <div class="modal-content">

                <form id="formEditSupplier" method="POST">

                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5>Edit Supplier</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label>Nama Supplier</label>
                            <input type="text" id="edit_nama" name="nama_supplier" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>No HP / Telepon</label>
                            <input type="text" id="edit_telepon" name="telepon" class="form-control" placeholder="Contoh: 081234567890">
                        </div>

                        <div class="mb-3">
                            <label>Alamat</label>
                            <textarea id="edit_alamat" name="alamat" rows="3" class="form-control"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit" class="btn btn-primary">
                            Update
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
    {{-- modal detail --}}
    <div class="modal fade" id="modalDetailSupplier">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header bg-primary">

                    <h5 class="modal-title text-white">
                        Detail Supplier
                    </h5>

                    <button class="btn-close btn-close-white" data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <h5 id="judulSupplier"></h5>

                    <div class="table-responsive">

                        <table class="table table-bordered">

                            <thead>

                                <tr>

                                    <th>No</th>
                                    <th>Perusahaan</th>
                                    <th width="150">Aksi</th>

                                </tr>

                            </thead>

                            <tbody id="listSupplier">

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ======================================
            // DELETE
            // ======================================
            document.querySelectorAll('.btn-delete').forEach(btn => {

                btn.addEventListener('click', function() {

                    let id = this.dataset.id;

                    Swal.fire({
                        title: 'Yakin hapus?',
                        text: 'Data tidak bisa dikembalikan!',
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

            // ======================================
            // EDIT
            // ======================================
            document.querySelectorAll('.btn-edit').forEach(btn => {

                btn.addEventListener('click', function() {

                    let id = this.dataset.id;

                    document.getElementById('edit_nama').value =
                        this.dataset.nama;

                    document.getElementById('edit_telepon').value =
                        this.dataset.telepon;

                    document.getElementById('edit_alamat').value =
                        this.dataset.alamat;

                    @if (auth()->user()->role === 'super_admin')
                        if (document.getElementById('edit_perusahaan')) {
                            document.getElementById('edit_perusahaan').value =
                                this.dataset.perusahaan_id;
                        }
                    @endif

                    document.getElementById('formEditSupplier').action =
                        `/dashboard/supplier/${id}`;

                    new bootstrap.Modal(
                        document.getElementById('modalEditSupplier')
                    ).show();

                });

            });

            // ======================================
            // DETAIL SUPPLIER
            // ======================================
            const modalDetail = new bootstrap.Modal(
                document.getElementById('modalDetailSupplier')
            );

            document.querySelectorAll('.btn-detail-supplier').forEach(btn => {

                btn.addEventListener('click', function() {

                    fetch(
                            `/dashboard/supplier/detail-perusahaan?nama_supplier=${encodeURIComponent(this.dataset.nama)}`)

                        .then(res => res.json())

                        .then(data => {

                            document.getElementById('judulSupplier').innerHTML =
                                data[0].nama_supplier;

                            let html = '';

                            data.forEach((item, index) => {

                                html += `
                    <tr>

                        <td>${index+1}</td>

                        <td>${item.perusahaan.nama_perusahaan}</td>

                        <td class="text-center">

                            <button
                                class="btn btn-warning btn-sm btn-edit-modal me-1"

                                data-id="${item.id}"
                                data-nama="${item.nama_supplier}"
                                data-telepon="${item.telepon ?? ''}"
                                data-alamat="${item.alamat ?? ''}"
                                data-perusahaan="${item.perusahaan.nama_perusahaan}"
                                data-perusahaan_id="${item.perusahaan_id}">

                                <i class="bx bx-edit-alt"></i>

                            </button>

                            <button
                                class="btn btn-danger btn-sm btn-delete-modal"
                                data-id="${item.id}">

                                <i class="bx bx-trash"></i>

                            </button>

                        </td>

                    </tr>
                    `;

                            });

                            document.getElementById('listSupplier').innerHTML = html;

                            // ======================================
                            // EDIT DARI MODAL
                            // ======================================
                            document.querySelectorAll('.btn-edit-modal').forEach(btn => {

                                btn.addEventListener('click', function() {

                                    document.getElementById('edit_nama').value =
                                        this.dataset.nama;

                                    document.getElementById('edit_telepon')
                                        .value =
                                        this.dataset.telepon;

                                    document.getElementById('edit_alamat')
                                        .value =
                                        this.dataset.alamat;

                                    @if (auth()->user()->role === 'super_admin')
                                        if (document.getElementById(
                                                'edit_perusahaan')) {
                                            document.getElementById(
                                                    'edit_perusahaan').value =
                                                this.dataset.perusahaan_id;
                                        }
                                    @endif

                                    document.getElementById('formEditSupplier')
                                        .action =
                                        `/dashboard/supplier/${this.dataset.id}`;

                                    modalDetail.hide();

                                    new bootstrap.Modal(
                                        document.getElementById(
                                            'modalEditSupplier')
                                    ).show();

                                });

                            });

                            // ======================================
                            // DELETE DARI MODAL
                            // ======================================
                            document.querySelectorAll('.btn-delete-modal').forEach(btn => {

                                btn.addEventListener('click', function() {

                                    let id = this.dataset.id;

                                    modalDetail.hide();

                                    Swal.fire({
                                        title: 'Yakin hapus?',
                                        text: 'Data tidak bisa dikembalikan!',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#696cff',
                                        cancelButtonColor: '#8592a3',
                                        confirmButtonText: 'Ya, hapus!',
                                        cancelButtonText: 'Batal'

                                    }).then((result) => {

                                        if (result.isConfirmed) {

                                            const form = document
                                                .createElement('form');

                                            form.method = 'POST';
                                            form.action =
                                                `/dashboard/supplier/${id}`;

                                            form.innerHTML = `
                                    @csrf
                                    <input type="hidden" name="_method" value="DELETE">
                                `;

                                            document.body.appendChild(
                                                form);

                                            form.submit();

                                        } else {

                                            modalDetail.show();

                                        }

                                    });

                                });

                            });

                            modalDetail.show();

                        });

                });

            });

        });
    </script>
@endsection
