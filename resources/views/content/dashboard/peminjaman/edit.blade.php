@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Data Peminjaman')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/editpeminjaman.css') }}">

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="card border-0 shadow-sm rounded-4">

                    <div class="card-header border-0 px-4 pt-4">
                        <h5 class="text-primary mb-0">Edit Data Peminjaman</h5>
                        <small class="text-muted">Perbarui data peminjaman barang</small>
                    </div>

                    <div class="card-body px-4 pb-4">

                        <form action="{{ route('peminjaman.update', $peminjaman->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- 🔥 TIPE --}}
                            <input type="hidden" name="jenis_perusahaan" id="jenis_perusahaan_input"
                                value="{{ $peminjaman->tipe_peminjam ?? 'internal' }}">

                            <div class="row">

                                {{-- ================= KIRI ================= --}}
                                <div class="col-md-6">

                                    {{-- KODE --}}
                                    <div class="mb-3">
                                        <label>Kode Barang</label>
                                        <input type="text" id="kode_barang" class="form-control"
                                            value="{{ $peminjaman->keluar->kode_barang ?? '' }}">
                                        <input type="hidden" name="keluar_id" id="keluar_id"
                                            value="{{ $peminjaman->keluar_id }}">
                                    </div>

                                    {{-- NAMA --}}
                                    <div class="mb-3">
                                        <label>Nama Barang</label>
                                        <input type="text" id="nama_barang" class="form-control"
                                            value="{{ $peminjaman->keluar->masuk->kategori->nama_barang ?? '' }}" readonly>
                                    </div>

                                    {{-- JENIS --}}
                                    <div class="mb-3">
                                        <label>Jenis Peminjam</label>
                                        <select id="jenis_perusahaan" class="form-control">
                                            <option value="internal"
                                                {{ $peminjaman->tipe_peminjam == 'internal' ? 'selected' : '' }}>
                                                Internal
                                            </option>
                                            <option value="external"
                                                {{ $peminjaman->tipe_peminjam == 'external' ? 'selected' : '' }}>
                                                Eksternal
                                            </option>
                                        </select>
                                    </div>

                                    {{-- ===== INTERNAL ===== --}}
                                    <div id="form_internal"
                                        class="{{ $peminjaman->tipe_peminjam == 'external' ? 'd-none' : '' }}">

                                        <div class="mb-3 position-relative">
                                            <label>Nama Karyawan</label>
                                            <input type="text" id="nama" class="form-control"
                                                value="{{ $peminjaman->karyawan->nama_karyawan ?? '' }}">
                                            <input type="hidden" name="karyawan_id" id="karyawan_id"
                                                value="{{ $peminjaman->karyawan_id }}">
                                            <div id="hasil_nama" class="autocomplete-box d-none"></div>
                                        </div>

                                        <div class="mb-3">
                                            <label>Perusahaan</label>
                                            <input type="text" class="form-control"
                                                value="{{ auth()->user()->perusahaan->nama_perusahaan ?? '-' }}" readonly>

                                            <input type="hidden" name="perusahaan_id"
                                                value="{{ auth()->user()->perusahaan->id ?? '' }}">
                                        </div>

                                        <div class="mb-3">
                                            <label>Lokasi</label>
                                            <select name="lokasi_id" class="form-control">
                                                @foreach ($lokasis as $l)
                                                    <option value="{{ $l->id }}"
                                                        {{ $peminjaman->lokasi_id == $l->id ? 'selected' : '' }}>
                                                        {{ $l->nama_lokasi }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>

                                    {{-- ===== EXTERNAL ===== --}}
                                    <div id="form_external"
                                        class="{{ $peminjaman->tipe_peminjam == 'external' ? '' : 'd-none' }}">

                                        <div class="mb-3">
                                            <label>Nama Peminjam</label>
                                            <input type="text" name="nama_eksternal" class="form-control"
                                                value="{{ $peminjaman->nama_eksternal }}">
                                        </div>

                                        <div class="mb-3">
                                            <label>Perusahaan</label>
                                            <input type="text" name="perusahaan_eksternal" class="form-control"
                                                value="{{ $peminjaman->perusahaan_eksternal }}">
                                        </div>

                                        <div class="mb-3">
                                            <label>Lokasi</label>
                                            <input type="text" name="lokasi_manual" class="form-control"
                                                value="{{ $peminjaman->lokasi_manual }}">
                                        </div>

                                    </div>

                                </div>

                                {{-- ================= KANAN ================= --}}
                                <div class="col-md-6">

                                    <div class="mb-3">
                                        <label>Tanggal Pinjam</label>
                                        <input type="date" name="tanggal_pinjam" class="form-control"
                                            value="{{ $peminjaman->tanggal_pinjam }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label>Rencana Kembali</label>
                                        <input type="date" name="tanggal_rencana_kembali" class="form-control"
                                            value="{{ $peminjaman->tanggal_rencana_kembali }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
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

                                    <div class="mb-3">
                                        <label>Keperluan</label>
                                        <textarea name="keperluan" class="form-control">{{ $peminjaman->keperluan }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label>Catatan</label>
                                        <textarea name="catatan" class="form-control">{{ $peminjaman->catatan }}</textarea>
                                    </div>

                                </div>

                            </div>

                            <div class="mt-3 text-end">
                                <button class="btn btn-primary">Update</button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const jenis = document.getElementById('jenis_perusahaan');
        const hiddenJenis = document.getElementById('jenis_perusahaan_input');

        const internal = document.getElementById('form_internal');
        const external = document.getElementById('form_external');

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

    });
</script>
