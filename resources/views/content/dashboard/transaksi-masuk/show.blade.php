@extends('layouts/contentNavbarLayout')

@section('title', 'Detail Penerimaan Aset')

@section('content')

    <div class="container-fluid px-3">

        <div class="card shadow-sm border-0">

            <div class="card-header">
                <h5 class="mb-0 text-primary">
                    Detail Penerimaan Aset
                </h5>
            </div>

            <div class="card-body">

                {{-- INFORMASI PENERIMAAN --}}
                <table class="table table-bordered">

                    @if (auth()->user()->role == 'super_admin')
                        <tr>
                            <th width="250">Perusahaan</th>
                            <td>
                                {{ $masuk->perusahaan->nama_perusahaan ?? '-' }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <th>Jenis Penerimaan</th>
                        <td>
                            @if ($masuk->jenis_masuk == 'Pembelian')
                                <span class="badge bg-success">
                                    Pembelian
                                </span>
                            @else
                                <span class="badge bg-info">
                                    Mutasi Antar Perusahaan
                                </span>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th>Asal Penerimaan</th>
                        <td>

                            @if ($masuk->jenis_masuk == 'Pembelian')
                                {{ $masuk->supplier->nama_supplier ?? '-' }}
                            @else
                                {{ $masuk->perusahaanAsal->nama_perusahaan ?? '-' }}
                            @endif

                        </td>
                    </tr>

                    <tr>
                        <th>Kategori Aset</th>
                        <td>
                            {{ $masuk->dataAset->kategori->nama_barang ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Merek</th>
                        <td>
                            {{ $masuk->dataAset->merek ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Type</th>
                        <td>
                            {{ $masuk->dataAset->type ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <th>Warna</th>
                        <td>
                            {{ $masuk->dataAset->warna ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Pembelian</th>
                        <td>
                            {{ \Carbon\Carbon::parse($masuk->tanggal_pembelian)->format('d-m-Y') }}
                        </td>
                    </tr>

                    <tr>
                        <th>Jumlah Diterima</th>
                        <td>
                            {{ number_format($masuk->jumlah) }}
                        </td>
                    </tr>

                    <tr>
                        <th>Harga Satuan</th>

                        <td>

                            @if ($masuk->jenis_masuk == 'Pembelian')
                                Rp {{ number_format($masuk->harga_satuan, 0, ',', '.') }}
                            @else
                                -
                            @endif

                        </td>
                    </tr>

                    <tr>
                        <th>Total Harga</th>

                        <td>

                            @if ($masuk->jenis_masuk == 'Pembelian')
                                Rp {{ number_format($masuk->jumlah * $masuk->harga_satuan, 0, ',', '.') }}
                            @else
                                -
                            @endif

                        </td>
                    </tr>

                    <tr>
                        <th>Garansi</th>

                        <td>

                            @if ($masuk->jenis_masuk == 'Pembelian')
                                {{ $masuk->garansi }} Bulan
                            @else
                                -
                            @endif

                        </td>
                    </tr>

                    <tr>
                        <th>Keterangan Penerimaan</th>
                        <td>

                            @if ($masuk->ket_penerimaan == 'BAIK')
                                <span class="badge bg-success">
                                    BAIK
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    RUSAK
                                </span>
                            @endif

                        </td>
                    </tr>

                    <tr>
                        <th>Tanggal Input</th>
                        <td>
                            {{ $masuk->created_at->format('d-m-Y H:i') }}
                        </td>
                    </tr>
                    @if ($masuk->jenis_masuk == 'Mutasi')

                        <tr>
                            <th>Perusahaan Asal</th>

                            <td>

                                {{ $masuk->perusahaanAsal->nama_perusahaan ?? '-' }}

                            </td>
                        </tr>

                        @if ($masuk->historyMutasi)
                            <tr>
                                <th>Tanggal Mutasi</th>

                                <td>

                                    {{ \Carbon\Carbon::parse($masuk->historyMutasi->tanggal_mutasi)->format('d-m-Y') }}

                                </td>
                            </tr>
                        @endif

                    @endif

                </table>

                {{-- INVENTARIS GENERATED --}}
                <div class="mt-4">

                    <h5 class="text-primary">
                        Daftar Nomor Inventaris Aset
                    </h5>

                    <div class="table-responsive">

                        <table class="table table-bordered">

                            <thead class="table-primary">
                                <tr>
                                    <th width="60">No</th>
                                    <th>No Inventaris</th>
                                    <th>Kode Aset</th>
                                    <th>Status Aset</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($masuk->inventaris as $inventaris)
                                    <tr>

                                        <td>
                                            {{ $loop->iteration }}
                                        </td>

                                        <td>
                                            <span class="badge bg-label-info">
                                                {{ $inventaris->no_inventaris }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="badge bg-label-primary">
                                                {{ $inventaris->kode_aset }}
                                            </span>
                                        </td>

                                        <td>

                                            @switch($inventaris->status)
                                                @case('TERSEDIA')
                                                    <span class="badge bg-success">
                                                        TERSEDIA
                                                    </span>
                                                @break

                                                @case('DIPAKAI')
                                                    <span class="badge bg-primary">
                                                        DIPAKAI
                                                    </span>
                                                @break

                                                @case('DIPINJAM')
                                                    <span class="badge bg-warning">
                                                        DIPINJAM
                                                    </span>
                                                @break

                                                @case('RUSAK')
                                                    <span class="badge bg-danger">
                                                        RUSAK
                                                    </span>
                                                @break

                                                @case('AFKIR')
                                                    <span class="badge bg-dark">
                                                        AFKIR
                                                    </span>
                                                @break

                                                @default
                                                    <span class="badge bg-secondary">
                                                        {{ $inventaris->status }}
                                                    </span>
                                            @endswitch

                                        </td>

                                    </tr>

                                    @empty

                                        <tr>
                                            <td colspan="4" class="text-center">
                                                Data inventaris belum tersedia
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>

                        </div>

                    </div>

                    <hr>

                    <a href="{{ route('transaksi-masuk.index') }}" class="btn btn-secondary">

                        <i class="bx bx-arrow-back"></i>
                        Kembali

                    </a>

                </div>

            </div>

        </div>

    @endsection
