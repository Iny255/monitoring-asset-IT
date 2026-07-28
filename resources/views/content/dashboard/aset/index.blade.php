@extends('layouts/contentNavbarLayout')

@section('title', 'Kategori Barang')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HERO HEADER --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-primary me-3">
                            <span class="avatar-initial rounded">
                                <i class="bx bx-category-alt fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">Kategori Barang</h3>
                            <small class="text-muted">Kelola master kategori barang (Laptop, Printer, PC, Monitor, dll)</small>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
                            <i class="bx bx-plus me-1"></i> Tambah Kategori
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ALERT --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                {{-- FILTER + SEARCH --}}
                <form method="GET" class="row g-3 align-items-end mb-4">
                    @if (auth()->user()->role === 'super_admin')
                        <div class="col-md-4">
                            <label class="form-label">Perusahaan</label>
                            <select name="perusahaan_id" class="form-select">
                                <option value="">Semua Perusahaan</option>
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
                        <label class="form-label">Pencarian</label>
                        <input type="text" name="search" class="form-control"
                            placeholder="Cari nama / kode barang..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-primary w-100">
                            <i class="bx bx-search"></i> Cari
                        </button>
                        <a href="{{ route('aset.index') }}" class="btn btn-secondary w-100">
                            Reset
                        </a>
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

                                @if (auth()->user()->role == 'super_admin')

                                    @if (request('perusahaan_id'))
                                        <th>PERUSAHAAN</th>
                                    @else
                                        <th>DIGUNAKAN DI</th>
                                    @endif

                                @endif

                                @if (!(auth()->user()->role == 'super_admin' && empty(request('perusahaan_id'))))
                                    <th width="150">ACTION</th>
                                @endif
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

                                    @if (auth()->user()->role == 'super_admin')
                                        @if (request('perusahaan_id'))
                                            <td>
                                                {{ $kategori->perusahaan->nama_perusahaan }}
                                            </td>
                                        @else
                                            <td>
                                                <span class="badge bg-label-primary btn-detail-perusahaan"
                                                    style="cursor: pointer;" data-kode="{{ $kategori->kode_barang }}"
                                                    data-nama="{{ $kategori->nama_barang }}"
                                                    data-total="{{ $kategori->total_perusahaan }}"
                                                    title="Klik untuk melihat daftar perusahaan">

                                                    {{ $kategori->total_perusahaan }} Perusahaan

                                                </span>
                                            </td>
                                        @endif
                                    @endif


                                    @if (!(auth()->user()->role == 'super_admin' && empty(request('perusahaan_id'))))
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
                                    @endif
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
    <!-- ================= MODAL DETAIL PERUSAHAAN ================= -->
    <div class="modal fade" id="modalDetailPerusahaan" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">
                        Detail Penggunaan Aset
                    </h5>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">

                    <h5 id="judulBarang" class="mb-3"></h5>

                    <div class="table-responsive">

                        <table class="table table-bordered">

                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Perusahaan</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>

                            <tbody id="listPerusahaan">

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ==========================
    // DELETE (HALAMAN UTAMA)
    // ==========================
    document.querySelectorAll('.btn-delete').forEach(btn => {

        btn.addEventListener('click', function () {

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
    // EDIT (HALAMAN UTAMA)
    // ==========================
    const modalEdit = document.getElementById('modalEditKategori');
    const formEdit = document.getElementById('formEditKategori');
    const inputEditKode = document.getElementById('edit_kode_barang');
    const inputEditNama = document.getElementById('edit_nama_barang');

    document.querySelectorAll('.btn-edit').forEach(btn => {

        btn.addEventListener('click', function () {

            inputEditKode.value = this.dataset.kode;
            inputEditNama.value = this.dataset.nama;

            formEdit.action = `/dashboard/aset/${this.dataset.id}`;

            new bootstrap.Modal(modalEdit).show();

        });

    });

    // ==========================
    // MODAL DETAIL
    // ==========================
    const modalDetail = new bootstrap.Modal(
        document.getElementById('modalDetailPerusahaan')
    );

    document.querySelectorAll('.btn-detail-perusahaan').forEach(btn => {

        btn.addEventListener('click', function () {

            fetch(`/dashboard/aset/detail-perusahaan?kode_barang=${encodeURIComponent(this.dataset.kode)}&nama_barang=${encodeURIComponent(this.dataset.nama)}`)

            .then(res => res.json())

            .then(data => {

                document.getElementById('judulBarang').innerHTML = data[0].nama_barang;

                let html = '';

                data.forEach((item,index)=>{

                    html += `
                    <tr>

                        <td>${index+1}</td>

                        <td>${item.perusahaan.nama_perusahaan}</td>

                        <td class="text-center">

                            <button
                                class="btn btn-warning btn-sm btn-edit-modal me-1"
                                data-id="${item.id}"
                                data-kode="${item.kode_barang}"
                                data-nama="${item.nama_barang}">

                                <i class="bx bx-edit-alt"></i>

                            </button>

                            <button
                                class="btn btn-danger btn-sm btn-delete-modal"
                                data-id="${item.id}">

                                <i class="bx bx-trash"></i>

                            </button>

                        </td>

                    </tr>
                    `;

                });

                document.getElementById('listPerusahaan').innerHTML = html;

                // ==========================
                // EDIT DARI MODAL
                // ==========================
                document.querySelectorAll('.btn-edit-modal').forEach(btn => {

                    btn.addEventListener('click', function () {

                        inputEditKode.value = this.dataset.kode;
                        inputEditNama.value = this.dataset.nama;

                        formEdit.action = `/dashboard/aset/${this.dataset.id}`;

                        modalDetail.hide();

                        new bootstrap.Modal(modalEdit).show();

                    });

                });

                // ==========================
                // DELETE DARI MODAL
                // ==========================
                document.querySelectorAll('.btn-delete-modal').forEach(btn => {

                    btn.addEventListener('click', function () {

                        let id = this.dataset.id;

                        modalDetail.hide();

                        Swal.fire({
                            title: 'Yakin hapus?',
                            text: 'Data tidak bisa dikembalikan!',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#696cff',
                            cancelButtonColor: '#8592a3',
                            confirmButtonText: 'Ya, hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result)=>{

                            if(result.isConfirmed){

                                const form = document.createElement('form');

                                form.method = 'POST';
                                form.action = `/dashboard/aset/${id}`;

                                form.innerHTML = `
                                    @csrf
                                    <input type="hidden" name="_method" value="DELETE">
                                `;

                                document.body.appendChild(form);

                                form.submit();

                            }else{

                                modalDetail.show();

                            }

                        });

                    });

                });

                modalDetail.show();

            });

        });

    });

});
</script>
@endsection