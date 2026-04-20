@extends('layouts/contentNavbarLayout')

@section('title', 'Stok Barang')

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="text-primary mb-0">Stok Barang</h5>

        <div class="d-flex gap-2">

            {{-- Tombol kembali hanya untuk PETUGAS --}}
            @if(auth()->user()->role == 'petugas')
                <a href="{{ route('transaksi-keluar.index') }}" class="btn btn-secondary btn-sm">
                    Kembali
                </a>
            @endif

            {{-- Tombol CETAK hanya untuk MANAGER
            @if(auth()->user()->role == 'manager')
                <a href="{{ route('manager.cetak.stok') }}" target="_blank" class="btn btn-primary btn-sm">
                    <i class="bi bi-printer"></i> Cetak
                </a>
            @endif --}}

        </div>
    </div>

    <div class="card-body">

        {{-- SEARCH --}}
        <form method="GET" 
              action="{{ auth()->user()->role == 'manager' ? route('manager.laporan.stok') : route('transaksi-masuk.stok') }}" 
              class="row g-3 mb-4">

            <div class="col-md-7 d-flex">
                <input type="text" name="search" class="form-control me-2"
                    placeholder="Cari berdasarkan nama barang"
                    value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">Cari</button>
            </div>

        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-primary text-center">
                    <tr>
                        <th>NO</th>
                        <th>NAMA BARANG</th>
                        <th>TYPE</th>
                        <th>MEREK</th>
                        <th>STOK</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stoks as $index => $stok)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $stok->kategori->nama_barang ?? '-' }}</td>
                        <td>{{ $stok->type }}</td>
                        <td>{{ $stok->merek }}</td>
                        <td class="text-center">
                            <span class="badge bg-success">
                                {{ $stok->jumlah }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Data stok belum tersedia
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

@endsection
