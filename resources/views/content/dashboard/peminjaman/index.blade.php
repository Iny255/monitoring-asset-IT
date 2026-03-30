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
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="text-primary mb-0">Data Peminjaman</h5>

                {{-- PETUGAS SAJA --}}
                @if (auth()->user()->role === 'petugas')
                    <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">
                        Tambah Pengajuan Peminjaman
                    </a>
                @endif
            </div>
        </div>

        <div class="card-body">

            {{-- SEARCH --}}
            <form method="GET" action="{{ route('peminjaman.index') }}" class="row g-2 mb-4">
                <div class="col-md-6 d-flex">
                    <input type="text" name="search" class="form-control me-2"
                        placeholder="Cari berdasarkan nama barang" value="{{ request('search') }}">

                    <button type="submit" class="btn btn-primary px-4">
                        Cari
                    </button>
                </div>
            </form>

            {{-- TABLE --}}
            <div class="table-responsive">

                <table class="table table-bordered">

                    <thead class="table-primary text-center">
                        <tr>
                            <th>KODE BARANG</th>
                            <th>BARANG</th>
                            <th>KARYAWAN</th>
                            <th>TANGGAL PINJAM</th>
                            <th>RENCANA KEMBALI</th>
                            <th>STATUS</th>
                            <th style="width:160px">AKSI</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($peminjamans as $p)
                            <tr>

                                <td>
                                    {{ optional($p->keluar)->kode_barang ?? '-' }}
                                </td>

                                <td>
                                    {{ optional(optional(optional($p->keluar)->masuk)->kategori)->nama_barang ?? '-' }}
                                </td>

                                <td>
                                    {{ optional($p->karyawan)->nama_karyawan ?? '-' }}
                                </td>

                                <td>
                                    {{ $p->tanggal_pinjam ? \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d-m-Y') : '-' }}
                                </td>

                                <td>
                                    {{ $p->tanggal_rencana_kembali ? \Carbon\Carbon::parse($p->tanggal_rencana_kembali)->format('d-m-Y') : '-' }}
                                </td>

                                <td class="text-center">

                                    @if ($p->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($p->status == 'disetujui')
                                        <span class="badge bg-info">Disetujui</span>
                                    @elseif($p->status == 'dipinjam')
                                        <span class="badge bg-primary">Dipinjam</span>
                                    @elseif($p->status == 'dikembalikan')
                                        <span class="badge bg-success">Dikembalikan</span>
                                    @else
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif

                                </td>

                                <td>

                                    <div class="d-flex justify-content-center gap-1 flex-nowrap">

                                        {{-- ================= PETUGAS ================= --}}
                                        @if (auth()->user()->role === 'petugas')
                                            <a href="{{ route('peminjaman.show', $p->id) }}" class="btn btn-info btn-sm">
                                                <i class="bx bx-show"></i>
                                            </a>

                                            <button class="btn btn-warning btn-sm btn-edit" data-id="{{ $p->id }}">
                                                <i class="bx bx-edit-alt"></i>
                                            </button>

                                            <form id="delete-form-{{ $p->id }}"
                                                action="{{ route('peminjaman.destroy', $p->id) }}" method="POST"
                                                style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>

                                            <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $p->id }}">
                                                <i class="bx bx-trash"></i>
                                            </button>

                                            {{-- ================= MANAGER ================= --}}
                                        @elseif(auth()->user()->role === 'manager')
                                            <a href="{{ route('laporan.peminjaman.show', $p->id) }}"
                                                class="btn btn-info btn-sm">
                                                <i class="bx bx-show"></i> Detail
                                            </a>
                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada data peminjaman
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>

                {{-- PAGINATION --}}
                <div class="mt-4">
                    {{ $peminjamans->links('pagination::bootstrap-4') }}
                </div>

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
                            document.getElementById(`delete-form-${id}`).submit();
                        }

                    });

                });

            });


            // EDIT
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
                            window.location.href = `/dashboard/peminjaman/${id}/edit`;
                        }

                    });

                });

            });

        });
    </script>

@endsection
