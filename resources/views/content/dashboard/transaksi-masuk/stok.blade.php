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
                    <a href="{{ route('transaksi-keluar.index') }}" class="btn btn-secondary px-4 py-2 btn-sm">
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

                            @foreach ($perusahaans ?? [] as $p)
                                <option value="{{ data_get($p, 'id') }}"
                                    {{ request('perusahaan_id') == data_get($p, 'id') ? 'selected' : '' }}>

                                    {{ data_get($p, 'nama_perusahaan', '-') }}

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

                            @if (auth()->user()->role == 'super_admin')
                                <th>PERUSAHAAN</th>
                            @endif

                            <th>NAMA BARANG</th>

                            <th>TYPE</th>

                            <th>MEREK</th>

                            <th>KONDISI</th>

                            <th>STOK AWAL</th>

                            <th>DIPAKAI OLEH</th>

                            <th>TOTAL KELUAR</th>

                            <th>SISA STOK</th>
                            <th>AKSI</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($stoks ?? collect() as $group)
                            @php

                                $group = collect($group);

                                $first = $group->first();

                                if (!$first) {
                                    continue;
                                }

                                $stokAwal = $group->sum('jumlah');

                                $totalKeluar = $group->sum(function ($item) {
                                    return collect(data_get($item, 'keluars', []))->sum('jumlah');
                                });

                                $sisa = $stokAwal - $totalKeluar;

                            @endphp

                            <tr>

                                {{-- NO --}}
                                <td class="text-center">

                                    {{ $loop->iteration }}

                                </td>

                                {{-- PERUSAHAAN --}}
                                @if (auth()->user()->role == 'super_admin')
                                    <td>

                                        {{ data_get($first, 'perusahaan.nama_perusahaan', '-') }}

                                    </td>
                                @endif

                                {{-- NAMA BARANG --}}
                                <td>

                                    {{ data_get($first, 'kategori.nama_barang', '-') }}

                                </td>

                                {{-- TYPE --}}
                                <td>

                                    {{ data_get($first, 'type', '-') }}

                                </td>

                                {{-- MEREK --}}
                                <td>

                                    {{ data_get($first, 'merek', '-') }}

                                </td>

                                {{-- KONDISI --}}
                                <td class="text-center">

                                    @if (data_get($first, 'kondisi') == 'Baru')
                                        <span class="badge bg-primary">

                                            Baru

                                        </span>
                                    @else
                                        <span class="badge bg-warning">

                                            Bekas

                                        </span>
                                    @endif

                                </td>

                                {{-- STOK AWAL --}}
                                <td class="text-center">

                                    <span class="badge bg-info">

                                        {{ $stokAwal }}

                                    </span>

                                </td>

                                {{-- DIPAKAI OLEH --}}
                                <td>
                                    @php

                                        $pemakai = collect();

                                        foreach ($group ?? collect() as $item) {
                                            foreach (data_get($item, 'keluars', collect()) as $keluar) {
                                                if (data_get($keluar, 'jenis_penerima') == 'Perorangan') {
                                                    $pemakai->push(data_get($keluar, 'karyawan.nama_karyawan', '-'));
                                                } else {
                                                    $pemakai->push(data_get($keluar, 'divisi_klr', '-'));
                                                }
                                            }
                                        }

                                    @endphp

                                    @forelse ($pemakai->unique() as $nama)
                                        <div class="mb-1">

                                            <span class="badge bg-label-primary">

                                                {{ $nama }}

                                            </span>

                                        </div>

                                    @empty

                                        <span class="text-muted">

                                            Belum dipakai

                                        </span>
                                    @endforelse

                                </td>

                                {{-- TOTAL KELUAR --}}
                                <td class="text-center">

                                    <span class="badge bg-danger">

                                        {{ $totalKeluar }}

                                    </span>

                                </td>

                                {{-- SISA --}}
                                <td class="text-center">

                                    <span class="badge bg-success">

                                        {{ $sisa }}

                                    </span>

                                </td>

                                {{-- AKSI --}}
                                <td class="text-center">

                                    <a href="{{ auth()->user()->role == 'manager'
                                        ? route('manager.stok.history', data_get($first, 'id'))
                                        : route('stok.history', data_get($first, 'id')) }}"
                                        class="btn btn-primary btn-sm">

                                        Riwayat

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                               <td colspan="{{ auth()->user()->role == 'super_admin' ? 11 : 10 }}" class="text-center text-muted">

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
