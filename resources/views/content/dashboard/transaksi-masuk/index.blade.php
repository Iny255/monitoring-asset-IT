@extends('layouts/contentNavbarLayout')

@section('title', 'Transaksi Masuk')

@section('content')

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


    <div class="card">

        {{-- HEADER --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0" style="color: navy">Data Barang Masuk</h5>

            {{-- TAMBAH DATA (PETUGAS ONLY) --}}
            @auth
                @if (auth()->user()->role === 'petugas')
                    <a href="{{ route('transaksi-masuk.create') }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus"></i> Tambah Data
                    </a>
                @endif
            @endauth
        </div>


        <div class="card-body">

            {{-- SEARCH --}}
            <form method="GET" action="{{ request()->url() }}" class="row g-3 mb-4">
                <div class="col-md-7 d-flex">
                    <input type="text" name="search" class="form-control me-2"
                        placeholder="Cari berdasarkan nama barang" value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </form>


            <div class="table-responsive">
                <table class="table table-bordered table-hover">

                    {{-- HEADER TABLE --}}
                    <thead class="table-primary text-center">
                        <tr>
                            <th>KODE MASUK</th>
                            <th>NAMA BARANG</th>
                            <th>TYPE</th>
                            <th>MEREK</th>
                            <th>JUMLAH</th>
                            <th>TANGGAL BELI</th>
                            <th>SUPPLIER</th>
                            <th>GAMBAR</th>

                            {{-- AKSI PETUGAS & MANAGER --}}
                            @auth
                                @if (in_array(auth()->user()->role, ['petugas', 'manager']))
                                    <th width="160">AKSI</th>
                                @endif
                            @endauth
                        </tr>
                    </thead>


                    <tbody>
                        @forelse ($masuks as $masuk)
                            <tr>

                                <td class="text-center">
                                    <span class="badge bg-label-primary">{{ $masuk->kode_masuk }}</span>
                                </td>

                                <td>{{ $masuk->kategori->nama_barang ?? '-' }}</td>
                                <td>{{ $masuk->type }}</td>
                                <td>{{ $masuk->merek }}</td>

                                <td class="text-center">{{ $masuk->jumlah }}</td>

                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($masuk->tgl_beli)->format('d-m-Y') }}
                                </td>

                                <td>{{ $masuk->supplier }}</td>

                                {{-- GAMBAR --}}
                                <td class="text-center">
                                    @if ($masuk->gambar)
                                        <img src="{{ asset('storage/' . $masuk->gambar) }}" width="60"
                                            class="img-thumbnail">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>


                                {{-- ================= AKSI ================= --}}
                                @auth
                                    @if (auth()->user()->role === 'petugas')
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">

                                                {{-- SHOW --}}
                                                <a href="{{ route('transaksi-masuk.show', $masuk->id) }}"
                                                    class="btn btn-info btn-sm" title="Detail">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                {{-- EDIT --}}
                                                <button class="btn btn-warning btn-sm btn-edit" data-id="{{ $masuk->id }}"
                                                    title="Edit">
                                                    <i class="bx bx-edit-alt"></i>
                                                </button>

                                                {{-- DELETE --}}
                                                <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $masuk->id }}"
                                                    title="Hapus">
                                                    <i class="bx bx-trash"></i>
                                                </button>

                                                {{-- DOWNLOAD --}}
                                                @if ($masuk->gambar)
                                                    <a href="{{ route('transaksi-masuk.download', $masuk->id) }}"
                                                        class="btn btn-success btn-sm" title="Download Gambar">
                                                        <i class="bx bx-download"></i>
                                                    </a>
                                                @endif

                                            </div>

                                            {{-- FORM DELETE --}}
                                            <form id="delete-form-{{ $masuk->id }}"
                                                action="{{ route('transaksi-masuk.destroy', $masuk->id) }}" method="POST"
                                                style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>

                                        {{-- MANAGER → SHOW ONLY --}}
                                    @elseif (auth()->user()->role === 'manager')
                                        <td class="text-center">
                                            <a href="{{ route('laporan.masuk.show', $masuk->id) }}"
                                                class="btn btn-info btn-sm">
                                                <i class="bx bx-show"></i> Detail
                                            </a>
                                        </td>
                                    @endif
                                @endauth

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    Data barang masuk belum ada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- PAGINATION --}}
                <div class="mt-4">
                    {{ $masuks->links('pagination::bootstrap-4') }}
                </div>

            </div>
        </div>
    </div>

@endsection



{{-- ================= SCRIPT PETUGAS ONLY ================= --}}
@auth
    @if (auth()->user()->role === 'petugas')
        @section('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    // DELETE
                    document.querySelectorAll('.btn-delete').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const id = this.dataset.id;

                            Swal.fire({
                                title: 'Apakah kamu yakin?',
                                text: "Data akan dihapus!",
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
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, Edit',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = `/dashboard/transaksi-masuk/${id}/edit`;
                                }
                            });
                        });
                    });

                });
            </script>
        @endsection
    @endif
@endauth
