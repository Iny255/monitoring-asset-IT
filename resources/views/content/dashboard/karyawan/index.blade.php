@extends('layouts/contentNavbarLayout')

@section('title', 'Karyawan')

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
                    <h5 style="color: navy">Data Karyawan</h5>
                </div>
                <div class="">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKaryawan">
                        Tambah Data Karyawan
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ url('/dashboard/karyawan') }}" class="row g-3 mb-4">
                <div class="col-md-7 text">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1 me-2">
                            <input type="text" name="search" class="form-control w-100"
                                placeholder="Cari berdasarkan nama karyawan" value="{{ request('search') }}">
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
                            <th width=>KODE KARYAWAN</th>
                            <th>NAMA KARYAWAN</th>
                            <th>JABATAN</th>
                            <th>DIVISI</th>
                            <th>PERUSAHAAN</th>
                            <th width=>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($karyawans as $karyawan)
                            <tr>
                                <td>{{ $karyawan->kode_karyawan }}</td>
                                <td>{{ $karyawan->nama_karyawan }}</td>
                                <td>{{ $karyawan->jabatan }}</td>
                                <td>{{ $karyawan->divisi }}</td>
                                <td>{{ $karyawan->perusahaan }}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm btn-edit" data-id="{{ $karyawan->id }}"
                                        data-kode="{{ $karyawan->kode_karyawan }}"
                                        data-nama="{{ $karyawan->nama_karyawan }}" data-jabatan="{{ $karyawan->jabatan }}"
                                        data-divisi="{{ $karyawan->divisi }}"
                                        data-perusahaan="{{ $karyawan->perusahaan }}">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>
                                    <form id="delete-form-{{ $karyawan->id }}"
                                        action="{{ route('karyawan.destroy', $karyawan->id) }}" method="POST"
                                        style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $karyawan->id }}">
                                        <i class="bx bx-trash"></i>
                                    </button>

                                </td>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $karyawans->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH KARYAWAN -->
    <div class="modal fade" id="modalTambahKaryawan" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">

                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold">Tambah Data Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body px-4">

                    <form action="{{ route('karyawan.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Kode Karyawan</label>
                            <input type="text" name="kode_karyawan"
                                class="form-control @error('kode_karyawan') is-invalid @enderror"
                                value="{{ old('kode_karyawan') }}">
                            @error('kode_karyawan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Karyawan</label>
                            <input type="text" name="nama_karyawan"
                                class="form-control @error('nama_karyawan') is-invalid @enderror"
                                value="{{ old('nama_karyawan') }}">
                            @error('nama_karyawan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror"
                                value="{{ old('jabatan') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Divisi</label>
                            <input type="text" name="divisi" class="form-control @error('divisi') is-invalid @enderror"
                                value="{{ old('divisi') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Perusahaan</label>
                            <input type="text" name="perusahaan"
                                class="form-control @error('perusahaan') is-invalid @enderror"
                                value="{{ old('perusahaan') }}">
                        </div>

                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
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
    </div>
    <!-- MODAL EDIT KARYAWAN -->
    <div class="modal fade" id="modalEditKaryawan" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">

                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold">Edit Data Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body px-4">

                    <form id="formEditKaryawan" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Kode Karyawan</label>
                            <input type="text" name="kode_karyawan" id="edit_kode" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Karyawan</label>
                            <input type="text" name="nama_karyawan" id="edit_nama" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="jabatan" id="edit_jabatan" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Divisi</label>
                            <input type="text" name="divisi" id="edit_divisi" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Perusahaan</label>
                            <input type="text" name="perusahaan" id="edit_perusahaan" class="form-control">
                        </div>

                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-warning">
                                Update
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/karyawan.js') }}"></script>
@endpush
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /* ================= DELETE ================= */
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;

                    Swal.fire({
                        title: 'Apakah kamu yakin?',
                        text: "Data karyawan ini akan dihapus!",
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


            /* ================= EDIT POPUP ================= */
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {

                    const id = this.dataset.id;

                    document.getElementById('edit_kode').value = this.dataset.kode;
                    document.getElementById('edit_nama').value = this.dataset.nama;
                    document.getElementById('edit_jabatan').value = this.dataset.jabatan;
                    document.getElementById('edit_divisi').value = this.dataset.divisi;
                    document.getElementById('edit_perusahaan').value = this.dataset.perusahaan;

                    document.getElementById('formEditKaryawan')
                        .action = `/dashboard/karyawan/${id}`;

                    var modal = new bootstrap.Modal(document.getElementById('modalEditKaryawan'));
                    modal.show();
                });
            });


            /* ================= AUTO OPEN MODAL TAMBAH JIKA ERROR ================= */
            @if ($errors->any())
                var modalTambah = new bootstrap.Modal(document.getElementById('modalTambahKaryawan'));
                modalTambah.show();
            @endif

        });
    </script>
@endsection
