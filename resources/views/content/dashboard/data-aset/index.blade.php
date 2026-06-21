@extends('layouts/contentNavbarLayout')

@section('title', 'Data Aset')

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
                    Data Aset
                </h5>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahAset">

                    <i class="bx bx-plus"></i>
                    Tambah Data

                </button>

            </div>

            <div class="card-body">

                {{-- FILTER --}}
                <form method="GET" class="row g-3 mb-4">

                    @if (auth()->user()->role === 'super_admin')

                        <div class="col-md-3">

                            <label class="form-label">
                                Perusahaan
                            </label>

                            <select name="perusahaan_id" id="perusahaan_id" class="form-select" required>

                                <option value="">
                                    Semua Perusahaan
                                </option>

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

                        <label class="form-label">
                            Pencarian
                        </label>

                        <input type="text" name="search" class="form-control" placeholder="Cari merek / type"
                            value="{{ request('search') }}">

                    </div>

                    <div class="col-md-4 d-flex align-items-end gap-2">

                        <button class="btn btn-primary">
                            <i class="bx bx-search"></i>
                            Cari
                        </button>

                        <a href="{{ route('data-aset.index') }}" class="btn btn-secondary">

                            Reset

                        </a>

                    </div>

                </form>

                {{-- TABLE --}}
                <div class="table-responsive">

                    <table class="table table-bordered align-middle">

                        <thead class="table-primary text-center">

                            <tr>

                                <th width="60">NO</th>

                                <th>KATEGORI</th>

                                <th>MEREK</th>

                                <th>TYPE</th>

                                <th>WARNA</th>

                                @if (auth()->user()->role === 'super_admin')
                                    <th>PERUSAHAAN</th>
                                @endif

                                <th width="130">ACTION</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($dataAsets as $aset)
                                <tr>

                                    {{-- NO --}}
                                    <td class="text-center">
                                        {{ ($dataAsets->currentPage() - 1) * $dataAsets->perPage() + $loop->iteration }}
                                    </td>

                                    {{-- KATEGORI --}}
                                    <td>
                                        {{ strtoupper($aset->kategori->nama_barang ?? '-') }}
                                    </td>

                                    {{-- MEREK --}}
                                    <td>
                                        {{ strtoupper($aset->merek ?? '-') }}
                                    </td>

                                    {{-- TYPE --}}
                                    <td>
                                        {{ strtoupper($aset->type ?? '-') }}
                                    </td>

                                    {{-- WARNA --}}
                                    <td>
                                        {{ $aset->warna ? strtoupper($aset->warna) : '-' }}
                                    </td>

                                    {{-- PERUSAHAAN --}}
                                    @if (auth()->user()->role === 'super_admin')
                                        <td>
                                            {{ strtoupper($aset->perusahaan->nama_perusahaan ?? '-') }}
                                        </td>
                                    @endif

                                    {{-- ACTION --}}
                                    <td class="text-center">

                                        <div class="d-flex justify-content-center gap-2">

                                            {{-- EDIT --}}
                                            <button type="button" class="btn btn-warning btn-sm btn-edit"
                                                data-id="{{ $aset->id }}" data-perusahaan="{{ $aset->perusahaan_id }}"
                                                data-kategori="{{ $aset->kategori_id }}" data-merek="{{ $aset->merek }}"
                                                data-type="{{ $aset->type }}" data-warna="{{ $aset->warna }}">
                                                <i class="bx bx-edit-alt"></i>
                                            </button>

                                            {{-- DELETE FORM --}}
                                            <form id="delete-form-{{ $aset->id }}"
                                                action="{{ route('data-aset.destroy', $aset->id) }}" method="POST"
                                                style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>

                                            {{-- DELETE BUTTON --}}
                                            <button type="button" class="btn btn-danger btn-sm btn-delete"
                                                data-id="{{ $aset->id }}">
                                                <i class="bx bx-trash"></i>
                                            </button>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="{{ auth()->user()->role === 'super_admin' ? 7 : 6 }}" class="text-center">
                                        Data tidak ditemukan
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

                {{-- PAGINATION --}}
                <div class="mt-3">

                    {{ $dataAsets->links('pagination::bootstrap-5') }}

                </div>

            </div>

        </div>

    </div>
    <!-- ================= MODAL TAMBAH ================= -->
    <div class="modal fade" id="modalTambahAset" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <form action="{{ route('data-aset.store') }}" method="POST">

                    @csrf

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Tambah Data Aset
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                    </div>

                    <div class="modal-body">

                        <div class="row">

                            @if (auth()->user()->role === 'super_admin')

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Perusahaan
                                    </label>

                                    <select name="perusahaan_id" id="perusahaan_modal" class="form-select" required>

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

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Kategori Aset
                                </label>

                                <select name="kategori_id" id="kategori_id" class="form-select" required>

                                    <option value="">
                                        Pilih Kategori
                                    </option>

                                    @if (auth()->user()->role !== 'super_admin')

                                        @foreach ($kategoris as $kategori)
                                            <option value="{{ $kategori->id }}">
                                                {{ strtoupper($kategori->nama_barang) }}
                                            </option>
                                        @endforeach

                                    @endif
                                </select>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Merek
                                </label>

                                <input type="text" name="merek" class="form-control" style="text-transform: uppercase"
                                    required>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Type
                                </label>

                                <input type="text" name="type" class="form-control"
                                    style="text-transform: uppercase" required>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Warna
                                </label>

                                <input type="text" name="warna" class="form-control"
                                    style="text-transform: uppercase">

                            </div>

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
    <div class="modal fade" id="modalEditAset" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <form id="formEditAset" method="POST">

                    @csrf
                    @method('PUT')

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Edit Data Aset
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                    </div>

                    <div class="modal-body">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Kategori Aset
                                </label>

                                <select id="edit_kategori" name="kategori_id" class="form-select" required>

                                    @foreach ($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}">
                                            {{ strtoupper($kategori->nama_barang) }}
                                        </option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Merek
                                </label>

                                <input type="text" id="edit_merek" name="merek" class="form-control" required>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Type
                                </label>

                                <input type="text" id="edit_type" name="type" class="form-control" required>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Warna
                                </label>

                                <input type="text" id="edit_warna" name="warna" class="form-control">

                            </div>

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

            // =====================================
            // DELETE
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
                            document
                                .getElementById('delete-form-' + id)
                                .submit();
                        }

                    });

                });

            });

            // =====================================
            // MODAL EDIT
            // =====================================

            const modalEdit = document.getElementById('modalEditAset');
            const formEdit = document.getElementById('formEditAset');

            document.querySelectorAll('.btn-edit').forEach(btn => {

                btn.addEventListener('click', function() {

                    let id = this.dataset.id;
                    let perusahaanId = this.dataset.perusahaan;
                    let kategoriId = this.dataset.kategori;

                    document.getElementById('edit_merek').value =
                        this.dataset.merek;

                    document.getElementById('edit_type').value =
                        this.dataset.type;

                    document.getElementById('edit_warna').value =
                        this.dataset.warna ?? '';

                    formEdit.action =
                        `/dashboard/data-aset/${id}`;

                    fetch('/dashboard/get-kategori/' + perusahaanId)

                        .then(response => response.json())

                        .then(data => {

                            let html =
                                '<option value="">Pilih Kategori</option>';

                            data.forEach(item => {

                                html += `
                            <option value="${item.id}"
                                ${item.id == kategoriId ? 'selected' : ''}>
                                ${item.nama_barang.toUpperCase()}
                            </option>
                        `;

                            });

                            document.getElementById('edit_kategori')
                                .innerHTML = html;

                            new bootstrap.Modal(modalEdit).show();

                        })

                        .catch(error => {

                            console.log(error);

                            Swal.fire(
                                'Error',
                                'Gagal memuat kategori',
                                'error'
                            );

                        });

                });

            });

            // =====================================
            // LOAD KATEGORI SAAT TAMBAH DATA
            // =====================================

            const perusahaanTambah =
                document.getElementById('perusahaan_modal');

            const kategoriTambah =
                document.getElementById('kategori_id');

            if (perusahaanTambah && kategoriTambah) {

                perusahaanTambah.addEventListener('change', function() {

                    let perusahaanId = this.value;

                    kategoriTambah.innerHTML =
                        '<option value="">Loading...</option>';

                    fetch('/dashboard/get-kategori/' + perusahaanId)

                        .then(response => response.json())

                        .then(data => {

                            let html =
                                '<option value="">Pilih Kategori</option>';

                            data.forEach(item => {

                                html += `
                            <option value="${item.id}">
                                ${item.nama_barang.toUpperCase()}
                            </option>
                        `;

                            });

                            kategoriTambah.innerHTML = html;

                        })

                        .catch(error => {

                            console.log(error);

                            kategoriTambah.innerHTML =
                                '<option value="">Gagal memuat kategori</option>';

                        });

                });

            }

        });
    </script>
@endsection
