@extends('layouts/contentNavbarLayout')

@section('title', 'Transaksi Keluar')

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">

        {{-- HEADER --}}
        <div class="card-header bg-white border-0 py-3 px-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                <h5 class="mb-0 fw-semibold text-primary">
                    Data Barang Keluar
                </h5>

                <div class="d-flex gap-2">

                    @if (auth()->user()->role === 'petugas')
                        <a href="{{ route('transaksi-keluar.create') }}" class="btn btn-primary btn-sm px-3">
                            <i class="bx bx-plus"></i> Tambah Data
                        </a>
                    @endif

                    @if (auth()->user()->role == 'manager')
                        <a href="{{ route('manager.laporan.stok') }}" class="btn btn-info btn-sm px-3">
                            Cek Stok
                        </a>
                    @else
                        <a href="{{ route('masuk.stok') }}" class="btn btn-info btn-sm px-3">
                            Cek Stok
                        </a>
                    @endif

                </div>
            </div>
        </div>

        {{-- BODY --}}
        <div class="card-body px-4 pt-3 pb-4">

            {{-- SEARCH --}}
            <form method="GET" action="{{ route('transaksi-keluar.index') }}" class="row g-2 mb-4">
                <div class="col-md-6 d-flex">
                    <input type="text" name="search" class="form-control me-2"
                        placeholder="Cari berdasarkan nama barang" value="{{ request('search') }}">

                    <button type="submit" class="btn btn-primary px-4">
                        Cari
                    </button>
                </div>
            </form>


            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>KODE KELUAR</th>
                            <th>NAMA BARANG</th>
                            <th>TYPE</th>
                            <th>NAMA KARYAWAN</th>
                            <th>DIVISI</th>
                            <th>JUMLAH</th>
                            <th width="120">AKSI</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($keluars as $keluar)
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-label-primary">
                                        {{ $keluar->kode_keluar }}
                                    </span>
                                </td>

                                {{-- NAMA BARANG --}}
                                <td>{{ optional(optional($keluar->masuk)->kategori)->nama_barang ?? '-' }}</td>

                                {{-- TYPE --}}
                                <td>{{ optional($keluar->masuk)->type ?? '-' }}</td>

                                {{-- NAMA KARYAWAN --}}
                                <td>
                                    @if ($keluar->jenis_penerima == 'Perorangan')
                                        {{ optional($keluar->karyawan)->nama_karyawan ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- DIVISI --}}
                                <td>
                                    @if ($keluar->jenis_penerima == 'Perorangan')
                                        {{ optional($keluar->karyawan)->divisi ?? '-' }}
                                    @elseif($keluar->jenis_penerima == 'Perdivisi')
                                        {{ $keluar->divisi_klr ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>



                                {{-- JUMLAH --}}
                                <td class="text-center">{{ $keluar->jumlah }}</td>

                                {{-- AKSI --}}
                                <td class="text-center">

                                    {{-- PETUGAS : FULL AKSI --}}
                                    @if (auth()->user()->role === 'petugas')
                                        <a href="{{ route('transaksi-keluar.show', $keluar->id) }}"
                                            class="btn btn-info btn-sm">
                                            <i class="bx bx-show"></i>
                                        </a>

                                        <button class="btn btn-warning btn-sm btn-edit" data-id="{{ $keluar->id }}">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>

                                        <form id="delete-form-{{ $keluar->id }}"
                                            action="{{ route('transaksi-keluar.destroy', $keluar->id) }}" method="POST"
                                            style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $keluar->id }}">
                                            <i class="bx bx-trash"></i>
                                        </button>

                                        {{-- MANAGER : SHOW ONLY --}}
                                    @elseif(auth()->user()->role === 'manager')
                                        <a href="{{ route('laporan.keluar.show', $keluar->id) }}"
                                            class="btn btn-info btn-sm">
                                            <i class="bx bx-show"></i> Detail
                                        </a>
                                    @endif

                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    Data barang keluar belum ada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>

                <div class="mt-4">
                    {{ $keluars->links('pagination::bootstrap-4') }}
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
                        title: 'Apakah kamu yakin?',
                        text: "Data transaksi keluar ini akan dihapus!",
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
            // EDIT
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;

                    Swal.fire({
                        title: 'Edit data ini?',
                        text: 'Kamu akan diarahkan ke halaman edit',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Edit',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `/dashboard/transaksi-keluar/${id}/edit`;
                        }
                    });
                });
            });

        });
    </script>
@endsection
