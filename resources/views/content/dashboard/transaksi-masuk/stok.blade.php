@extends('layouts/contentNavbarLayout')

@section('title', 'Stok Aset')

@section('content')

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="text-primary mb-0">
                Stok Aset
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

                <div class="col-md-6 d-flex mb-4">

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

                            <th>TOTAL ASET</th>

                            <th>TERSEDIA</th>

                            <th>DIPAKAI</th>

                            <th>DIPINJAM</th>

                            <th>RUSAK</th>

                            <th>PEMAKAI</th>

                            <th>AKSI</th>

                        </tr>

                    </thead>
                    <tbody>

                        @forelse($stoks as $stok)

                            @php

                                $pemakai = collect();

                                foreach ($stok->inventaris as $inv) {
                                    $keluar = $inv->keluarTerakhir;

                                    if (!$keluar) {
                                        continue;
                                    }

                                    // hanya tampilkan aset yang memang masih dipakai
                                    if ($inv->status != 'DIPAKAI') {
                                        continue;
                                    }

                                    if ($keluar->jenis_penerima == 'Perorangan') {
                                        $pemakai->push(optional($keluar->karyawan)->nama_karyawan);
                                    } else {
                                        $pemakai->push($keluar->divisi_klr);
                                    }
                                }

                            @endphp

                            <tr>

                                <td class="text-center">
                                    {{ $loop->iteration }}
                                </td>

                                @if (auth()->user()->role == 'super_admin')
                                    <td>
                                        {{ $stok->perusahaan->nama_perusahaan ?? '-' }}
                                    </td>
                                @endif

                                <td>
                                    {{ $stok->kategori->nama_barang ?? '-' }}
                                </td>

                                <td>
                                    {{ $stok->type ?? '-' }}
                                </td>

                                <td>
                                    {{ $stok->merek ?? '-' }}
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-primary">
                                        {{ $stok->total_aset }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-success">
                                        {{ $stok->tersedia }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-warning">
                                        {{ $stok->dipakai }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-info">
                                        {{ $stok->dipinjam }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-danger">
                                        {{ $stok->rusak }}
                                    </span>
                                </td>

                                <td>

                                    @forelse($pemakai->unique() as $nama)
                                        <span class="badge bg-label-primary mb-1">
                                            {{ $nama }}
                                        </span>
                                        <br>

                                    @empty

                                        <span class="text-muted">
                                            Belum dipakai
                                        </span>
                                    @endforelse

                                </td>

                                <td class="text-center">

                                    <a href="{{ route('stok.history', $stok->data_aset_id) }}"
                                        class="btn btn-primary btn-sm">
                                        Riwayat
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="{{ auth()->user()->role == 'super_admin' ? 12 : 11 }}"
                                    class="text-center text-muted">

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
