@extends('layouts/contentNavbarLayout')

@section('title', 'Detail Data Peminjaman')

@section('content')

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold text-primary">Detail Peminjaman</h5>
        </div>

        <div class="card-body">

            <div class="row g-4">

                {{-- INFO PEMINJAMAN --}}
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-header bg-light fw-bold">
                            Informasi Peminjaman
                        </div>

                        <div class="card-body p-0">
                            <table class="table table-bordered mb-0">

                                <tr>
                                    <th width="40%">Kode Barang</th>
                                    <td>{{ $peminjaman->keluar->kode_barang ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Nama Barang</th>
                                    <td>{{ $peminjaman->keluar->masuk->kategori->nama_barang ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Peminjam</th>
                                    <td>
                                        @if ($peminjaman->tipe_peminjam === 'external')
                                            {{ $peminjaman->nama_eksternal ?? '-' }}
                                        @else
                                            {{ optional($peminjaman->karyawan)->nama_karyawan ?? '-' }}
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th>Perusahaan</th>
                                    <td>
                                        @if ($peminjaman->tipe_peminjam === 'external')
                                            {{ $peminjaman->perusahaan_eksternal ?? '-' }}
                                        @else
                                            {{ optional($peminjaman->perusahaan)->nama_perusahaan ?? '-' }}
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th>Lokasi</th>
                                    <td>
                                        @if ($peminjaman->tipe_peminjam === 'external')
                                            {{ $peminjaman->lokasi_manual ?? '-' }}
                                        @else
                                            {{ optional($peminjaman->lokasi)->nama_lokasi ?? '-' }}
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th>Tanggal Pinjam</th>
                                    <td>{{ $peminjaman->tanggal_pinjam ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Rencana Kembali</th>
                                    <td>{{ $peminjaman->tanggal_rencana_kembali ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Tanggal Kembali</th>
                                    <td>{{ $peminjaman->tanggal_kembali ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @php
                                            $status = strtolower($peminjaman->status ?? '');
                                        @endphp

                                        <span
                                            class="badge
                                        @if ($status == 'dipinjam') bg-warning text-dark
                                        @elseif($status == 'dikembalikan') bg-success
                                        @elseif($status == 'hilang') bg-danger
                                        @elseif($status == 'rusak') bg-secondary
                                        @else bg-dark @endif">
                                            {{ $peminjaman->status ?? '-' }}
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <th>Keperluan</th>
                                    <td>{{ $peminjaman->keperluan ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <th>Catatan</th>
                                    <td>{{ $peminjaman->catatan ?? '-' }}</td>
                                </tr>

                            </table>
                        </div>

                    </div>
                </div>

            </div>

            {{-- TOMBOL --}}
            <div class="mt-4">
                @auth
                    @if (auth()->user()->role === 'manager')
                        <a href="{{ route('manager.laporan.peminjaman') }}" class="btn btn-secondary px-4">
                            ← Kembali
                        </a>
                    @else
                        <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary px-4">
                            ← Kembali
                        </a>
                    @endif
                @endauth
            </div>

        </div>
    </div>

@endsection
