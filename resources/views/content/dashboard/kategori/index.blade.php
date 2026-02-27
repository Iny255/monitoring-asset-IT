@extends('layouts/contentNavbarLayout')

@section('title', 'Kategori')

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
                    <h5 style="color: navy">Data Kategori Barang</h5>
                </div>
                <div class="">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
                        Tambah Data Kategori
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ url('/dashboard/kategori') }}" class="row g-3 mb-4">
                    <div class="col-md-7 text">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1 me-2">
                                <input type="text" name="search" class="form-control w-100"
                                    placeholder="Cari berdasarkan nama barang" value="{{ request('search') }}">
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
                                <th width=>KODE BARANG</th>
                                <th>NAMA BARANG</th>
                                <th width=>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kategoris as $kategori)
                                <tr>
                                    <td>{{ $kategori->kode_barang }}</td>
                                    <td>{{ $kategori->nama_barang }}</td>

                                    <td>
                                        <button class="btn btn-warning btn-sm btn-edit" data-id="{{ $kategori->id }}"
                                            data-kode="{{ $kategori->kode_barang }}"
                                            data-nama="{{ $kategori->nama_barang }}">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>
                                        <form id="delete-form-{{ $kategori->id }}"
                                            action="{{ route('kategori.destroy', $kategori->id) }}" method="POST"
                                            style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $kategori->id }}">
                                            <i class="bx bx-trash"></i>
                                        </button>

                                    </td>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $kategoris->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL TAMBAH KATEGORI -->
        <div class="modal fade" id="modalTambahKategori" tabindex="-1">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">

                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-semibold">
                            Tambah Kategori Barang
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>
                    </div>

                    <div class="modal-body px-4">

                        <form action="{{ route('kategori.store') }}" method="POST">
                            @csrf

                            <!-- KODE BARANG -->
                            <div class="mb-3">
                                <label class="form-label fw-medium">
                                    Kode Barang
                                </label>
                                <input type="text" name="kode_barang" class="form-control"
                                    value="{{ $kodeBarang ?? '' }}" readonly>
                            </div>

                            <!-- NAMA BARANG -->
                            <div class="mb-3">
                                <label class="form-label fw-medium">
                                    Nama Barang
                                </label>
                                <input type="text" name="nama_barang"
                                    class="form-control @error('nama_barang') is-invalid @enderror"
                                    value="{{ old('nama_barang') }}" placeholder="Contoh: Laptop" required>

                                @error('nama_barang')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
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
        <!-- MODAL EDIT KATEGORI -->
        <div class="modal fade" id="modalEditKategori" tabindex="-1">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">

                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-semibold">
                            Edit Kategori Barang
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body px-4">

                        <form id="formEditKategori" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- KODE BARANG (READONLY) -->
                            <div class="mb-3">
                                <label class="form-label fw-medium">Kode Barang</label>
                                <input type="text" id="edit_kode_barang" class="form-control" readonly>
                            </div>

                            <!-- NAMA BARANG -->
                            <div class="mb-3">
                                <label class="form-label fw-medium">Nama Barang</label>
                                <input type="text" name="nama_barang" id="edit_nama_barang" class="form-control"
                                    required>
                            </div>

                            <div class="text-end mt-4">
                                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
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
        </div>
    @endsection
    <!--/ Striped Rows -->
    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                /* ================= DELETE ================= */
                document.querySelectorAll('.btn-delete').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.dataset.id;

                        Swal.fire({
                            title: 'Apakah kamu yakin?',
                            text: "Data kategori ini akan dihapus!",
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
                        const kode = this.dataset.kode;
                        const nama = this.dataset.nama;

                        // Isi form modal
                        document.getElementById('edit_kode_barang').value = kode;
                        document.getElementById('edit_nama_barang').value = nama;

                        // Set action form
                        document.getElementById('formEditKategori')
                            .action = `/dashboard/kategori/${id}`;

                        // Tampilkan modal
                        var modal = new bootstrap.Modal(document.getElementById('modalEditKategori'));
                        modal.show();
                    });
                });


                /* ================= AUTO OPEN MODAL TAMBAH JIKA ERROR ================= */
                @if ($errors->any())
                    var modalTambah = new bootstrap.Modal(document.getElementById('modalTambahKategori'));
                    modalTambah.show();
                @endif

            });
        </script>
    @endsection
