@extends('layouts/contentNavbarLayout')

@section('title', 'Hak Akses & Aplikasi')

@section('content')

    <div class="container-fluid">

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

            {{-- HEADER --}}
            <div class="card-header d-flex justify-content-between align-items-center">

                <h5 class="text-primary mb-0">
                    Hak Akses & Aplikasi
                </h5>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">

                    <i class="bx bx-plus"></i>
                    Tambah Data

                </button>

            </div>

            <div class="card-body">

                {{-- FILTER --}}
                <form method="GET" class="row g-3 mb-4">

                    <div class="col-md-4">

                        <label class="form-label">
                            Pencarian
                        </label>

                        <input type="text" name="search" class="form-control" placeholder="Cari nama hak akses..."
                            value="{{ request('search') }}">

                    </div>

                    <div class="col-md-3">

                        <label class="form-label">
                            Kategori
                        </label>

                        <select name="kategori" class="form-select">

                            <option value="">
                                Semua
                            </option>



                            <option value="Aplikasi" {{ request('kategori') == 'Aplikasi' ? 'selected' : '' }}>

                                Aplikasi

                            </option>

                            <option value="Hak Akses" {{ request('kategori') == 'Hak Akses' ? 'selected' : '' }}>

                                Hak Akses

                            </option>

                        </select>

                    </div>
                    <div class="col-md-3">

                        <label class="form-label">
                            Jenis
                        </label>

                        <select name="jenis" class="form-select">

                            <option value="">Semua</option>

                            <option value="Software" {{ request('jenis') == 'Software' ? 'selected' : '' }}>
                                Software
                            </option>

                            <option value="PPN" {{ request('jenis') == 'PPN' ? 'selected' : '' }}>
                                PPN
                            </option>

                            <option value="NON PPN" {{ request('jenis') == 'NON PPN' ? 'selected' : '' }}>
                                NON PPN
                            </option>

                        </select>

                    </div>

                    <div class="col-md-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select name="status" class="form-select">

                            <option value="">
                                Semua
                            </option>

                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>

                                Aktif

                            </option>

                            <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>

                                Nonaktif

                            </option>

                        </select>

                    </div>

                    <div class="col-md-2 d-flex align-items-end gap-2">

                        <button class="btn btn-primary w-100">

                            <i class="bx bx-search"></i>

                        </button>

                        <a href="{{ route('hak-akses.index') }}" class="btn btn-secondary">

                            Reset

                        </a>

                    </div>

                </form>

                {{-- TABLE --}}
                <div class="table-responsive">

                    <table class="table table-bordered align-middle">

                        <thead class="table-primary text-center">

                            <tr>

                                <th width="60">
                                    NO
                                </th>

                                <th width="170">
                                    KATEGORI
                                </th>
                                <th width="120">

                                    JENIS

                                </th>

                                <th>
                                    NAMA HAK AKSES / APLIKASI
                                </th>

                                <th width="130">
                                    STATUS
                                </th>

                                <th width="120">
                                    ACTION
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($accesses as $access)
                                <tr>

                                    {{-- NO --}}
                                    <td class="text-center">
                                        {{ ($accesses->currentPage() - 1) * $accesses->perPage() + $loop->iteration }}
                                    </td>

                                    {{-- KATEGORI --}}
                                    <td class="text-center">

                                        @if ($access->kategori == 'Aplikasi')
                                            <span class="badge bg-label-primary">
                                                {{ strtoupper($access->kategori) }}
                                            </span>
                                        @else
                                            <span class="badge bg-label-success">
                                                {{ strtoupper($access->kategori) }}
                                            </span>
                                        @endif

                                    </td>

                                    {{-- JENIS --}}
                                    <td class="text-center">

                                        @if ($access->jenis == 'Software')
                                            <span class="badge bg-label-primary">
                                                Software
                                            </span>
                                        @elseif($access->jenis == 'PPN')
                                            <span class="badge bg-label-warning">
                                                PPN
                                            </span>
                                        @else
                                            <span class="badge bg-label-info">
                                                NON PPN
                                            </span>
                                        @endif

                                    </td>

                                    {{-- NAMA --}}
                                    <td>

                                        <strong>{{ strtoupper($access->nama_akses) }}</strong>

                                    </td>

                                    {{-- STATUS --}}
                                    <td class="text-center">

                                        @if ($access->status == 'aktif')
                                            <span class="badge bg-success">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                Nonaktif
                                            </span>
                                        @endif

                                    </td>

                                    {{-- ACTION --}}
                                    <td class="text-center">

                                        <div class="d-flex justify-content-center gap-2">

                                            <button type="button" class="btn btn-warning btn-sm btn-edit"
                                                data-id="{{ $access->id }}" data-kategori="{{ $access->kategori }}"
                                                data-jenis="{{ $access->jenis }}" data-nama="{{ $access->nama_akses }}"
                                                data-status="{{ $access->status }}">

                                                <i class="bx bx-edit-alt"></i>

                                            </button>

                                            <form id="delete-form-{{ $access->id }}"
                                                action="{{ route('hak-akses.destroy', $access->id) }}" method="POST"
                                                style="display:none">

                                                @csrf
                                                @method('DELETE')

                                            </form>

                                            <button type="button" class="btn btn-danger btn-sm btn-delete"
                                                data-id="{{ $access->id }}">

                                                <i class="bx bx-trash"></i>

                                            </button>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="6" class="text-center">

                                        Tidak ada data.

                                    </td>

                                </tr>
                            @endforelse

                        </tbody>
                    </table>

                </div>

                <div class="mt-3">

                    {{ $accesses->links('pagination::bootstrap-5') }}

                </div>

            </div>

        </div>

    </div>
    <!-- ================= MODAL TAMBAH ================= -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="{{ route('hak-akses.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Hak Akses & Aplikasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">

                            <label class="form-label">

                                Kategori

                            </label>

                            <select id="kategori" name="kategori" class="form-select" required>

                                <option value="">Pilih Kategori</option>

                                <option value="Aplikasi">Aplikasi</option>

                                <option value="Hak Akses">Hak Akses</option>

                            </select>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Jenis

                            </label>

                            <select id="jenis" name="jenis" class="form-select" required>

                            </select>

                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Hak Akses</label>
                            <input type="text" name="nama_akses" class="form-control"
                                style="text-transform: uppercase" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>

                            <select name="status" class="form-select">

                                <option value="aktif">Aktif</option>

                                <option value="nonaktif">Nonaktif</option>

                            </select>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                            Batal

                        </button>

                        <button type="submit" class="btn btn-primary">

                            Simpan

                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>
    <!-- ================= MODAL EDIT ================= -->
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="formEdit" method="POST">

                    @csrf
                    @method('PUT')

                    <div class="modal-header">

                        <h5 class="modal-title">

                            Edit Hak Akses & Aplikasi

                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal">

                        </button>

                    </div>

                    <div class="modal-body">

                        <div class="mb-3">

                            <label class="form-label">

                                Kategori

                            </label>

                            <select id="edit_kategori" name="kategori" class="form-select" required>

                                <option value="Aplikasi">Aplikasi</option>
                                <option value="Hak Akses">Hak Akses</option>

                            </select>

                        </div>
                        <div class="mb-3">

                            <label class="form-label">

                                Jenis

                            </label>

                            <select id="edit_jenis" name="jenis" class="form-select" required>

                            </select>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Nama Hak Akses

                            </label>

                            <input type="text" id="edit_nama" name="nama_akses" class="form-control" required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Status

                            </label>

                            <select id="edit_status" name="status" class="form-select">

                                <option value="aktif">

                                    Aktif

                                </option>

                                <option value="nonaktif">

                                    Nonaktif

                                </option>

                            </select>

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
            // ==============================
            // KATEGORI -> JENIS (TAMBAH)
            // ==============================

            const kategori = document.getElementById('kategori');
            const jenis = document.getElementById('jenis');

            if (kategori) {

                kategori.addEventListener('change', function() {

                    if (this.value === 'Aplikasi') {

                        jenis.innerHTML = `
                <option value="Software" selected>Software</option>
            `;

                        jenis.setAttribute('readonly', true);
                        jenis.setAttribute('disabled', true);

                        // tetap terkirim ke server
                        jenis.removeAttribute('disabled');

                    } else if (this.value === 'Hak Akses') {

                        jenis.innerHTML = `
                <option value="">Pilih Jenis</option>
                <option value="PPN">PPN</option>
                <option value="NON PPN">NON PPN</option>
            `;

                    } else {

                        jenis.innerHTML = `<option value="">Pilih Jenis</option>`;

                    }

                });

            }

            // ==========================
            // DELETE
            // ==========================

            document.querySelectorAll('.btn-delete').forEach(btn => {

                btn.addEventListener('click', function() {

                    let id = this.dataset.id;

                    Swal.fire({

                        title: 'Yakin?',

                        text: 'Data akan dihapus.',

                        icon: 'warning',

                        showCancelButton: true,

                        confirmButtonText: 'Ya',

                        cancelButtonText: 'Batal'

                    }).then((result) => {

                        if (result.isConfirmed) {

                            document
                                .getElementById('delete-form-' + id)
                                .submit();

                        }

                    });

                });

            });

            // ==========================
            // EDIT
            // ==========================

            const modalEdit = document.getElementById('modalEdit');

            const formEdit = document.getElementById('formEdit');

            document.querySelectorAll('.btn-edit').forEach(btn => {

                btn.addEventListener('click', function() {

                    let id = this.dataset.id;

                    formEdit.action = '/dashboard/hak-akses/' + id;

                    document.getElementById('edit_kategori').value = this.dataset.kategori;

                    loadJenisEdit(
                        this.dataset.kategori,
                        this.dataset.jenis
                    );

                    document.getElementById('edit_nama').value = this.dataset.nama;

                    document.getElementById('edit_status').value = this.dataset.status;

                    new bootstrap.Modal(modalEdit).show();

                });

            });
        });

        function loadJenisEdit(kategori, selectedJenis) {

            let jenis = document.getElementById('edit_jenis');

            if (kategori === 'Aplikasi') {

                jenis.innerHTML = `
            <option value="Software">Software</option>
        `;

                jenis.value = "Software";

            } else {

                jenis.innerHTML = `
            <option value="PPN">PPN</option>
            <option value="NON PPN">NON PPN</option>
        `;

                jenis.value = selectedJenis;

            }

        }
    </script>

@endsection
