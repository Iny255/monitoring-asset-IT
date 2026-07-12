@extends('layouts/contentNavbarLayout')

@section('title', 'Penerimaan Aset')

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
            <h5 class="text-primary mb-0">Data Penerimaan Aset</h5>

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
            {{-- FILTER LAPORAN --}}
            <div class="card border mb-4">

                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold">
                        Filter Data Penerimaan Aset
                    </h6>
                </div>

                <div class="card-body">

                    <form method="GET" action="{{ route('transaksi-masuk.index') }}">

                        <div class="row g-3">

                            @if (auth()->user()->role === 'super_admin')
                                <div class="col-md-3">

                                    <label class="form-label">
                                        Perusahaan
                                    </label>

                                    <select name="perusahaan_id" class="form-select">

                                        <option value="">
                                            Semua Perusahaan
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

                            <div class="col-md-3">

                                <label class="form-label">
                                    Tanggal Awal
                                </label>

                                <input type="date" name="tanggal_awal" class="form-control"
                                    value="{{ request('tanggal_awal') }}">

                            </div>

                            <div class="col-md-3">

                                <label class="form-label">
                                    Tanggal Akhir
                                </label>

                                <input type="date" name="tanggal_akhir" class="form-control"
                                    value="{{ request('tanggal_akhir') }}">

                            </div>
                            <div class="col-md-3">

                                <label class="form-label">
                                    Supplier
                                </label>

                                <select name="supplier_id" class="form-select">

                                    <option value="">
                                        Semua Supplier
                                    </option>

                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}"
                                            {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>

                                            {{ $supplier->nama_supplier }}

                                        </option>
                                    @endforeach

                                </select>

                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jenis Masuk</label>

                                <select name="jenis_masuk" class="form-select">
                                    <option value="">Semua</option>

                                    <option value="Pembelian"
                                        {{ request('jenis_masuk') == 'Pembelian' ? 'selected' : '' }}>
                                        Pembelian
                                    </option>

                                    <option value="Mutasi" {{ request('jenis_masuk') == 'Mutasi' ? 'selected' : '' }}>
                                        Mutasi Antar Perusahaan
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-3">

                                <label class="form-label">
                                    Pencarian
                                </label>

                                <input type="text" name="search" class="form-control"
                                    placeholder="Nama barang / merek / type" value="{{ request('search') }}">

                            </div>

                        </div>

                        <div class="mt-3 d-flex gap-2">

                            <button type="submit" class="btn btn-primary">

                                <i class="bx bx-search"></i>
                                Tampilkan

                            </button>

                            <a href="{{ route('transaksi-masuk.index') }}" class="btn btn-secondary">

                                <i class="bx bx-refresh"></i>
                                Reset

                            </a>

                            <a href="{{ route('transaksi-masuk.cetak', request()->query()) }}" target="_blank"
                                class="btn btn-danger">

                                <i class="bx bx-printer"></i>
                                Cetak PDF

                            </a>

                        </div>

                    </form>

                </div>

            </div>


            <div class="table-responsive">
                <table class="table table-bordered table-hover">

                    <thead class="table-primary text-center">
                        <tr>

                            <th width="60">NO</th>
                            @if (auth()->user()->role === 'super_admin')
                                <th>PERUSAHAAN</th>
                            @endif
                            <th>DATA ASET</th>
                            <th>JENIS</th>
                            <th>ASAL</th>
                            <th>TGL PEMBELIAN</th>
                            <th>JUMLAH</th>
                            <th>HARGA SATUAN</th>
                            <th>GARANSI</th>
                            <th>KET. PENERIMAAN</th>
                            <th width="150">AKSI</th>

                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($masuks as $index => $masuk)
                            <tr>

                                {{-- NO --}}
                                <td class="text-center">
                                    {{ $masuks->firstItem() + $index }}
                                </td>

                                {{-- PERUSAHAAN --}}
                                @if (auth()->user()->role === 'super_admin')
                                    <td>

                                        {{ $masuk->perusahaan->nama_perusahaan ?? '-' }}

                                    </td>
                                @endif

                                {{-- DATA ASET --}}
                                <td>

                                    {{ $masuk->dataAset->kategori->nama_barang ?? '-' }}
                                    <br>

                                    <small class="text-muted">

                                        {{ $masuk->dataAset->merek ?? '-' }}
                                        -
                                        {{ $masuk->dataAset->type ?? '-' }}

                                    </small>

                                </td>
                                <td class="text-center">

                                    @if ($masuk->jenis_masuk == 'Pembelian')
                                        <span class="badge bg-success">
                                            Pembelian
                                        </span>
                                    @else
                                        <span class="badge bg-info">
                                            Mutasi
                                        </span>
                                    @endif

                                </td>

                                {{-- SUPPLIER --}}
                                <td>

                                    @if ($masuk->jenis_masuk == 'Pembelian')
                                        {{ $masuk->supplier->nama_supplier ?? '-' }}
                                    @else
                                        {{ $masuk->perusahaanAsal->nama_perusahaan ?? '-' }}
                                    @endif

                                </td>
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($masuk->tanggal_pembelian)->format('d-m-Y') }}
                                </td>

                                {{-- JUMLAH --}}
                                <td class="text-center">

                                    {{ number_format($masuk->jumlah) }}

                                </td>

                                {{-- HARGA --}}
                                <td class="text-end">

                                    @if ($masuk->jenis_masuk == 'Pembelian')
                                        Rp {{ number_format($masuk->harga_satuan, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif

                                </td>

                                {{-- GARANSI --}}
                                <td class="text-center">

                                    {{ $masuk->garansi }} Bulan

                                </td>

                                {{-- KETERANGAN --}}
                                <td class="text-center">

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

                                {{-- AKSI --}}
                                <td class="text-center">

                                    <div class="d-flex justify-content-center gap-1">

                                        <a href="{{ route('transaksi-masuk.show', $masuk->id) }}"
                                            class="btn btn-info btn-sm">

                                            <i class="bx bx-show"></i>

                                        </a>

                                        @if (in_array(auth()->user()->role, ['petugas', 'super_admin']))
                                            <button type="button" class="btn btn-warning btn-sm btn-edit"
                                                data-url="{{ route('transaksi-masuk.edit', $masuk->id) }}">

                                                <i class="bx bx-edit-alt"></i>

                                            </button>
                                            <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $masuk->id }}">

                                                <i class="bx bx-trash"></i>

                                            </button>
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

                                <td colspan="{{ auth()->user()->role === 'super_admin' ? 10 : 9 }}" class="text-center">

                                    Data penerimaan aset belum ada

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
                //edit
                document.querySelectorAll('.btn-edit').forEach(btn => {

                    btn.addEventListener('click', function() {

                        let url = this.dataset.url;

                        Swal.fire({

                            title: 'Edit Data?',
                            text: 'Anda akan masuk ke halaman edit data.',
                            icon: 'question',

                            showCancelButton: true,

                            confirmButtonColor: '#696cff',
                            cancelButtonColor: '#8592a3',

                            confirmButtonText: 'Ya, Edit',
                            cancelButtonText: 'Batal'

                        }).then((result) => {

                            if (result.isConfirmed) {

                                window.location.href = url;

                            }

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
