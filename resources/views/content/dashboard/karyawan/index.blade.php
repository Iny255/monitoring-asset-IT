@extends('layouts/contentNavbarLayout')

@section('title', 'Karyawan')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

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

        <div class="card">

            {{-- HEADER --}}
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-primary mb-0">Data Karyawan</h5>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKaryawan">
                    Tambah Data Karyawan
                </button>
            </div>

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
                        <input type="text" name="search" class="form-control" placeholder="Cari nama / kode karyawan..."
                            value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-primary w-100">
                            <i class="bx bx-search"></i> Cari
                        </button>

                        <a href="{{ route('karyawan.index') }}" class="btn btn-secondary w-100">
                            Reset
                        </a>
                    </div>

                </form>

                {{-- TABLE --}}
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th>KODE</th>
                                <th>NAMA</th>
                                <th>JABATAN</th>
                                <th>DIVISI</th>
                                <th>PERUSAHAAN</th>
                                <th width="120">ACTION</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($karyawans as $karyawan)
                                <tr>
                                    <td>{{ $karyawan->kode_karyawan }}</td>
                                    <td>{{ $karyawan->nama_karyawan }}</td>
                                    <td>{{ $karyawan->jabatan }}</td>
                                    <td>{{ $karyawan->divisi }}</td>
                                    <td>{{ $karyawan->perusahaan->nama_perusahaan ?? '-' }}</td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">

                                            <!-- EDIT -->
                                            <button class="btn btn-warning btn-sm btn-edit" data-id="{{ $karyawan->id }}"
                                                data-kode="{{ $karyawan->kode_karyawan }}"
                                                data-nama="{{ $karyawan->nama_karyawan }}"
                                                data-jabatan="{{ $karyawan->jabatan }}"
                                                data-divisi="{{ $karyawan->divisi }}"
                                                data-perusahaan="{{ $karyawan->perusahaan->nama_perusahaan ?? '-' }}"
                                                data-perusahaan_id="{{ $karyawan->id_perusahaan }}">
                                                <i class="bx bx-edit-alt"></i>
                                            </button>

                                            <!-- DELETE -->
                                            <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $karyawan->id }}">
                                                <i class="bx bx-trash"></i>
                                            </button>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Data tidak ditemukan</td>
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
    </div>


    {{-- ================= MODAL TAMBAH ================= --}}
    <div class="modal fade" id="modalTambahKaryawan">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <form action="{{ route('karyawan.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5>Tambah Karyawan</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <input type="text" name="kode_karyawan" class="form-control mb-3" placeholder="Kode">

                        <input type="text" name="nama_karyawan" class="form-control mb-3" placeholder="Nama">

                        <input type="text" name="jabatan" class="form-control mb-3" placeholder="Jabatan">

                        <input type="text" name="divisi" class="form-control mb-3" placeholder="Divisi">

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

                    <div class="modal-body">

                        <input type="text" id="edit_kode" name="kode_karyawan" class="form-control mb-2">
                        <input type="text" id="edit_nama" name="nama_karyawan" class="form-control mb-2">
                        <input type="text" id="edit_jabatan" name="jabatan" class="form-control mb-2">
                        <input type="text" id="edit_divisi" name="divisi" class="form-control mb-2">

                        @if (auth()->user()->role === 'super_admin')
                            <select name="id_perusahaan" id="edit_perusahaan" class="form-select">
                                @foreach ($perusahaans as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" id="edit_perusahaan_text" class="form-control" readonly>
                        @endif

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-warning">Update</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- ================= SCRIPT ================= --}}
    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // DELETE
                document.querySelectorAll('.btn-delete').forEach(btn => {
                    btn.onclick = function() {
                        if (confirm('Yakin hapus?')) {
                            document.getElementById('delete-form-' + this.dataset.id).submit();
                        }
                    }
                });

                // EDIT
                document.querySelectorAll('.btn-edit').forEach(btn => {
                    btn.onclick = function() {

                        let id = this.dataset.id;

                        document.getElementById('edit_kode').value = this.dataset.kode;
                        document.getElementById('edit_nama').value = this.dataset.nama;
                        document.getElementById('edit_jabatan').value = this.dataset.jabatan;
                        document.getElementById('edit_divisi').value = this.dataset.divisi;

                        @if (auth()->user()->role === 'super_admin')
                            document.getElementById('edit_perusahaan').value = this.dataset.perusahaan_id;
                        @else
                            document.getElementById('edit_perusahaan_text').value = this.dataset.perusahaan;
                        @endif

                        document.getElementById('formEditKaryawan').action =
                            `/dashboard/karyawan/${id}`;

                        new bootstrap.Modal(document.getElementById('modalEditKaryawan')).show();
                    }
                });

            });
        </script>
    @endsection

@endsection
