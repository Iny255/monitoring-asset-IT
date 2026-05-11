@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Data Peminjaman')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/editpeminjaman.css') }}">

    <div class="container-fluid">

        <div class="row justify-content-center">

            <div class="col-lg-10">

                <div class="card border-0 shadow-sm rounded-4">

                    {{-- HEADER --}}
                    <div class="card-header border-0 px-4 pt-4">

                        <h5 class="text-primary mb-0">
                            Edit Data Peminjaman
                        </h5>

                        <small class="text-muted">
                            Perbarui data peminjaman barang
                        </small>

                    </div>

                    {{-- BODY --}}
                    <div class="card-body px-4 pb-4">

                        <form action="{{ route('peminjaman.update', $peminjaman->id) }}" method="POST">

                            @csrf
                            @method('PUT')

                            {{-- TIPE --}}
                            <input type="hidden" name="jenis_perusahaan" id="jenis_perusahaan_input"
                                value="{{ $peminjaman->tipe_peminjam ?? 'internal' }}">

                            <div class="row">

                                {{-- ========================================= --}}
                                {{-- KIRI --}}
                                {{-- ========================================= --}}
                                <div class="col-md-6">

                                    {{-- KODE BARANG --}}
                                    <div class="mb-3">

                                        <label class="form-label">
                                            Kode Barang
                                        </label>

                                        <input type="text" id="kode_barang" class="form-control"
                                            value="{{ $peminjaman->keluar->kode_barang ?? '' }}">

                                        <input type="hidden" name="keluar_id" id="keluar_id"
                                            value="{{ $peminjaman->keluar_id }}">

                                    </div>

                                    {{-- NAMA BARANG --}}
                                    <div class="mb-3">

                                        <label class="form-label">
                                            Nama Barang
                                        </label>

                                        <input type="text" id="nama_barang" class="form-control"
                                            value="{{ $peminjaman->keluar->masuk->kategori->nama_barang ?? '' }}" readonly>

                                    </div>

                                    {{-- JENIS PEMINJAM --}}
                                    <div class="mb-3">

                                        <label class="form-label">
                                            Jenis Peminjam
                                        </label>

                                        <select id="jenis_perusahaan" class="form-select">

                                            <option value="internal"
                                                {{ $peminjaman->tipe_peminjam == 'internal' ? 'selected' : '' }}>
                                                Internal
                                            </option>

                                            <option value="external"
                                                {{ $peminjaman->tipe_peminjam == 'external' ? 'selected' : '' }}>
                                                External
                                            </option>

                                        </select>

                                    </div>

                                    {{-- ========================================= --}}
                                    {{-- INTERNAL --}}
                                    {{-- ========================================= --}}
                                    <div id="form_internal"
                                        class="{{ $peminjaman->tipe_peminjam == 'external' ? 'd-none' : '' }}">

                                        {{-- PERUSAHAAN --}}
                                        <div class="mb-3">

                                            <label class="form-label">
                                                Perusahaan
                                            </label>

                                            @if (auth()->user()->role === 'super_admin')

                                                <select name="perusahaan_id" id="perusahaan_id" class="form-select">

                                                    <option value="">
                                                        -- Pilih Perusahaan --
                                                    </option>

                                                    @foreach ($perusahaans as $p)
                                                        <option value="{{ $p->id }}"
                                                            {{ $peminjaman->perusahaan_id == $p->id ? 'selected' : '' }}>

                                                            {{ $p->nama_perusahaan }}

                                                        </option>
                                                    @endforeach

                                                </select>
                                            @else
                                                <input type="text" class="form-control"
                                                    value="{{ auth()->user()->perusahaan->nama_perusahaan ?? '-' }}"
                                                    readonly>

                                                <input type="hidden" name="perusahaan_id"
                                                    value="{{ auth()->user()->id_perusahaan ?? '' }}">

                                            @endif

                                        </div>

                                        {{-- KARYAWAN --}}
                                        <div class="mb-3 position-relative">

                                            <label class="form-label">
                                                Nama Karyawan
                                            </label>

                                            <input type="text" id="nama" class="form-control" autocomplete="off"
                                                value="{{ $peminjaman->karyawan->nama_karyawan ?? '' }}">

                                            <input type="hidden" name="karyawan_id" id="karyawan_id"
                                                value="{{ $peminjaman->karyawan_id }}">

                                            <div id="hasil_nama" class="autocomplete-box d-none">
                                            </div>

                                        </div>

                                        {{-- LOKASI --}}
                                        <div class="mb-3">

                                            <label class="form-label">
                                                Lokasi
                                            </label>

                                            <select name="lokasi_id" id="lokasi_id" class="form-select">

                                                <option value="">
                                                    -- Pilih Lokasi --
                                                </option>

                                                @foreach ($lokasis as $l)
                                                    <option value="{{ $l->id }}"
                                                        data-perusahaan="{{ $l->perusahaan_id }}"
                                                        {{ $peminjaman->lokasi_id == $l->id ? 'selected' : '' }}>

                                                        {{ $l->nama_lokasi }}

                                                    </option>
                                                @endforeach

                                            </select>

                                        </div>

                                    </div>

                                    {{-- ========================================= --}}
                                    {{-- EXTERNAL --}}
                                    {{-- ========================================= --}}
                                    <div id="form_external"
                                        class="{{ $peminjaman->tipe_peminjam == 'external' ? '' : 'd-none' }}">

                                        {{-- NAMA --}}
                                        <div class="mb-3">

                                            <label class="form-label">
                                                Nama Peminjam
                                            </label>

                                            <input type="text" name="nama_eksternal" class="form-control"
                                                value="{{ $peminjaman->nama_eksternal }}">

                                        </div>

                                        {{-- PERUSAHAAN --}}
                                        <div class="mb-3">

                                            <label class="form-label">
                                                Perusahaan External
                                            </label>

                                            <input type="text" name="perusahaan_eksternal" class="form-control"
                                                value="{{ $peminjaman->perusahaan_eksternal }}">

                                        </div>

                                        {{-- LOKASI --}}
                                        <div class="mb-3">

                                            <label class="form-label">
                                                Lokasi
                                            </label>

                                            <input type="text" name="lokasi_manual" class="form-control"
                                                value="{{ $peminjaman->lokasi_manual }}">

                                        </div>

                                    </div>

                                </div>

                                {{-- ========================================= --}}
                                {{-- KANAN --}}
                                {{-- ========================================= --}}
                                <div class="col-md-6">

                                    {{-- TANGGAL PINJAM --}}
                                    <div class="mb-3">

                                        <label class="form-label">
                                            Tanggal Pinjam
                                        </label>

                                        <input type="date" name="tanggal_pinjam" class="form-control"
                                            value="{{ $peminjaman->tanggal_pinjam }}" required>

                                    </div>

                                    {{-- TANGGAL KEMBALI --}}
                                    <div class="mb-3">

                                        <label class="form-label">
                                            Rencana Kembali
                                        </label>

                                        <input type="date" name="tanggal_rencana_kembali" class="form-control"
                                            value="{{ $peminjaman->tanggal_rencana_kembali }}" required>

                                    </div>

                                    {{-- STATUS --}}
                                    <div class="mb-3">

                                        <label class="form-label">
                                            Status
                                        </label>

                                        <select name="status" class="form-select">

                                            <option value="Dipinjam"
                                                {{ $peminjaman->status == 'Dipinjam' ? 'selected' : '' }}>
                                                Dipinjam
                                            </option>

                                            <option value="Dikembalikan"
                                                {{ $peminjaman->status == 'Dikembalikan' ? 'selected' : '' }}>
                                                Dikembalikan
                                            </option>

                                        </select>

                                    </div>

                                    {{-- KEPERLUAN --}}
                                    <div class="mb-3">

                                        <label class="form-label">
                                            Keperluan
                                        </label>

                                        <textarea name="keperluan" class="form-control" rows="4">{{ $peminjaman->keperluan }}</textarea>

                                    </div>

                                    {{-- CATATAN --}}
                                    <div class="mb-3">

                                        <label class="form-label">
                                            Catatan
                                        </label>

                                        <textarea name="catatan" class="form-control" rows="4">{{ $peminjaman->catatan }}</textarea>

                                    </div>

                                </div>

                            </div>

                            {{-- BUTTON --}}
                            <div class="text-end mt-3">

                                <button class="btn btn-primary px-4">
                                    Update
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection

