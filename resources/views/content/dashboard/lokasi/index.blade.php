@extends('layouts/contentNavbarLayout')

@section('title', 'Lokasi Penempatan')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HERO HEADER --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-primary me-3">
                            <span class="avatar-initial rounded">
                                <i class="bx bx-map-pin fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">Lokasi Penempatan</h3>
                            <small class="text-muted">Kelola master lokasi gedung, ruangan, dan cabang penempatan aset</small>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahLokasi">
                            <i class="bx bx-plus me-1"></i> Tambah Lokasi
                        </button>
                    </div>
                </div>
            </div>
        </div>

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

        <div class="card border-0 shadow-sm">
            {{-- BODY --}}
            <div class="card-body">

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
                        <input type="text" name="search" class="form-control" placeholder="Cari kode / nama lokasi..."
                            value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-primary w-100">
                            <i class="bx bx-search"></i> Cari
                        </button>
                        <a href="{{ route('lokasi.index') }}" class="btn btn-secondary w-100">
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

                                <th>NAMA LOKASI</th>

                                @if (auth()->user()->role == 'super_admin')

                                    @if (request('perusahaan_id'))
                                        <th>PERUSAHAAN</th>
                                    @else
                                        <th>DIGUNAKAN DI</th>
                                    @endif

                                @endif

                                @if (!(auth()->user()->role == 'super_admin' && empty(request('perusahaan_id'))))
                                    <th width="120">ACTION</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($lokasis as $index => $lokasi)
                                <tr>
                                    <td class="text-center">
                                        {{ $lokasis->firstItem() + $index }}
                                    </td>
                                    <td>{{ $lokasi->nama_lokasi }}</td>

                                    @if (auth()->user()->role == 'super_admin')
                                        @if (request('perusahaan_id'))
                                            <td>
                                                {{ $lokasi->perusahaan->nama_perusahaan }}
                                            </td>
                                        @else
                                            <td>
                                                <span class="badge bg-label-primary btn-detail-lokasi"
                                                    style="cursor:pointer" data-nama="{{ $lokasi->nama_lokasi }}">

                                                    {{ $lokasi->total_perusahaan }} Perusahaan

                                                </span>
                                            </td>
                                        @endif
                                    @endif

                                    @if (!(auth()->user()->role == 'super_admin' && empty(request('perusahaan_id'))))
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">

                                                {{-- EDIT --}}
                                                <button class="btn btn-warning btn-sm btn-edit"
                                                    data-id="{{ $lokasi->id }}" data-kode="{{ $lokasi->kode_lokasi }}"
                                                    data-nama="{{ $lokasi->nama_lokasi }}"
                                                    data-perusahaan_id="{{ $lokasi->id_perusahaan }}">
                                                    <i class="bx bx-edit-alt"></i>
                                                </button>

                                                {{-- DELETE --}}

                                                <form id="delete-form-{{ $lokasi->id }}"
                                                    action="{{ route('lokasi.destroy', $lokasi->id) }}" method="POST"
                                                    style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>

                                                <button class="btn btn-danger btn-sm btn-delete"
                                                    data-id="{{ $lokasi->id }}">
                                                    <i class="bx bx-trash"></i>
                                                </button>

                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Data tidak ditemukan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- MODAL TAMBAH --}}
                <div class="modal fade" id="modalTambahLokasi">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <form action="{{ route('lokasi.store') }}" method="POST">
                                @csrf

                                <div class="modal-header">
                                    <h5 class="modal-title">Tambah Lokasi</h5>
                                    <button class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

                                    {{-- SUPER ADMIN --}}
                                    @if (auth()->user()->role === 'super_admin')
                                        <div class="mb-3">
                                            <label>Perusahaan</label>
                                            <select name="id_perusahaan" id="perusahaanSelect" class="form-select" required>
                                                <option value="">-- pilih perusahaan --</option>
                                                @foreach ($perusahaans as $p)
                                                    <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif



                                    <div class="mb-3">
                                        <label>Nama Lokasi</label>
                                        <input type="text" name="nama_lokasi" class="form-control" required>
                                    </div>

                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button class="btn btn-primary">Simpan</button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
                {{-- MODAL EDIT --}}
                <div class="modal fade" id="modalEditLokasi">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <form id="formEditLokasi" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Lokasi</h5>
                                    <button class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

                                    {{-- SUPER ADMIN --}}
                                    @if (auth()->user()->role === 'super_admin')
                                        <div class="mb-3">
                                            <label>Perusahaan</label>
                                            <select name="id_perusahaan" id="edit_perusahaan" class="form-select">
                                                @foreach ($perusahaans as $p)
                                                    <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif



                                    <div class="mb-3">
                                        <label>Nama Lokasi</label>
                                        <input type="text" name="nama_lokasi" id="edit_nama" class="form-control"
                                            required>
                                    </div>

                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button class="btn btn-warning">Update</button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modalDetailLokasi">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">

                            <div class="modal-header bg-primary">
                                <h5 class="modal-title text-white">
                                    Detail Lokasi
                                </h5>

                                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">

                                <h5 id="judulLokasi"></h5>

                                <table class="table table-bordered">

                                    <thead>

                                        <tr>
                                            <th>No</th>
                                            <th>Perusahaan</th>
                                            <th width="150">Aksi</th>
                                        </tr>

                                    </thead>

                                    <tbody id="listLokasi">

                                    </tbody>

                                </table>

                            </div>

                        </div>
                    </div>
                </div>
                {{-- PAGINATION --}}
                <div class="mt-3">
                    {{ $lokasis->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>

@endsection


@section('scripts')
    <script>
        // DELETE
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.onclick = function() {

                let id = this.dataset.id;

                Swal.fire({
                    title: 'Yakin hapus?',
                    text: "Data tidak bisa dikembalikan!",
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

            }
        });


        // EDIT
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.onclick = function() {

                let id = this.dataset.id;

                document.getElementById('edit_nama').value = this.dataset.nama;


                @if (auth()->user()->role === 'super_admin')
                    document.getElementById('edit_perusahaan').value = this.dataset.perusahaan_id;
                @endif

                document.getElementById('formEditLokasi').action =
                    `/dashboard/lokasi/${id}`;

                new bootstrap.Modal(document.getElementById('modalEditLokasi')).show();
            }
        });
        // ======================================
