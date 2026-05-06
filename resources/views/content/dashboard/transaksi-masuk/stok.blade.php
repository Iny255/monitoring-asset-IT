@extends('layouts/contentNavbarLayout')

@section('title', 'Stok Barang')

@section('content')

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="text-primary mb-0">
                Stok Barang
            </h5>

            <div class="d-flex gap-2">

                {{-- PETUGAS --}}
                @if (auth()->user()->role == 'petugas')
                    <a href="{{ route('transaksi-keluar.index') }}" class="btn btn-secondary btn-sm">
                        Kembali
                    </a>
                @endif

            </div>

        </div>

        <div class="card-body">

            {{-- FILTER + SEARCH --}}
            <form method="GET"
                action="{{ auth()->user()->role == 'manager' ? route('manager.laporan.stok') : route('transaksi-masuk.stok') }}"
                class="row g-3 mb-4">

                {{-- SUPER ADMIN --}}
                @if (auth()->user()->role == 'super_admin')

                    <div class="col-md-3">

                        <select name="perusahaan_id" class="form-select">

                            <option value="">
                                -- Semua Perusahaan --
                            </option>

                            @foreach ($perusahaans as $p)
                                <option value="{{ $p->id }}"
                                    {{ request('perusahaan_id') == $p->id ? 'selected' : '' }}>

                                    {{ $p->nama_perusahaan }}

                                </option>
                            @endforeach

                        </select>

                    </div>

                @endif

                <div class="col-md-6 d-flex">

                    <input type="text" name="search" class="form-control me-2"
                        placeholder="Cari berdasarkan nama barang" value="{{ request('search') }}">

                    <button type="submit" class="btn btn-primary">
                        Cari
                    </button>

                </div>

            </form>


            {{-- TABLE --}}
            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead class="table-primary text-center">

                        <tr>

                            <th>NO</th>

                            {{-- SUPER ADMIN --}}
                            @if (auth()->user()->role == 'super_admin')
                                <th>PERUSAHAAN</th>
                            @endif

                            <th>NAMA BARANG</th>
                            <th>TYPE</th>
                            <th>MEREK</th>
                            <th>STOK</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($stoks as $index => $stok)
                            <tr>

                                <td class="text-center">
                                    {{ $index + 1 }}
                                </td>

                                {{-- PERUSAHAAN --}}
                                @if (auth()->user()->role == 'super_admin')
                                    <td>
                                        {{ $stok->perusahaan->nama_perusahaan ?? '-' }}
                                    </td>
                                @endif

                                <td>
                                    {{ $stok->kategori->nama_barang ?? '-' }}
                                </td>

                                <td>
                                    {{ $stok->type }}
                                </td>

                                <td>
                                    {{ $stok->merek }}
                                </td>

                                <td class="text-center">

                                    <span class="badge bg-success">
                                        {{ $stok->jumlah }}
                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="text-center text-muted">

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