@section('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // =========================================
            // TOGGLE INTERNAL EXTERNAL
            // =========================================

            const jenis = document.getElementById('jenis_perusahaan');

            const hiddenJenis =
                document.getElementById('jenis_perusahaan_input');

            const internal =
                document.getElementById('form_internal');

            const external =
                document.getElementById('form_external');

            jenis.addEventListener('change', function() {

                hiddenJenis.value = this.value;

                if (this.value === 'external') {

                    internal.classList.add('d-none');

                    external.classList.remove('d-none');

                } else {

                    internal.classList.remove('d-none');

                    external.classList.add('d-none');

                }

            });


            // =========================================
            // FILTER LOKASI BERDASARKAN PERUSAHAAN
            // =========================================

            const perusahaanSelect =
                document.getElementById('perusahaan_id');

            const lokasiSelect =
                document.getElementById('lokasi_id');

            if (perusahaanSelect && lokasiSelect) {

                function filterLokasi() {

                    const perusahaanId =
                        perusahaanSelect.value;

                    Array.from(lokasiSelect.options).forEach(option => {

                        if (option.value === '') return;

                        if (
                            option.dataset.perusahaan === perusahaanId
                        ) {

                            option.style.display = 'block';

                        } else {

                            option.style.display = 'none';

                        }

                    });

                }

                filterLokasi();

                perusahaanSelect.addEventListener('change', filterLokasi);

            }


            // =========================================
            // AUTOCOMPLETE KARYAWAN
            // =========================================

            const inputNama =
                document.getElementById('nama');

            const hasilNama =
                document.getElementById('hasil_nama');

            const karyawanId =
                document.getElementById('karyawan_id');

            inputNama.addEventListener('keyup', function() {

                let keyword = this.value;

                if (keyword.length < 1) {

                    hasilNama.classList.add('d-none');

                    return;

                }

                fetch(`/dashboard/peminjaman/search-karyawan?q=${keyword}`)

                    .then(res => res.json())

                    .then(data => {

                        hasilNama.innerHTML = '';

                        if (data.length === 0) {

                            hasilNama.classList.add('d-none');

                            return;

                        }

                        hasilNama.classList.remove('d-none');

                        data.forEach(item => {

                            hasilNama.innerHTML += `
                    <div class="autocomplete-item"
                        data-id="${item.id}"
                        data-nama="${item.nama_karyawan}">
                        ${item.nama_karyawan}
                    </div>
                `;

                        });

                    });

            });

            document.addEventListener('click', function(e) {

                if (e.target.classList.contains('autocomplete-item')) {

                    inputNama.value =
                        e.target.dataset.nama;

                    karyawanId.value =
                        e.target.dataset.id;

                    hasilNama.classList.add('d-none');

                }

            });

        });
    </script>

@endsection
