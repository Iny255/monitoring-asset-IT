@extends('layouts/contentNavbarLayout')

@section('title', 'Aset')

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
                <h5 class="mb-0 fw-semibold text-primary">Data Aset</h5>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
                    <i class="bx bx-plus"></i> Tambah Data
                </button>
            </div>

            {{-- BODY --}}
            <div class="card-body p-4">
                @if (auth()->user()->role === 'super_admin')
                    <form method="GET" class="row mb-3">

                        <div class="col-md-4">
                            <select name="perusahaan_id" class="form-control">
                                <option value="">-- Semua Perusahaan --</option>

                                @foreach ($perusahaans as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button class="btn btn-primary">Filter</button>
                        </div>

                    </form>
                @endif

                {{-- SEARCH --}}
                <form method="GET" class="row mb-4">
                    <div class="col-md-6 d-flex">
                        <input type="text" name="search" class="form-control me-2"
                            placeholder="Cari nama / kode barang..." value="{{ request('search') }}">
                        <button class="btn btn-primary">Cari</button>
                    </div>
                </form>

                {{-- TABLE --}}
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-primary text-center">
                            <tr>
                                <th width="60">NO</th>
                                <th width="120">KODE</th>
                                <th>NAMA BARANG</th>

                                @if (auth()->user()->role === 'super_admin')
                                    <th>PERUSAHAAN</th>
                                @endif

                                <th width="150">ACTION</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($kategoris as $index => $kategori)
                                <tr>
                                    <td class="text-center">{{ $kategoris->firstItem() + $index }}</td>
                                    <td>
                                        <span class="badge bg-label-primary">
                                            {{ $kategori->kode_barang }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $kategori->nama_barang }}
                                    </td>

                                    @if (auth()->user()->role === 'super_admin')
                                        <td>{{ $kategori->perusahaan->nama_perusahaan ?? '-' }}</td>
                                    @endif


                                    <td class="text-center">

                                        <button type="button" class="btn btn-warning btn-sm btn-edit"
                                            data-id="{{ $kategori->id }}" data-kode="{{ $kategori->kode_barang }}"
                                            data-nama="{{ $kategori->nama_barang }}">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>

                                        <form id="delete-form-{{ $kategori->id }}"
                                            action="{{ route('aset.destroy', $kategori->id) }}" method="POST"
                                            style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <button type="button" class="btn btn-danger btn-sm btn-delete"
                                            data-id="{{ $kategori->id }}">
                                            <i class="bx bx-trash"></i>
                                        </button>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Data tidak ditemukan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="mt-3">
                    {{ $kategoris->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>

    {{-- ================= MODAL TAMBAH ================= --}}
    <div class="modal fade" id="modalTambahKategori">
        <div class="modal-dialog">
            <div class="modal-content">


                <form action="{{ route('aset.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Aset</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        @if (auth()->user()->role === 'super_admin')
                            <div class="mb-3">
                                <label class="form-label">
                                    Perusahaan
                                </label>

                                <select name="perusahaan_id" class="form-control" required>

                                    <option value="">
                                        -- Pilih Perusahaan --
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
                            <label class="form-label">
                                Kode Barang
                            </label>

                            <input type="text" name="kode_barang" class="form-control text-uppercase" maxlength="10"
                                placeholder="Contoh : L, P, T, SV" required>

                            <small class="text-muted">
                                Digunakan sebagai kode kategori aset
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Nama Barang
                            </label>

                            <input type="text" name="nama_barang" class="form-control" placeholder="Contoh : Laptop"
                                required>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">Simpan</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <!-- ================= MODAL EDIT ================= -->
    <div class="modal fade" id="modalEditKategori" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="formEditKategori" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Aset</h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">
                                Kode Barang
                            </label>

                            <input type="text" id="edit_kode_barang" name="kode_barang" class="form-control"
                                maxlength="10" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Nama Barang
                            </label>

                            <input type="text" id="edit_nama_barang" name="nama_barang" class="form-control"
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

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ==========================
            // DELETE
            // ==========================
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

            // ==========================
            // EDIT
            // ==========================
            const modalEdit = document.getElementById('modalEditKategori');
            const formEdit = document.getElementById('formEditKategori');
            const inputEditKode = document.getElementById('edit_kode_barang');
            const inputEditNama = document.getElementById('edit_nama_barang');

            document.querySelectorAll('.btn-edit').forEach(btn => {

                btn.addEventListener('click', function() {

                    let id = this.dataset.id;

                    inputEditKode.value = this.dataset.kode;
                    inputEditNama.value = this.dataset.nama;

                    formEdit.action = `/dashboard/aset/${id}`;

                    new bootstrap.Modal(modalEdit).show();

                });

            });

        });
    </script>
@endsection