// DETAIL PERUSAHAAN
// ======================================

const modalDetail = new bootstrap.Modal(
    document.getElementById('modalDetailLokasi')
);

document.querySelectorAll('.btn-detail-lokasi').forEach(btn => {

    btn.addEventListener('click', function () {

        fetch(
            `/dashboard/lokasi/detail-perusahaan?nama_lokasi=${encodeURIComponent(this.dataset.nama)}`
        )

        .then(res => res.json())

        .then(data => {

            document.getElementById('judulLokasi').innerHTML =
                data[0].nama_lokasi;

            let html = '';

            data.forEach((item, index) => {

                html += `
                    <tr>

                        <td>${index + 1}</td>

                        <td>${item.perusahaan.nama_perusahaan}</td>

                        <td class="text-center">

                            <button
                                type="button"
                                class="btn btn-warning btn-sm btn-edit-modal me-1"
                                data-id="${item.id}"
                                data-nama="${item.nama_lokasi}"
                                data-perusahaan_id="${item.id_perusahaan}">

                                <i class="bx bx-edit-alt"></i>

                            </button>

                            <button
                                type="button"
                                class="btn btn-danger btn-sm btn-delete-modal"
                                data-id="${item.id}">

                                <i class="bx bx-trash"></i>

                            </button>

                        </td>

                    </tr>
                `;

            });

            document.getElementById('listLokasi').innerHTML = html;

            // ==========================
            // EDIT DARI MODAL
            // ==========================
            document.querySelectorAll('.btn-edit-modal').forEach(btn => {

                btn.addEventListener('click', function () {

                    document.getElementById('edit_nama').value =
                        this.dataset.nama;

                    @if(auth()->user()->role === 'super_admin')
                        document.getElementById('edit_perusahaan').value =
                            this.dataset.perusahaan_id;
                    @endif

                    document.getElementById('formEditLokasi').action =
                        `/dashboard/lokasi/${this.dataset.id}`;

                    modalDetail.hide();

                    new bootstrap.Modal(
                        document.getElementById('modalEditLokasi')
                    ).show();

                });

            });

            // ==========================hhh
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
                    }).then((result) => {

                        if (result.isConfirmed) {

                            const form = document.createElement('form');

                            form.method = 'POST';
                            form.action = `/dashboard/lokasi/${id}`;

                            form.innerHTML = `
                                @csrf
                                <input type="hidden" name="_method" value="DELETE">
                            `;

                            document.body.appendChild(form);

                            form.submit();

                        } else {

                            modalDetail.show();

                        }

                    });

                });

            });

            modalDetail.show();

        });

    });

});
    </script>
@endsection
