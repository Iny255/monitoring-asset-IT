@extends('layouts/contentNavbarLayout')

@section('title', 'Lokasi')

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
                    <h5 class="text-primary mb-0">Data Lokasi Barang</h5>
                </div>
                <div class="">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahLokasi">
                        Tambah Data Lokasi
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ url('/dashboard/lokasi') }}" class="row g-3 mb-4">
                <div class="col-md-7 text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1 me-2">
                            <input type="text" name="search" class="form-control w-100"
                                placeholder="Cari berdasarkan lokasi" value="{{ request('search') }}">
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
                        <tr class="center">
                            <th width=>KODE LOKASI</th>
                            <th>NAMA LOKASI</th>
                            <th width=>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lokasis as $lokasi)
                            <tr>
                                <td>{{ $lokasi->kode_lokasi }}</td>
                                <td>{{ $lokasi->nama_lokasi }}</td>

                                <td>
                                    <button class="btn btn-warning btn-sm btn-edit" data-id="{{ $lokasi->id }}"
                                        data-kode="{{ $lokasi->kode_lokasi }}" data-nama="{{ $lokasi->nama_lokasi }}">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>
                                    <form id="delete-form-{{ $lokasi->id }}"
                                        action="{{ route('lokasi.destroy', $lokasi->id) }}" method="POST"
                                        style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $lokasi->id }}">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </td>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $lokasis->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL TAMBAH LOKASI -->
    <div class="modal fade" id="modalTambahLokasi" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="{{ route('lokasi.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Lokasi Barang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <!-- KODE LOKASI -->
                        <div class="mb-3">
                            <label class="form-label">Kode Lokasi</label>
                            <input type="text" name="kode_lokasi" class="form-control" value="{{ $kodeLokasi }}"
                                readonly>
                        </div>

                        <!-- NAMA LOKASI -->
                        <div class="mb-3">
                            <label class="form-label">Nama Lokasi</label>
                            <input type="text" name="nama_lokasi"
                                class="form-control @error('nama_lokasi') is-invalid @enderror"
                                value="{{ old('nama_lokasi') }}" required>

                            @error('nama_lokasi')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
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
    <!-- MODAL EDIT LOKASI -->
    <div class="modal fade" id="modalEditLokasi" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="formEditLokasi" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Lokasi Barang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <!-- KODE -->
                        <div class="mb-3">
                            <label class="form-label">Kode Lokasi</label>
                            <input type="text" id="edit_kode_lokasi" class="form-control" readonly>
                        </div>

                        <!-- NAMA -->
                        <div class="mb-3">
                            <label class="form-label">Nama Lokasi</label>
                            <input type="text" name="nama_lokasi" id="edit_nama_lokasi" class="form-control"
                                required>
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

            // DELETE
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;

                    Swal.fire({
                        title: 'Apakah kamu yakin?',
                        text: "Data lokasi ini akan dihapus!",
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

            // EDIT POPUP
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {

                    const id = this.dataset.id;
                    const kode = this.dataset.kode;
                    const nama = this.dataset.nama;

                    document.getElementById('edit_kode_lokasi').value = kode;
                    document.getElementById('edit_nama_lokasi').value = nama;

                    document.getElementById('formEditLokasi').action =
                        `/dashboard/lokasi/${id}`;

                    var editModal = new bootstrap.Modal(
                        document.getElementById('modalEditLokasi')
                    );
                    editModal.show();
                });
            });

        });
    </script>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var modal = new bootstrap.Modal(
                    document.getElementById('modalTambahLokasi')
                );
                modal.show();
            });
        </script>
    @endif
@endsection
