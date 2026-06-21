@extends('layouts/contentNavbarLayout')

@section('title', 'Perusahaan')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    </h4>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div class="">
                    <h5 class="text-primary mb-0">Data Perusahaan</h5>
                </div>
                <div class="">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPerusahaan">
                        Tambah Data Perusahaan
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ url('/dashboard/perusahaan') }}" class="row g-3 mb-4">
                <div class="col-md-7 text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1 me-2">
                            <input type="text" name="search" class="form-control w-100"
                                placeholder="Cari berdasarkan nama perusahaan" value="{{ request('search') }}">
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive text-nowrap">
                <table class="table table-bordered">
                    <thead class="table-primary">
                        <tr>

                            <th width="120">
                                KODE
                            </th>

                            <th>
                                NAMA PERUSAHAAN
                            </th>

                            <th width="150">
                                TEMA
                            </th>

                            <th width="120">
                                ACTION
                            </th>

                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($perusahaans as $perusahaan)
                            <tr>

                                {{-- KODE --}}
                                <td>
                                    <span class="badge bg-primary">
                                        {{ $perusahaan->kode_perusahaan }}
                                    </span>
                                </td>

                                {{-- NAMA --}}
                                <td>
                                    {{ $perusahaan->nama_perusahaan }}
                                </td>

                                {{-- TEMA WARNA --}}
                                <td>

                                    <div class="d-flex align-items-center gap-2">

                                        <span
                                            style="
                            width:25px;
                            height:25px;
                            border-radius:50%;
                            display:inline-block;
                            border:1px solid #ddd;
                            background:{{ $perusahaan->primary_color }};
                        ">
                                        </span>

                                        <span
                                            style="
                            width:25px;
                            height:25px;
                            border-radius:50%;
                            display:inline-block;
                            border:1px solid #ddd;
                            background:{{ $perusahaan->secondary_color }};
                        ">
                                        </span>

                                    </div>

                                </td>

                                {{-- ACTION --}}
                                <td>

                                    <div class="d-flex gap-2">

                                        {{-- EDIT --}}
                                        <button class="btn btn-warning btn-sm btn-edit" data-id="{{ $perusahaan->id }}"
                                            data-kode="{{ $perusahaan->kode_perusahaan }}"
                                            data-nama="{{ $perusahaan->nama_perusahaan }}"
                                            data-primary="{{ $perusahaan->primary_color }}"
                                            data-secondary="{{ $perusahaan->secondary_color }}">

                                            <i class="bx bx-edit-alt"></i>

                                        </button>

                                        {{-- FORM DELETE --}}
                                        <form id="delete-form-{{ $perusahaan->id }}"
                                            action="{{ route('perusahaan.destroy', $perusahaan->id) }}" method="POST"
                                            style="display:none;">

                                            @csrf
                                            @method('DELETE')

                                        </form>

                                        {{-- DELETE --}}
                                        <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $perusahaan->id }}">

                                            <i class="bx bx-trash"></i>

                                        </button>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4" class="text-center">

                                    Data perusahaan tidak ditemukan

                                </td>

                            </tr>
                        @endforelse

                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $perusahaans->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL TAMBAH PERUSAHAAN -->
    <div class="modal fade" id="modalTambahPerusahaan" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="{{ route('perusahaan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Data Perusahaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">
                                Kode Perusahaan
                            </label>

                            <input type="text" name="kode_perusahaan" class="form-control" value="{{ $kodePerusahaan }}"
                                readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Nama Perusahaan
                            </label>

                            <input type="text" name="nama_perusahaan" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Primary Color
                            </label>

                            <input type="color" name="primary_color" class="form-control form-control-color"
                                value="#0d6efd">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Secondary Color
                            </label>

                            <input type="color" name="secondary_color" class="form-control form-control-color"
                                value="#6c757d">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Logo
                            </label>

                            <input type="file" name="logo" class="form-control">
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
    <!-- MODAL EDIT PERUSAHAAN -->
    <div class="modal fade" id="modalEditPerusahaan" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="formEditPerusahaan" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Data Perusahaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            Kode Perusahaan
                        </label>

                        <input type="text" name="kode_perusahaan" class="form-control" maxlength="2"
                            placeholder="Contoh : 05" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Nama Perusahaan
                        </label>

                        <input type="text" id="edit_nama_perusahaan" name="nama_perusahaan" class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Primary Color
                        </label>

                        <input type="color" id="edit_primary_color" name="primary_color"
                            class="form-control form-control-color">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Secondary Color
                        </label>

                        <input type="color" id="edit_secondary_color" name="secondary_color"
                            class="form-control form-control-color">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Logo
                        </label>

                        <input type="file" name="logo" class="form-control">
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
<!--/ Striped Rows -->
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ===============================
            // DELETE
            // ===============================
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;

                    Swal.fire({
                        title: 'Apakah kamu yakin?',
                        text: "Data perusahaan ini akan dihapus!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(`delete-form-${id}`).submit();
                        }
                    });
                });
            });


            // ===============================
            // EDIT POPUP
            // ===============================
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {

                    const id = this.dataset.id;
                    const kode = this.dataset.kode;
                    const nama = this.dataset.nama;

                    // isi form modal
                    document.getElementById('edit_kode_perusahaan').value = kode;
                    document.getElementById('edit_nama_perusahaan').value = nama;

                    // set action form
                    document.getElementById('formEditPerusahaan').action =
                        `/dashboard/perusahaan/${id}`;

                    // tampilkan modal
                    var editModal = new bootstrap.Modal(
                        document.getElementById('modalEditPerusahaan')
                    );
                    editModal.show();
                });
            });

        });
    </script>

    {{-- Buka modal tambah jika ada error --}}
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var myModal = new bootstrap.Modal(
                    document.getElementById('modalTambahPerusahaan')
                );
                myModal.show();
            });
        </script>
    @endif
@endsection
