@extends('layouts/contentNavbarLayout')

@section('title', 'Aset Saya - Monitoring Aset IT')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- HERO HEADER --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3 py-sm-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-md bg-label-primary me-3 flex-shrink-0">
                        <span class="avatar-initial rounded">
                            <i class="bi bi-laptop fs-3"></i>
                        </span>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1 fs-5 fs-sm-4">Aset & Perangkat Saya</h4>
                        <small class="text-muted">Daftar perangkat IT dan inventaris yang sedang Anda gunakan atau pinjam</small>
                    </div>
                </div>
                <div>
                    @if ($karyawan)
                        <span class="badge bg-label-success p-2 fs-6">
                            <i class="bi bi-person-badge me-1"></i> {{ $karyawan->nama_karyawan }} (NIK: {{ $karyawan->kode_karyawan ?? '-' }})
                        </span>
                    @else
                        <span class="badge bg-label-warning p-2 fs-6">
                            <i class="bi bi-exclamation-triangle me-1"></i> Belum Terhubung Karyawan
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ALERT UNTUK USER TANPA KARYAWAN_ID --}}
    @if (!$user->karyawan_id)
        <div class="alert alert-warning border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill fs-2 me-3 text-warning flex-shrink-0"></i>
                <div>
                    <h5 class="alert-heading fw-bold mb-1">Akun Belum Terhubung dengan Data Karyawan</h5>
                    <p class="mb-0 small">Akun Anda (<strong>{{ $user->username }}</strong>) belum ditautkan dengan Master Data Karyawan. Untuk menampilkan daftar perangkat dan aset yang Anda gunakan, silakan hubungi <strong>Petugas IT Support / Admin</strong> untuk mentautkan profil karyawan Anda.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- STATS CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold d-block mb-1 small">Aset Utama (Mapping)</span>
                        <h3 class="card-title fw-bold mb-0 text-primary">{{ $mapings->count() }}</h3>
                        <small class="text-muted">Perangkat tetap dialokasikan</small>
                    </div>
                    <div class="avatar avatar-md bg-label-primary flex-shrink-0">
                        <span class="avatar-initial rounded fs-4"><i class="bi bi-pc-display"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold d-block mb-1 small">Pinjaman Sementara</span>
                        <h3 class="card-title fw-bold mb-0 text-info">{{ $peminjamans->where('status', 'dipinjam')->count() }}</h3>
                        <small class="text-muted">Aset sedang dipinjam</small>
                    </div>
                    <div class="avatar avatar-md bg-label-info flex-shrink-0">
                        <span class="avatar-initial rounded fs-4"><i class="bi bi-clipboard-check"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold d-block mb-1 small">Divisi & Perusahaan</span>
                        <h6 class="fw-bold mb-0 text-dark">{{ $karyawan->divisi ?? '-' }}</h6>
                        <small class="text-muted">{{ $user->perusahaan->nama_perusahaan ?? 'Holding / HO' }}</small>
                    </div>
                    <div class="avatar avatar-md bg-label-success flex-shrink-0">
                        <span class="avatar-initial rounded fs-4"><i class="bi bi-building"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DAFTAR ASET UTAMA (MAPPING) --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold mb-0 fs-6 fs-sm-5">
                    <i class="bi bi-laptop text-primary me-2"></i> Perangkat & Aset Utama Dipakai
                </h5>
                <span class="badge bg-label-primary">{{ $mapings->count() }} Perangkat</span>
            </div>
        </div>
        
        {{-- DESKTOP TABLE VIEW (d-none d-md-block) --}}
        <div class="card-body p-0 d-none d-md-block">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Kode / No. Inventaris</th>
                            <th>Nama Aset & Kategori</th>
                            <th>Spesifikasi & Hardware</th>
                            <th>Lokasi Penempatan</th>
                            <th>Tgl Digunakan</th>
                            <th>Status</th>
                            <th class="text-center" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mapings as $maping)
                            @php
                                $inventaris = $maping->keluar?->inventaris;
                                $dataAset = $inventaris?->dataAset;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong class="text-primary">{{ $inventaris->no_inventaris ?? '-' }}</strong>
                                    @if ($inventaris?->kode_aset)
                                        <br><small class="text-muted">Kode: {{ $inventaris->kode_aset }}</small>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-dark">{{ $dataAset->nama_barang ?? 'Perangkat IT' }}</strong>
                                    @if ($dataAset?->kategori)
                                        <br><span class="badge bg-label-secondary"><i class="bi bi-tag me-1"></i>{{ $dataAset->kategori->nama_kategori }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($maping->processor || $maping->ram || $maping->system)
                                        <small class="d-block"><strong>Proc:</strong> {{ $maping->processor ?? '-' }}</small>
                                        <small class="d-block"><strong>RAM:</strong> {{ $maping->ram ?? '-' }} | <strong>OS:</strong> {{ $maping->system ?? '-' }}</small>
                                        @if ($maping->device_id)
                                            <small class="text-muted"><strong>ID:</strong> {{ $maping->device_id }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($maping->lokasi)
                                        <span class="badge bg-label-info"><i class="bi bi-geo-alt me-1"></i>{{ $maping->lokasi->nama_lokasi }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $maping->tanggal_digunakan ? \Carbon\Carbon::parse($maping->tanggal_digunakan)->format('d/m/Y') : '-' }}
                                </td>
                                <td>
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>{{ $maping->status ?? 'Aktif' }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('e-ticket.index') }}" class="btn btn-sm btn-outline-warning" title="Laporkan Kendala untuk Perangkat ini">
                                        <i class="bi bi-exclamation-octagon me-1"></i> Lapor Kendala
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    <span class="text-muted">Belum ada perangkat atau aset utama yang terdaftar atas nama Anda.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- MOBILE CARD VIEW (d-block d-md-none) --}}
        <div class="card-body p-3 d-block d-md-none">
            @forelse ($mapings as $maping)
                @php
                    $inventaris = $maping->keluar?->inventaris;
                    $dataAset = $inventaris?->dataAset;
                @endphp
                <div class="card border shadow-none mb-3 rounded-3 bg-body">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold text-dark mb-1 fs-6">{{ $dataAset->nama_barang ?? 'Perangkat IT' }}</h6>
                                <span class="badge bg-label-primary"><i class="bi bi-barcode me-1"></i>{{ $inventaris->no_inventaris ?? '-' }}</span>
                            </div>
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>{{ $maping->status ?? 'Aktif' }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Kategori:</small>
                                <span class="badge bg-label-secondary text-truncate max-w-100"><i class="bi bi-tag me-1"></i>{{ $dataAset->kategori->nama_kategori ?? '-' }}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Penempatan:</small>
                                <span class="badge bg-label-info text-truncate max-w-100"><i class="bi bi-geo-alt me-1"></i>{{ $maping->lokasi->nama_lokasi ?? '-' }}</span>
                            </div>
                            @if ($maping->processor || $maping->ram || $maping->system)
                                <div class="col-12">
                                    <small class="text-muted d-block">Spesifikasi:</small>
                                    <small class="fw-semibold text-dark d-block">{{ $maping->processor ?? '-' }} | {{ $maping->ram ?? '-' }} | {{ $maping->system ?? '-' }}</small>
                                </div>
                            @endif
                            <div class="col-12">
                                <small class="text-muted d-block">Tgl Digunakan:</small>
                                <small class="fw-semibold text-dark">{{ $maping->tanggal_digunakan ? \Carbon\Carbon::parse($maping->tanggal_digunakan)->format('d/m/Y') : '-' }}</small>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('e-ticket.index') }}" class="btn btn-warning btn-sm w-100 fw-bold py-2">
                                <i class="bi bi-exclamation-octagon me-1"></i> Lapor Kendala IT
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                    Belum ada perangkat atau aset utama yang terdaftar.
                </div>
            @endforelse
        </div>
    </div>

    {{-- DAFTAR PINJAMAN ASET SEMENTARA --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold mb-0 fs-6 fs-sm-5">
                    <i class="bi bi-clipboard-check text-info me-2"></i> Peminjaman Aset Sementara
                </h5>
                <span class="badge bg-label-info">{{ $peminjamans->count() }} Transaksi</span>
            </div>
        </div>
        
        {{-- DESKTOP TABLE VIEW --}}
        <div class="card-body p-0 d-none d-md-block">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Kode Pinjam</th>
                            <th>Nama Aset & Inventaris</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Rencana Kembali</th>
                            <th>Keperluan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($peminjamans as $pinjam)
                            @php
                                $inventaris = $pinjam->inventaris;
                                $dataAset = $inventaris?->dataAset;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong class="text-primary">{{ $pinjam->kode_peminjaman }}</strong></td>
                                <td>
                                    <strong class="text-dark">{{ $dataAset->nama_barang ?? '-' }}</strong>
                                    @if ($inventaris?->no_inventaris)
                                        <br><small class="text-muted">No: {{ $inventaris->no_inventaris }}</small>
                                    @endif
                                </td>
                                <td>{{ $pinjam->tanggal_pinjam ? \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $pinjam->tanggal_rencana_kembali ? \Carbon\Carbon::parse($pinjam->tanggal_rencana_kembali)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $pinjam->keperluan ?? '-' }}</td>
                                <td>
                                    @if ($pinjam->status === 'dipinjam')
                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Dipinjam</span>
                                    @elseif ($pinjam->status === 'kembali')
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Dikembalikan</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($pinjam->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    <span class="text-muted">Tidak ada data peminjaman aset sementara.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- MOBILE CARD VIEW --}}
        <div class="card-body p-3 d-block d-md-none">
            @forelse ($peminjamans as $pinjam)
                @php
                    $inventaris = $pinjam->inventaris;
                    $dataAset = $inventaris?->dataAset;
                @endphp
                <div class="card border shadow-none mb-3 rounded-3 bg-body">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <small class="text-primary fw-bold d-block">{{ $pinjam->kode_peminjaman }}</small>
                                <h6 class="fw-bold text-dark mb-0 fs-6">{{ $dataAset->nama_barang ?? '-' }}</h6>
                            </div>
                            @if ($pinjam->status === 'dipinjam')
                                <span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Dipinjam</span>
                            @elseif ($pinjam->status === 'kembali')
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Dikembalikan</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($pinjam->status) }}</span>
                            @endif
                        </div>
                        <hr class="my-2">
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted d-block">Tgl Pinjam:</small>
                                <small class="fw-semibold text-dark">{{ $pinjam->tanggal_pinjam ? \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->format('d/m/Y') : '-' }}</small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Tgl Kembali:</small>
                                <small class="fw-semibold text-dark">{{ $pinjam->tanggal_rencana_kembali ? \Carbon\Carbon::parse($pinjam->tanggal_rencana_kembali)->format('d/m/Y') : '-' }}</small>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block">Keperluan:</small>
                                <small class="fw-semibold text-dark">{{ $pinjam->keperluan ?? '-' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                    Tidak ada data peminjaman aset sementara.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
