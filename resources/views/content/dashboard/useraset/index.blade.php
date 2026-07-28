@extends('layouts/contentNavbarLayout')

@section('title', 'Data Karyawan')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HERO HEADER --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-primary me-3">
                            <span class="avatar-initial rounded">
                                <i class="bx bx-user-check fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">Data Karyawan</h3>
                            <small class="text-muted">Kelola master data karyawan dan divisi penerima aset</small>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKaryawan">
                            <i class="bx bx-user-plus me-1"></i> Tambah Karyawan
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
                                @foreach ($perusahaans ?? \App\Models\Perusahaan::all() as $p)
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
                            placeholder="Cari nama / kode user aset..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-primary w-100">
                            <i class="bx bx-search"></i> Cari
                        </button>

                        <a href="{{ route('useraset.index') }}" class="btn btn-secondary w-100">
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
                                <th>UID</th>
                                <th>NAMA</th>
                                <th>JABATAN</th>
                                <th>DIVISI</th>

                                @if (auth()->user()->role == 'super_admin')

                                    @if (request('perusahaan_id'))
                                        <th>PERUSAHAAN</th>
                                    @else
                                        <th>DIGUNAKAN DI</th>
                                    @endif
                                @else
                                    <th>PERUSAHAAN</th>

                                @endif

                                @if (!(auth()->user()->role == 'super_admin' && empty(request('perusahaan_id'))))
                                    <th width="120">ACTION</th>
                                @endif

                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($karyawans as $index => $karyawan)
                                <tr>
                                    <td class="text-center">{{ $karyawans->firstItem() + $index }}</td>
                                    <td>{{ $karyawan->kode_karyawan }}</td>
                                    <td>{{ $karyawan->nama_karyawan }}</td>
                                    <td>{{ $karyawan->jabatan }}</td>
                                    <td>{{ $karyawan->divisi }}</td>
                                    @if (auth()->user()->role == 'super_admin')
                                        @if (request('perusahaan_id'))
                                            <td>
                                                {{ $karyawan->perusahaan->nama_perusahaan }}
                                            </td>
                                        @else
                                            <td>

                                                <span class="badge bg-label-primary btn-detail-karyawan"
                                                    style="cursor:pointer" data-kode="{{ $karyawan->kode_karyawan }}">

                                                    {{ $karyawan->total_perusahaan }} Perusahaan

                                                </span>

                                            </td>
                                        @endif
                                    @else
                                        <td>
                                            {{ $karyawan->perusahaan->nama_perusahaan ?? '-' }}
                                        </td>
                                    @endif
                                    @if (!(auth()->user()->role == 'super_admin' && empty(request('perusahaan_id'))))
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">

                                                {{-- EDIT --}}
                                                <button class="btn btn-warning btn-sm btn-edit"
                                                    data-id="{{ $karyawan->id }}"
                                                    data-kode="{{ $karyawan->kode_karyawan }}"
                                                    data-nama="{{ $karyawan->nama_karyawan }}"
                                                    data-jabatan="{{ $karyawan->jabatan }}"
                                                    data-divisi="{{ $karyawan->divisi }}"
                                                    data-perusahaan="{{ $karyawan->perusahaan->nama_perusahaan ?? '-' }}"
                                                    data-perusahaan_id="{{ $karyawan->id_perusahaan }}">
                                                    <i class="bx bx-edit-alt"></i>
                                                </button>

                                                {{-- DELETE --}}
                                                <form id="delete-form-{{ $karyawan->id }}"
                                                    action="{{ route('useraset.destroy', $karyawan->id) }}" method="POST"
                                                    style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                <button class="btn btn-danger btn-sm btn-delete"
                                                    data-id="{{ $karyawan->id }}">
                                                    <i class="bx bx-trash"></i>
                                                </button>

                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->role == 'super_admin' && empty(request('perusahaan_id')) ? 6 : 7 }}"
                                        class="text-center">Data tidak ditemukan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="mt-3">
                    {{ $karyawans->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>

        {{-- ================= MODAL TAMBAH ================= --}}
        <div class="modal fade" id="modalTambahKaryawan">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <form action="{{ route('useraset.store') }}" method="POST">
                        @csrf

                        <div class="modal-header">
                            <h5>Tambah User Aset</h5>
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">UID User Aset</label>
                                <input type="text" name="kode_karyawan" class="form-control mb-3" placeholder="Kode">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" name="nama_karyawan" class="form-control mb-3" placeholder="Nama">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jabatan</label>
                                <input type="text" name="jabatan" class="form-control mb-3" placeholder="Jabatan">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Divisi</label>
                                <input type="text" name="divisi" class="form-control mb-3" placeholder="Divisi">
                            </div>

                            @if (auth()->user()->role === 'super_admin')
                                <select name="id_perusahaan" class="form-select">
                                    <option value="">Pilih Perusahaan</option>
                                    @foreach ($perusahaans as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                    @endforeach
                                </select>
                            @endif

                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button class="btn btn-primary">Simpan</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        {{-- ================= MODAL EDIT ================= --}}
        <div class="modal fade" id="modalEditKaryawan">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <form id="formEditKaryawan" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title">Edit User Aset</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Kode</label>
                                <input type="text" id="edit_kode" name="kode_karyawan" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label>Nama</label>
                                <input type="text" id="edit_nama" name="nama_karyawan" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label>Jabatan</label>
                                <input type="text" id="edit_jabatan" name="jabatan" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label>Divisi</label>
                                <input type="text" id="edit_divisi" name="divisi" class="form-control">
                            </div>

                            @if (auth()->user()->role === 'super_admin')
                                <div class="mb-3">
                                    <label>Perusahaan</label>
                                    <select name="id_perusahaan" id="edit_perusahaan" class="form-select">
                                        @foreach ($perusahaans as $p)
                                            <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <div class="mb-3">
                                    <label>Perusahaan</label>
                                    <input type="text" id="edit_perusahaan_text" class="form-control" readonly>
                                </div>
                            @endif
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button class="btn btn-warning">Update</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
        <div class="modal fade" id="modalDetailKaryawan">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header bg-primary">

                        <h5 class="modal-title text-white">
                            Detail User Aset
                        </h5>

                        <button class="btn-close btn-close-white" data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body">

                        <h5 id="judulKaryawan"></h5>

                        <div class="table-responsive">

                            <table class="table table-bordered">

                                <thead>

                                    <tr>

                                        <th>No</th>
                                        <th>Perusahaan</th>
                                        <th width="150">Aksi</th>

                                    </tr>

                                </thead>

                                <tbody id="listKaryawan">

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>
            </div>
        </div>

        {{-- ================= SCRIPT ================= --}}
        @section('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    // ======================================
                    // DELETE
                    // ======================================
                    document.querySelectorAll('.btn-delete').forEach(btn => {

                        btn.onclick = function() {

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

                        }

                    });

                    // ======================================
                    // EDIT
                    // ======================================
                    document.querySelectorAll('.btn-edit').forEach(btn => {

                        btn.onclick = function() {

                            let id = this.dataset.id;

                            document.getElementById('edit_kode').value = this.dataset.kode;
                            document.getElementById('edit_nama').value = this.dataset.nama;
                            document.getElementById('edit_jabatan').value = this.dataset.jabatan;
                            document.getElementById('edit_divisi').value = this.dataset.divisi;

                            @if (auth()->user()->role === 'super_admin')
                                document.getElementById('edit_perusahaan').value =
                                    this.dataset.perusahaan_id;
                            @else
                                document.getElementById('edit_perusahaan_text').value =
                                    this.dataset.perusahaan;
                            @endif

                            document.getElementById('formEditKaryawan').action =
                                `/dashboard/useraset/${id}`;

                            new bootstrap.Modal(
                                document.getElementById('modalEditKaryawan')
                            ).show();

                        }

                    });

                    // ======================================
                    // DETAIL USER ASET
                    // ======================================
                    const modalDetail = new bootstrap.Modal(
                        document.getElementById('modalDetailKaryawan')
                    );

                    document.querySelectorAll('.btn-detail-karyawan').forEach(btn => {

                        btn.addEventListener('click', function() {

                            fetch(
                                    `/dashboard/useraset/detail-perusahaan?kode_karyawan=${encodeURIComponent(this.dataset.kode)}`
                                    )

                                .then(res => res.json())

                                .then(data => {

                                    document.getElementById('judulKaryawan').innerHTML =
                                        data[0].nama_karyawan;

                                    let html = '';

                                    data.forEach((item, index) => {

                                        html += `
                    <tr>

                        <td>${index+1}</td>

                        <td>${item.perusahaan.nama_perusahaan}</td>

                        <td class="text-center">

                            <button
                                type="button"
                                class="btn btn-warning btn-sm btn-edit-modal me-1"
                                data-id="${item.id}"
                                data-kode="${item.kode_karyawan}"
                                data-nama="${item.nama_karyawan}"
                                data-jabatan="${item.jabatan}"
                                data-divisi="${item.divisi}"
                                data-perusahaan="${item.perusahaan.nama_perusahaan}"
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

                                    document.getElementById('listKaryawan').innerHTML = html;

                                    // ======================================
                                    // EDIT DARI MODAL
                                    // ======================================
                                    document.querySelectorAll('.btn-edit-modal').forEach(btn => {

                                        btn.addEventListener('click', function() {

                                            document.getElementById('edit_kode').value =
                                                this.dataset.kode;

                                            document.getElementById('edit_nama').value =
                                                this.dataset.nama;

                                            document.getElementById('edit_jabatan')
                                                .value =
                                                this.dataset.jabatan;

                                            document.getElementById('edit_divisi')
                                                .value =
                                                this.dataset.divisi;

                                            @if (auth()->user()->role === 'super_admin')
                                                document.getElementById(
                                                        'edit_perusahaan').value =
                                                    this.dataset.perusahaan_id;
                                            @else
                                                document.getElementById(
                                                        'edit_perusahaan_text').value =
                                                    this.dataset.perusahaan;
                                            @endif

                                            document.getElementById('formEditKaryawan')
                                                .action =
                                                `/dashboard/useraset/${this.dataset.id}`;

                                            modalDetail.hide();

                                            new bootstrap.Modal(
                                                document.getElementById(
                                                    'modalEditKaryawan')
                                            ).show();

                                        });

                                    });

                                    // ======================================
                                    // DELETE DARI MODAL
                                    // ======================================
                                    document.querySelectorAll('.btn-delete-modal').forEach(btn => {

                                        btn.addEventListener('click', function() {

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

                                                    const form = document
                                                        .createElement('form');

                                                    form.method = 'POST';
                                                    form.action =
                                                        `/dashboard/useraset/${id}`;

                                                    form.innerHTML = `
                                    @csrf
                                    <input type="hidden" name="_method" value="DELETE">
                                `;

                                                    document.body.appendChild(
                                                        form);

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

                });
            </script>
        @endsection
    </div>

@endsection
