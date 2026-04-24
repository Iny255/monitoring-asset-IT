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
            <h5 class="text-primary mb-0">Data Barang Masuk</h5>

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
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-7 d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Cari kode / nama barang"
                        value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </form>


            <div class="table-responsive">
                <table class="table table-bordered table-hover">

                    <thead class="table-primary text-center">
                        <tr>
                            <th>No</th>
                            <th>KODE MASUK</th>
                            <th>NAMA BARANG</th>
                            <th>TYPE</th>
                            <th>MEREK</th>
                            <th>JUMLAH</th>
                            <th>TANGGAL BELI</th>
                            <th>SUPPLIER</th>
                            <th>GAMBAR</th>

                            @auth
                                @if (in_array(auth()->user()->role, ['petugas', 'manager']))
                                    <th>AKSI</th>
                                @endif
                            @endauth
                        </tr>
                    </thead>


                    <tbody>
                        @forelse ($masuks as $index => $masuk)
                            <tr>

                                <td class="text-center">
                                    {{ $masuks->firstItem() + $index }}
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-label-primary">
                                        {{ $masuk->kode_masuk }}
                                    </span>
                                </td>

                                <td>
                                    {{ $masuk->kategori->nama_barang ?? '-' }}
                                </td>

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


                                {{-- AKSI --}}
                                @auth
                                    @if (auth()->user()->role === 'petugas')
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">

                                                {{-- DETAIL --}}
                                                <a href="{{ route('transaksi-masuk.show', $masuk->id) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                {{-- EDIT --}}
                                                <a href="{{ route('transaksi-masuk.edit', $masuk->id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="bx bx-edit-alt"></i>
                                                </a>

                                                {{-- DELETE --}}
                                                <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $masuk->id }}">
                                                    <i class="bx bx-trash"></i>
                                                </button>

                                                {{-- DOWNLOAD --}}
                                                @if ($masuk->gambar)
                                                    <a href="{{ route('transaksi-masuk.download', $masuk->id) }}"
                                                        class="btn btn-success btn-sm">
                                                        <i class="bx bx-download"></i>
                                                    </a>
                                                @endif

                                            </div>

                                            <form id="delete-form-{{ $masuk->id }}"
                                                action="{{ route('transaksi-masuk.destroy', $masuk->id) }}" method="POST"
                                                style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    @elseif (auth()->user()->role === 'manager')
                                        <td class="text-center">
                                            <a href="{{ route('manager.laporan.masuk.show', $masuk->id) }}"
                                                class="btn btn-info btn-sm">
                                                <i class="bx bx-show"></i>
                                            </a>
                                        </td>
                                    @endif
                                @endauth

                            </tr>

                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted">
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


{{-- SCRIPT --}}
@auth
    @if (auth()->user()->role === 'petugas')
        @section('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    document.querySelectorAll('.btn-delete').forEach(btn => {
                        btn.addEventListener('click', function() {

                            const id = this.dataset.id;

                            Swal.fire({
                                title: 'Yakin hapus data?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Ya',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    document.getElementById(`delete-form-${id}`).submit();
                                }
                            });

                        });
                    });

                });
            </script>
        @endsection
    @endif
@endauth
