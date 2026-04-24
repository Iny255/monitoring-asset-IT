@extends('layouts/contentNavbarLayout')

@section('title', 'Pengajuan Peminjaman Barang')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/peminjaman.css') }}">

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-10">

                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="text-primary mb-0">Pengajuan Peminjaman</h5>
                    </div>

                    <div class="card-body">

                        <form action="{{ route('peminjaman.store') }}" method="POST">
                            @csrf

                            {{-- 🔥 TIPE --}}
                            <input type="hidden" name="jenis_perusahaan" id="jenis_perusahaan_input" value="internal">

                            <div class="row">

                                {{-- ================= KIRI ================= --}}
                                <div class="col-md-6">

                                    {{-- KODE --}}
                                    <div class="mb-3">
                                        <label>Kode Barang</label>
                                        <input type="text" id="kode_barang" class="form-control">
                                        <input type="hidden" name="keluar_id" id="keluar_id">
                                    </div>

                                    {{-- NAMA --}}
                                    <div class="mb-3">
                                        <label>Nama Barang</label>
                                        <input type="text" id="nama_barang" class="form-control" readonly>
                                    </div>

                                    {{-- JENIS --}}
                                    <div class="mb-3">
                                        <label>Jenis Peminjam</label>
                                        <select id="jenis_perusahaan" class="form-control">
                                            <option value="internal">Internal</option>
                                            <option value="external">Eksternal</option>
                                        </select>
                                    </div>

                                    {{-- ===== INTERNAL ===== --}}
                                    <div id="form_internal">

                                        <div class="mb-3 position-relative">
                                            <label>Nama Karyawan</label>
                                            <input type="text" id="nama" class="form-control" autocomplete="off">
                                            <input type="hidden" name="karyawan_id" id="karyawan_id">
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
                                                    <option value="{{ $l->id }}">{{ $l->nama_lokasi }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>

                                    {{-- ===== EXTERNAL ===== --}}
                                    <div id="form_external" class="d-none">

                                        <div class="mb-3">
                                            <label>Nama Peminjam</label>
                                            <input type="text" name="nama_eksternal" class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <label>Perusahaan</label>
                                            <input type="text" name="perusahaan_eksternal" class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <label>Lokasi</label>
                                            <input type="text" name="lokasi_manual" class="form-control">
                                        </div>

                                    </div>

                                </div>

                                {{-- ================= KANAN ================= --}}
                                <div class="col-md-6">

                                    <div class="mb-3">
                                        <label>Tanggal Pinjam</label>
                                        <input type="date" name="tanggal_pinjam" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label>Rencana Kembali</label>
                                        <input type="date" name="tanggal_rencana_kembali" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label>Keperluan</label>
                                        <textarea name="keperluan" class="form-control"></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label>Catatan</label>
                                        <textarea name="catatan" class="form-control"></textarea>
                                    </div>

                                </div>

                            </div>

                            <div class="mt-3 text-end">
                                <button class="btn btn-primary">Simpan</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const kodeInput = document.getElementById('kode_barang');
        const namaInput = document.getElementById('nama_barang');
        const keluarId = document.getElementById('keluar_id');

        const jenis = document.getElementById('jenis_perusahaan');
        const hiddenJenis = document.getElementById('jenis_perusahaan_input');

        const internal = document.getElementById('form_internal');
        const external = document.getElementById('form_external');

        const inputNama = document.getElementById('nama');
        const inputId = document.getElementById('karyawan_id');
        const resultBox = document.getElementById('hasil_nama');

        // ================= SWITCH INTERNAL / EXTERNAL =================
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

        // ================= CEK BARANG =================
        kodeInput.addEventListener('blur', function() {

            let kode = this.value.trim();
            if (!kode) return;

            fetch(`/peminjaman/cek-status/${kode}`)
                .then(res => res.json())
                .then(res => {

                    if (res.dipinjam) {

                        Swal.fire({
                            icon: 'warning',
                            title: 'Barang sedang dipinjam!',
                            text: 'Tidak bisa dipinjam ulang'
                        });

                        kodeInput.value = '';
                        namaInput.value = '';
                        keluarId.value = '';
                        return;
                    }

                    fetch(`/dashboard/peminjaman/get-nama-barang/${kode}`)
                        .then(res => res.json())
                        .then(data => {

                            if (data.status === 'ok') {
                                namaInput.value = data.nama_barang;
                                keluarId.value = data.keluar_id;
                            } else {
                                Swal.fire('Kode tidak ditemukan');
                            }

                        });

                });

        });

        // ================= AUTOCOMPLETE =================
        let debounce;

        inputNama.addEventListener('input', function() {

            let keyword = this.value.trim();

            if (keyword.length < 2) {
                closeDropdown();
                return;
            }

            clearTimeout(debounce);

            debounce = setTimeout(() => {

                fetch(`/dashboard/peminjaman/search-karyawan?q=${keyword}`)
                    .then(res => res.json())
                    .then(data => {

                        resultBox.innerHTML = '';

                        if (!data.length) {
                            resultBox.innerHTML =
                                `<div class="p-2 text-muted">Tidak ditemukan</div>`;
                            resultBox.classList.remove('d-none');
                            return;
                        }

                        data.forEach(item => {

                            let div = document.createElement('div');
                            div.classList.add('autocomplete-item');
                            div.textContent = item.nama_karyawan;

                            div.addEventListener('mousedown', function(e) {
                                e.preventDefault();
                                inputNama.value = item.nama_karyawan;
                                inputId.value = item.id;
                                closeDropdown();
                            });

                            resultBox.appendChild(div);
                        });

                        resultBox.classList.remove('d-none');

                    });

            }, 300);

        });

        function closeDropdown() {
            resultBox.innerHTML = '';
            resultBox.classList.add('d-none');
        }

        document.addEventListener('click', function(e) {
            if (!resultBox.contains(e.target) && e.target !== inputNama) {
                closeDropdown();
            }
        });

    });
</script>
