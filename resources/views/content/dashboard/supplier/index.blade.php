@extends('layouts/contentNavbarLayout')

@section('title', 'Supplier')

@section('content')

    <div class="container-fluid">

        {{-- ALERT --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
            </div>
        @endif

        <div class="card shadow-sm border-0">

            {{-- HEADER --}}
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold text-primary">
                    Data Supplier
                </h5>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSupplier">

                    <i class="bx bx-plus"></i>
                    Tambah Data
                </button>
            </div>

            <div class="card-body p-4">

                {{-- FILTER PERUSAHAAN --}}
                @if (auth()->user()->role === 'super_admin')
                    <form method="GET" class="row mb-3">

                        <div class="col-md-4">
                            <select name="perusahaan_id" class="form-control">
                                <option value="">
                                    -- Semua Perusahaan --
                                </option>

                                @foreach ($perusahaans as $p)
                                    <option value="{{ $p->id }}"
                                        {{ request('perusahaan_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama_perusahaan }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <div class="col-md-2">
                            <button class="btn btn-primary">
                                Filter
                            </button>
                        </div>

                    </form>
                @endif

                {{-- SEARCH --}}
                <form method="GET" class="row mb-4">

                    <div class="col-md-6 d-flex">

                        <input type="text" name="search" class="form-control me-2" placeholder="Cari supplier..."
                            value="{{ request('search') }}">

                        <button class="btn btn-primary">
                            Cari
                        </button>

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

                                @if (auth()->user()->role === 'super_admin')
                                    <th>PERUSAHAAN</th>
                                @endif

                                <th width="150">ACTION</th>

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
                                    @if (auth()->user()->role === 'super_admin')
                                        <td>
                                            {{ $supplier->perusahaan->nama_perusahaan ?? '-' }}
                                        </td>
                                    @endif

                                    <td class="text-center">

                                        <button type="button" class="btn btn-warning btn-sm btn-edit"
                                            data-id="{{ $supplier->id }}" data-nama="{{ $supplier->nama_supplier }}"
                                            data-telepon="{{ $supplier->telepon }}" data-alamat="{{ $supplier->alamat }}">

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

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6" class="text-center">
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
                            <label>Telepon</label>
                            <input type="text" name="telepon" class="form-control">
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
                            <label>Telepon</label>
                            <input type="text" id="edit_telepon" name="telepon" class="form-control">
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

@endsection

@section('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // DELETE
            document.querySelectorAll('.btn-delete').forEach(btn => {

                btn.addEventListener('click', function() {

                    let id = this.dataset.id;

                    Swal.fire({
                        title: 'Yakin hapus?',
                        text: 'Data tidak bisa dikembalikan!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {

                        if (result.isConfirmed) {
                            document.getElementById('delete-form-' + id).submit();
                        }

                    });

                });

            });

            // EDIT
            document.querySelectorAll('.btn-edit').forEach(btn => {

                btn.addEventListener('click', function() {

                    let id = this.dataset.id;

                    document.getElementById('edit_nama').value = this.dataset.nama;
                    document.getElementById('edit_telepon').value = this.dataset.telepon;
                    document.getElementById('edit_alamat').value = this.dataset.alamat;

                    document.getElementById('formEditSupplier').action =
                        `/dashboard/supplier/${id}`;

                    new bootstrap.Modal(
                        document.getElementById('modalEditSupplier')
                    ).show();

                });

            });

        });
    </script>

@endsection
