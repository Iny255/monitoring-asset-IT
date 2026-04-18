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
                        <tr class="center">
                            <th width=>KODE PERUSAHAAN</th>
                            <th>NAMA PERUSAHAAN</th>
                            <th width=>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($perusahaans as $perusahaan)
                            <tr>
                                <td>{{ $perusahaan->kode_perusahaan }}</td>
                                <td>{{ $perusahaan->nama_perusahaan }}</td>

                                <td>
                                    <button class="btn btn-warning btn-sm btn-edit" data-id="{{ $perusahaan->id }}"
                                        data-kode="{{ $perusahaan->kode_perusahaan }}"
                                        data-nama="{{ $perusahaan->nama_perusahaan }}">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>
                                    <form id="delete-form-{{ $perusahaan->id }}"
                                        action="{{ route('perusahaan.destroy', $perusahaan->id) }}" method="POST"
                                        style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                    <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $perusahaan->id }}">
                                        <i class="bx bx-trash"></i>
                                    </button>

                                </td>
                        @endforeach
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

                <form action="{{ route('perusahaan.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Data Perusahaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <!-- KODE PERUSAHAAN -->
                        <div class="mb-3">
                            <label class="form-label">Kode Perusahaan</label>
                            <input type="text"  class="form-control" value="{{ $kodePerusahaan }}"
                                readonly>
                        </div>

                        <!-- NAMA PERUSAHAAN -->
                        <div class="mb-3">
                            <label class="form-label">Nama Perusahaan</label>
                            <input type="text" name="nama_perusahaan"
                                class="form-control @error('nama_perusahaan') is-invalid @enderror"
                                value="{{ old('nama_perusahaan') }}" required>

                            @error('nama_perusahaan')
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
    <!-- MODAL EDIT PERUSAHAAN -->
    <div class="modal fade" id="modalEditPerusahaan" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="formEditPerusahaan" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Data Perusahaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <!-- KODE -->
                        <div class="mb-3">
                            <label class="form-label">Kode Perusahaan</label>
                            <input type="text" id="edit_kode_perusahaan" class="form-control" readonly>
                        </div>

                        <!-- NAMA -->
                        <div class="mb-3">
                            <label class="form-label">Nama Perusahaan</label>
                            <input type="text" name="nama_perusahaan" id="edit_nama_perusahaan" class="form-control"
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
document.addEventListener('DOMContentLoaded', function () {
    var myModal = new bootstrap.Modal(
        document.getElementById('modalTambahPerusahaan')
    );
    myModal.show();
});
</script>
@endif
@endsection
