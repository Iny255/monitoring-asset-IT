@extends('layouts/contentNavbarLayout')

@section('title', 'Kategori')

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
                <h5 class="mb-0 fw-semibold text-primary">Data Kategori Barang</h5>

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
                                    <option value="{{ $p->id }}">
                                        {{ $p->nama_perusahaan }}
                                    </option>
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
                                <th>KODE</th>
                                <th>NAMA BARANG</th>

                                @if (auth()->user()->role === 'super_admin')
                                    <th>PERUSAHAAN</th>
                                @endif

                                <th>ACTION</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($kategoris as $kategori)
                                <tr>
                                    <td>{{ $kategori->kode_barang }}</td>
                                    <td>{{ $kategori->nama_barang }}</td>

                                    @if (auth()->user()->role === 'super_admin')
                                        <td>
                                            {{ $kategori->perusahaan->nama_perusahaan ?? '-' }}
                                        </td>
                                    @endif

                                    <td class="text-center">
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">
                                        Data tidak ditemukan
                                    </td>
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

                <form action="{{ route('kategori.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        {{-- SUPER ADMIN --}}
                        @if (auth()->user()->role === 'super_admin')
                            <div class="mb-3">
                                <label class="form-label">Perusahaan</label>
                                <select name="perusahaan_id" id="perusahaanSelect" class="form-control" required>
                                    <option value="">-- pilih perusahaan --</option>
                                    @foreach ($perusahaans as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Kode Barang</label>
                            <input type="text" id="kodeBarang" name="kode_barang" class="form-control" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" required>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">Simpan</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- ================= MODAL EDIT ================= --}}
    <div class="modal fade" id="modalEditKategori">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="formEditKategori" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Kode Barang</label>
                            <input type="text" id="edit_kode_barang" class="form-control" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" name="nama_barang" id="edit_nama_barang" class="form-control" required>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">Update</button>
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
                    if (confirm('Yakin hapus data?')) {
                        document.getElementById('delete-form-' + this.dataset.id).submit();
                    }
                });
            });

            // EDIT
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {

                    document.getElementById('edit_kode_barang').value = this.dataset.kode;
                    document.getElementById('edit_nama_barang').value = this.dataset.nama;

                    document.getElementById('formEditKategori').action =
                        `/dashboard/kategori/${this.dataset.id}`;

                    new bootstrap.Modal(document.getElementById('modalEditKategori')).show();
                });
            });

        });


        document.addEventListener('DOMContentLoaded', function() {

            const btnTambah = document.querySelector('[data-bs-target="#modalTambahKategori"]');

            if (!btnTambah) return;

            btnTambah.addEventListener('click', function() {

                const kodeInput = document.getElementById('kodeBarang');
                const select = document.getElementById('perusahaanSelect');

                // reset
                if (kodeInput) kodeInput.value = '';

                // ===============================
                // 👤 PETUGAS
                // ===============================
                if (!select && kodeInput) {

                   let perusahaanId = @json(auth()->user()->id_perusahaan);

                    if (!perusahaanId) {
                        console.log('Perusahaan kosong!');
                        return;
                    }

                    fetch(`/get-kode-kategori/${perusahaanId}`)
                        .then(res => res.json())
                        .then(data => {
                            kodeInput.value = data.kode;
                        });
                }

                // ===============================
                // 👑 SUPER ADMIN
                // ===============================
                if (select) {

                    select.onchange = function() {

                        let perusahaanId = this.value;

                        if (!perusahaanId) {
                            kodeInput.value = '';
                            return;
                        }

                        fetch(`/get-kode-kategori/${perusahaanId}`)
                            .then(res => res.json())
                            .then(data => {
                                kodeInput.value = data.kode;
                            });
                    };

                }

            });

        });
    </script>

@endsection
