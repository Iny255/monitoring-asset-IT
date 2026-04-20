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

    {{-- CARD --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="text-primary mb-0">Data Karyawan</h5>

            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKaryawan">
                Tambah Data Karyawan
            </button>
        </div>

        <div class="card-body">

            {{-- SEARCH --}}
            <form method="GET" class="row mb-4">
                <div class="col-md-6 d-flex">
                    <input type="text" name="search" class="form-control me-2"
                        placeholder="Cari nama / kode karyawan..." value="{{ request('search') }}">
                    <button class="btn btn-primary">Cari</button>
                </div>
            </form>

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>KODE</th>
                            <th>NAMA</th>
                            <th>JABATAN</th>
                            <th>DIVISI</th>
                            <th>PERUSAHAAN</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($karyawans as $karyawan)
                            <tr>
                                <td>{{ $karyawan->kode_karyawan }}</td>
                                <td>{{ $karyawan->nama_karyawan }}</td>
                                <td>{{ $karyawan->jabatan }}</td>
                                <td>{{ $karyawan->divisi }}</td>

                                {{-- ✅ FIX RELASI --}}
                                <td>
                                    {{ $karyawan->perusahaan->nama_perusahaan ?? '-' }}
                                </td>

                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm btn-edit"
                                        data-id="{{ $karyawan->id }}"
                                        data-kode="{{ $karyawan->kode_karyawan }}"
                                        data-nama="{{ $karyawan->nama_karyawan }}"
                                        data-jabatan="{{ $karyawan->jabatan }}"
                                        data-divisi="{{ $karyawan->divisi }}"
                                        data-perusahaan="{{ $karyawan->perusahaan->nama_perusahaan ?? '-' }}">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>

                                    <form id="delete-form-{{ $karyawan->id }}"
                                        action="{{ route('karyawan.destroy', $karyawan->id) }}"
                                        method="POST" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                    <button class="btn btn-danger btn-sm btn-delete"
                                        data-id="{{ $karyawan->id }}">
                                        <i class="bx bx-trash"></i>
                                    </button>
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
<div class="modal fade" id="modalTambahKaryawan" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('karyawan.store') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Karyawan</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Kode Karyawan</label>
                        <input type="text" name="kode_karyawan"
                            class="form-control @error('kode_karyawan') is-invalid @enderror"
                            value="{{ old('kode_karyawan') }}">
                        @error('kode_karyawan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label>Nama Karyawan</label>
                        <input type="text" name="nama_karyawan"
                            class="form-control @error('nama_karyawan') is-invalid @enderror"
                            value="{{ old('nama_karyawan') }}">
                    </div>

                    <div class="mb-3">
                        <label>Jabatan</label>
                        <input type="text" name="jabatan" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Divisi</label>
                        <input type="text" name="divisi" class="form-control">
                    </div>

                    {{-- ❌ PERUSAHAAN DIHAPUS (AUTO DARI LOGIN) --}}

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
<div class="modal fade" id="modalEditKaryawan" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form id="formEditKaryawan" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Karyawan</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="edit_id">

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

                    <div class="mb-3">
                        <label>Perusahaan</label>
                        <input type="text" id="edit_perusahaan" class="form-control" readonly>
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


{{-- ================= SCRIPT ================= --}}
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    /* DELETE */
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;

            if (confirm('Yakin hapus data?')) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    });

    /* EDIT */
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

            new bootstrap.Modal(document.getElementById('modalEditKaryawan')).show();
        });
    });

});
</script>
@endsection

@endsection