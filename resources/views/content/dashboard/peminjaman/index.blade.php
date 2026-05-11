@extends('layouts/contentNavbarLayout')

@section('title', 'Peminjaman')

@section('content')

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
        <div class="card-header">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                <h5 class="text-primary mb-0">
                    Data Peminjaman
                </h5>

                {{-- BUTTON --}}
                @if (auth()->user()->role === 'petugas' || auth()->user()->role === 'super_admin')
                    <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">

                        Tambah Pengajuan Peminjaman

                    </a>
                @endif

            </div>

        </div>

        <div class="card-body">

            {{-- ===================================== --}}
            {{-- FILTER --}}
            {{-- ===================================== --}}
            <form method="GET" action="{{ route('peminjaman.index') }}" class="row g-3 mb-4">

                {{-- SEARCH --}}
                <div class="col-md-4">

                    <input type="text" name="search" class="form-control" placeholder="Cari barang / karyawan..."
                        value="{{ request('search') }}">

                </div>

                {{-- FILTER PERUSAHAAN --}}
                @if (auth()->user()->role === 'super_admin')

                    <div class="col-md-3">

                        <select name="perusahaan" class="form-select">

                            <option value="">
                                Semua Perusahaan
                            </option>

                            @foreach ($perusahaans as $perusahaan)
                                <option value="{{ $perusahaan->id }}"
                                    {{ request('perusahaan') == $perusahaan->id ? 'selected' : '' }}>

                                    {{ $perusahaan->nama_perusahaan }}

                                </option>
                            @endforeach

                        </select>

                    </div>

                @endif

                {{-- FILTER STATUS --}}
                <div class="col-md-3">

                    <select name="status" class="form-select">

                        <option value="">
                            Semua Status
                        </option>

                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>

                            Pending

                        </option>

                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>

                            Disetujui

                        </option>

                        <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>

                            Dipinjam

                        </option>

                        <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>

                            Dikembalikan

                        </option>

                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>

                            Ditolak

                        </option>

                    </select>

                </div>

                {{-- BUTTON --}}
                <div class="col-md-2 d-grid">

                    <button type="submit" class="btn btn-primary">

                        Filter

                    </button>

                </div>

            </form>

            {{-- ===================================== --}}
            {{-- TABLE --}}
            {{-- ===================================== --}}
            <div class="table-responsive">

                <table class="table table-bordered align-middle">

                    <thead class="table-primary text-center">

                        <tr>

                            <th>
                                KODE BARANG
                            </th>

                            <th>
                                BARANG
                            </th>

                            {{-- SUPER ADMIN --}}
                            @if (auth()->user()->role === 'super_admin')
                                <th>
                                    PERUSAHAAN
                                </th>
                            @endif

                            <th>
                                KARYAWAN
                            </th>

                            <th>
                                TANGGAL PINJAM
                            </th>

                            <th>
                                RENCANA KEMBALI
                            </th>

                            <th>
                                STATUS
                            </th>

                            <th width="160">
                                AKSI
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($peminjamans as $p)
                            <tr>

                                {{-- KODE --}}
                                <td>

                                    {{ optional($p->keluar)->kode_barang ?? '-' }}

                                </td>

                                {{-- BARANG --}}
                                <td>

                                    {{ optional(optional(optional($p->keluar)->masuk)->kategori)->nama_barang ?? '-' }}

                                </td>

                                {{-- PERUSAHAAN --}}
                                @if (auth()->user()->role === 'super_admin')
                                    <td>

                                        @if ($p->tipe_peminjam === 'external')
                                            <span class="badge bg-danger">

                                                {{ $p->perusahaan_eksternal ?? '-' }}

                                            </span>
                                        @else
                                            <span class="badge bg-primary">

                                                {{ optional($p->perusahaan)->nama_perusahaan ?? '-' }}

                                            </span>
                                        @endif

                                    </td>
                                @endif

                                {{-- KARYAWAN --}}
                                <td>

                                    @if ($p->tipe_peminjam === 'external')
                                        {{ $p->nama_eksternal ?? '-' }}
                                    @else
                                        {{ optional($p->karyawan)->nama_karyawan ?? '-' }}
                                    @endif

                                </td>

                                {{-- TGL PINJAM --}}
                                <td>

                                    {{ $p->tanggal_pinjam ? \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d-m-Y') : '-' }}

                                </td>

                                {{-- TGL KEMBALI --}}
                                <td>

                                    {{ $p->tanggal_rencana_kembali ? \Carbon\Carbon::parse($p->tanggal_rencana_kembali)->format('d-m-Y') : '-' }}

                                </td>

                                {{-- STATUS --}}
                                <td class="text-center">

                                    @if ($p->status == 'pending')
                                        <span class="badge bg-warning">
                                            Pending
                                        </span>
                                    @elseif($p->status == 'disetujui')
                                        <span class="badge bg-info">
                                            Disetujui
                                        </span>
                                    @elseif($p->status == 'dipinjam')
                                        <span class="badge bg-primary">
                                            Dipinjam
                                        </span>
                                    @elseif($p->status == 'dikembalikan')
                                        <span class="badge bg-success">
                                            Dikembalikan
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            Ditolak
                                        </span>
                                    @endif

                                </td>

                                {{-- AKSI --}}
                                <td>

                                    <div class="d-flex justify-content-center gap-1 flex-nowrap">

                                        {{-- PETUGAS --}}
                                        @if (auth()->user()->role === 'petugas' || auth()->user()->role === 'super_admin')
                                            {{-- SHOW --}}
                                            <a href="{{ route('peminjaman.show', $p->id) }}" class="btn btn-info btn-sm">

                                                <i class="bx bx-show"></i>

                                            </a>

                                            {{-- EDIT --}}
                                            <button class="btn btn-warning btn-sm btn-edit" data-id="{{ $p->id }}">

                                                <i class="bx bx-edit-alt"></i>

                                            </button>

                                            {{-- DELETE --}}
                                            <form id="delete-form-{{ $p->id }}"
                                                action="{{ route('peminjaman.destroy', $p->id) }}" method="POST"
                                                style="display:none;">

                                                @csrf
                                                @method('DELETE')

                                            </form>

                                            <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $p->id }}">

                                                <i class="bx bx-trash"></i>

                                            </button>

                                            {{-- MANAGER --}}
                                        @elseif(auth()->user()->role === 'manager')
                                            <a href="{{ route('manager.laporan.peminjaman.show', $p->id) }}"
                                                class="btn btn-info btn-sm">

                                                <i class="bx bx-show"></i>

                                            </a>
                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="{{ auth()->user()->role === 'super_admin' ? 8 : 7 }}"
                                    class="text-center text-muted py-5">

                                    Belum ada data peminjaman

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- PAGINATION --}}
            <div class="mt-4">

                {{ $peminjamans->links('pagination::bootstrap-4') }}

            </div>

        </div>

    </div>

@endsection

@section('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // =====================================
            // DELETE
            // =====================================

            document.querySelectorAll('.btn-delete').forEach(btn => {

                btn.addEventListener('click', function() {

                    const id = this.dataset.id;

                    Swal.fire({

                        title: 'Hapus data?',
                        text: "Data peminjaman akan dihapus!",
                        icon: 'warning',

                        showCancelButton: true,

                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'

                    }).then((result) => {

                        if (result.isConfirmed) {

                            document
                                .getElementById(`delete-form-${id}`)
                                .submit();

                        }

                    });

                });

            });

            // =====================================
            // EDIT
            // =====================================

            document.querySelectorAll('.btn-edit').forEach(btn => {

                btn.addEventListener('click', function() {

                    const id = this.dataset.id;

                    Swal.fire({

                        title: 'Edit data?',
                        text: 'Kamu akan diarahkan ke halaman edit',
                        icon: 'question',

                        showCancelButton: true,

                        confirmButtonText: 'Ya, Edit',
                        cancelButtonText: 'Batal'

                    }).then((result) => {

                        if (result.isConfirmed) {

                            window.location.href =
                                `/dashboard/peminjaman/${id}/edit`;

                        }

                    });

                });

            });

        });
    </script>

@endsection
