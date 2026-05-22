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
                @if (auth()->user()->role === 'petugas' || auth()->user()->role === 'super_admin')
                    <a href="{{ route('transaksi-masuk.create') }}"class="btn btn-primary px-4 py-2 fw-semibold">
                        <i class="bx bx-plus"></i> Tambah Data
                    </a>
                @endif
            @endauth
        </div>


        <div class="card-body">

            {{-- SEARCH --}}
            <form method="GET" class="row g-3 mb-4">

                {{-- 🔍 SEARCH --}}
                <div class="col-md-5 d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Cari kode / nama barang"
                        value="{{ request('search') }}">
                </div>

                {{-- 🏢 FILTER PERUSAHAAN (SUPER ADMIN ONLY) --}}
                @if (auth()->user()->role === 'super_admin')
                    <div class="col-md-4">
                        <select name="perusahaan_id" class="form-select">
                            <option value="">-- Semua Perusahaan --</option>
                            @foreach ($perusahaans as $p)
                                <option value="{{ $p->id }}"
                                    {{ request('perusahaan_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_perusahaan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- 🔘 BUTTON --}}
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Cari</button>

                    {{-- 🔄 RESET --}}
                    <a href="{{ route('transaksi-masuk.index') }}" class="btn btn-secondary">
                        Reset
                    </a>
                </div>

            </form>


            <div class="table-responsive">
                <table class="table table-bordered table-hover">

                    <thead class="table-primary text-center">
                        <tr>
                            <th>No</th>
                            <th>KODE MASUK</th>
                            @if (auth()->user()->role === 'super_admin')
                                <th>PERUSAHAAN</th>
                            @endif
                            <th>NAMA BARANG</th>
                            <th>TYPE</th>
                            <th>MEREK</th>
                            <th>JUMLAH</th>
                            <th>TANGGAL BELI</th>
                            <th>SUPPLIER</th>
                            <th>GAMBAR</th>

                            @auth
                                @if (in_array(auth()->user()->role, ['petugas', 'manager', 'super_admin']))
                                    <th>AKSI</th>
                                @endif
                            @endauth
                        </tr>
                    </thead>


                    <tbody>
                        @forelse ($masuks as $index => $masuk)
                            <tr>

                                {{-- NO --}}
                                <td class="text-center">
                                    {{ $masuks->firstItem() + $index }}
                                </td>

                                {{-- KODE --}}
                                <td class="text-center">
                                    <span class="badge bg-label-primary">
                                        {{ $masuk->kode_masuk }}
                                    </span>
                                </td>

                                {{-- PERUSAHAAN (HANYA SUPER ADMIN) --}}
                                @if (auth()->user()->role === 'super_admin')
                                    <td class="text-center">
                                        <span class="badge bg-label-info">
                                            {{ optional($masuk->perusahaan)->nama_perusahaan ?? '-' }}
                                        </span>
                                    </td>
                                @endif

                                {{-- NAMA BARANG --}}
                                <td>
                                    {{ $masuk->kategori->nama_barang ?? '-' }}
                                </td>

                                {{-- TYPE --}}
                                <td>{{ $masuk->type }}</td>

                                {{-- MEREK --}}
                                <td>{{ $masuk->merek }}</td>

                                {{-- JUMLAH --}}
                                <td class="text-center">{{ $masuk->jumlah }}</td>

                                {{-- TANGGAL --}}
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($masuk->tgl_beli)->format('d-m-Y') }}
                                </td>

                                {{-- SUPPLIER --}}
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
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">

                                        {{-- SHOW --}}
                                        @auth

                                            @if (auth()->user()->role === 'manager')
                                                <a href="{{ route('manager.laporan.masuk.show', $masuk->id) }}"
                                                    class="btn btn-info btn-sm">

                                                    <i class="bx bx-show"></i>

                                                </a>
                                            @else
                                                <a href="{{ route('transaksi-masuk.show', $masuk->id) }}"
                                                    class="btn btn-info btn-sm">

                                                    <i class="bx bx-show"></i>

                                                </a>
                                            @endif

                                        @endauth

                                        @if (in_array(auth()->user()->role, ['petugas', 'super_admin']))
                                            <a href="{{ route('transaksi-masuk.edit', $masuk->id) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="bx bx-edit-alt"></i>
                                            </a>

                                            <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $masuk->id }}">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        @endif

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

                            </tr>
                        @empty
                            <tr>
                                @php
                                    $colspan = auth()->user()->role === 'super_admin' ? 11 : 10;
                                @endphp

                                <td colspan="{{ $colspan }}" class="text-center text-muted">
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
    @if (auth()->user()->role === 'petugas' || auth()->user()->role === 'super_admin')
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
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    const perusahaanSelect = document.querySelector('[name="id_perusahaan"]');
                    const kategoriSelect = document.getElementById('kategori');

                    function loadKategori(id) {

                        if (!id) {
                            kategoriSelect.innerHTML = '<option value="">-- Pilih Barang --</option>';
                            return;
                        }

                        fetch('/dashboard/get-kategori/' + id)
                            .then(res => res.json())
                            .then(data => {

                                let html = '<option value="">-- Pilih Barang --</option>';

                                data.forEach(item => {
                                    html += `<option value="${item.id}">${item.nama_barang}</option>`;
                                });

                                kategoriSelect.innerHTML = html;
                            });
                    }

                    if (perusahaanSelect) {

                        perusahaanSelect.addEventListener('change', function() {
                            loadKategori(this.value);
                        });

                        // 🔥 auto load saat pertama
                        if (perusahaanSelect.value) {
                            loadKategori(perusahaanSelect.value);
                        }
                    }

                });
            </script>
        @endsection
    @endif
@endauth
