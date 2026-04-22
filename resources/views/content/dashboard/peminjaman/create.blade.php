@extends('layouts/contentNavbarLayout')

@section('title', 'Pengajuan Peminjaman Barang')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/peminjaman.css') }}">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-10 col-md-12">

                <!-- CARD -->
                <div class="card border-0 shadow-sm rounded-4">

                    <div class="card-header border-0 px-4 pt-4">
                        <h5 class="text-primary mb-0">Pengajuan Peminjaman</h5>
                        <small class="text-muted">
                            Silakan isi data peminjaman barang
                        </small>
                    </div>


                    <div class="card-body px-4 pb-4">

                        <form action="{{ route('peminjaman.store') }}" method="POST">
                            @csrf

                            <div class="row">

                                {{-- KIRI --}}
                                <div class="col-md-6">

                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Kode Barang</label>
                                        <input type="text" id="kode_barang" class="form-control"
                                            placeholder="Ketik kode barang...">
                                        <input type="hidden" name="keluar_id" id="keluar_id">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Nama Barang</label>
                                        <input type="text" id="nama_barang" class="form-control" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Tipe Peminjam</label>
                                        <select id="tipe_peminjam" name="tipe_peminjam" class="form-control">
                                            <option value="internal">Karyawan Internal</option>
                                            <option value="eksternal">Pihak Eksternal</option>
                                        </select>
                                    </div>

                                    {{-- ================= INTERNAL (AUTOCOMPLETE) ================= --}}
                                    <div id="field_karyawan" class="mb-3 position-relative">
                                        <label class="form-label">Peminjam (Karyawan)</label>
                                        <input type="text" id="nama" class="form-control" autocomplete="off">
                                        <input type="hidden" name="karyawan_id" id="karyawan_id">
                                        <div id="hasil_nama" class="autocomplete-box d-none"></div>
                                    </div>

                                    {{-- ================= EKSTERNAL ================= --}}
                                    <div id="field_eksternal" class="mb-3 d-none">
                                        <label class="form-label">Nama Peminjam</label>
                                        <input type="text" name="nama_eksternal" class="form-control">
                                    </div>

                                    <div id="field_perusahaan" class="mb-3 d-none">
                                        <label class="form-label">Asal Perusahaan</label>
                                        <input type="text" name="perusahaan_eksternal" class="form-control">
                                    </div>

                                    {{-- LOKASI --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Lokasi</label>
                                        <select name="lokasi_id" class="form-control" required>
                                            <option value="">-- Pilih Lokasi --</option>
                                            @foreach ($lokasis as $l)
                                                <option value="{{ $l->id }}">
                                                    {{ $l->nama_lokasi }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>


                                {{-- KANAN --}}
                                <div class="col-md-6">

                                    {{-- TANGGAL PINJAM --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Tanggal Pinjam</label>
                                        <input type="date" name="tanggal_pinjam" class="form-control" required>
                                    </div>

                                    {{-- RENCANA KEMBALI --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Rencana Kembali</label>
                                        <input type="date" name="tanggal_rencana_kembali" class="form-control" required>
                                    </div>

                                    {{-- KEPERLUAN --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Keperluan</label>
                                        <textarea name="keperluan" class="form-control" rows="2"
                                            placeholder="Contoh: Operasional, Meeting, dll"></textarea>
                                    </div>

                                    {{-- CATATAN --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Catatan</label>
                                        <textarea name="catatan" class="form-control" rows="2"></textarea>
                                    </div>

                                </div>

                            </div>


                            {{-- BUTTON --}}
                            <div class="d-flex justify-content-end gap-2 mt-2">
                                <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary px-4">
                                    Batal
                                </a>

                                <button type="submit" class="btn btn-primary px-4">
                                    Simpan
                                </button>
                            </div>

                        </form>

                        <!-- END CARD -->

                    </div>
                </div>
            </div>

@endsection
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


        <script>
            document.addEventListener('DOMContentLoaded', function () {

                let swalShown = false; // supaya swal hanya muncul sekali
                let lastKode = null; // supaya tidak request berulang

                const kodeInput = document.getElementById('kode_barang');
                const namaInput = document.getElementById('nama_barang');
                const keluarId = document.getElementById('keluar_id');

                namaInput.addEventListener('focus', function () {

                    let kode = kodeInput.value.trim();

                    if (!kode) return;

                    // cegah request berulang jika kode sama
                    if (lastKode === kode) return;
                    lastKode = kode;

                    fetch(`/dashboard/peminjaman/get-nama-barang/${kode}`)
                        .then(res => res.json())
                        .then(data => {

                            if (data.status === 'ok') {
                                namaInput.value = data.nama_barang;
                                keluarId.value = data.keluar_id;
                                swalShown = false; // reset jika data ketemu
                            } else {
                                namaInput.value = '';
                                keluarId.value = '';

                                if (!swalShown) {
                                    swalShown = true;

                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Data Tidak Ditemukan',
                                        text: 'Kode barang tidak ditemukan',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            }
                        })
                        .catch(() => {

                            namaInput.value = '';
                            keluarId.value = '';

                            if (!swalShown) {
                                swalShown = true;

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Gagal mengambil data barang'
                                });
                            }
                        });

                });

            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const inputNama = document.getElementById('nama');
                const inputId = document.getElementById('karyawan_id');
                const resultBox = document.getElementById('hasil_nama');

                if (!inputNama || !inputId || !resultBox) return;

                let debounce;

                inputNama.addEventListener('input', function () {

                    let keyword = this.value.trim();

                    if (keyword.length < 2) {
                        closeDropdown();
                        return;
                    }

                    clearTimeout(debounce);

                    debounce = setTimeout(() => {

                        fetch(
                            `{{ url('/dashboard/peminjaman/search-karyawan') }}?q=${encodeURIComponent(keyword)}`
                        )
                            .then(res => res.json())
                            .then(data => {

                                resultBox.innerHTML = '';

                                if (!data.length) {
                                    resultBox.innerHTML = `
                            <div class="p-2 text-muted small">
                                Data tidak ditemukan
                            </div>`;
                                    resultBox.classList.remove('d-none');
                                    return;
                                }

                                data.forEach(item => {

                                    const div = document.createElement('div');
                                    div.className = 'autocomplete-item';
                                    div.textContent = item.nama_karyawan;

                                    div.addEventListener('mousedown', function (e) {
                                        e.preventDefault();
                                        inputNama.value = item.nama_karyawan;
                                        inputId.value = item.id;
                                        closeDropdown();
                                    });

                                    resultBox.appendChild(div);
                                });

                                resultBox.classList.remove('d-none');
                            })
                            .catch(err => {
                                console.log(err);
                                closeDropdown();
                            });

                    }, 300);
                });

                function closeDropdown() {
                    resultBox.innerHTML = '';
                    resultBox.classList.add('d-none');
                }

                document.addEventListener('click', function (e) {
                    if (!resultBox.contains(e.target) && e.target !== inputNama) {
                        closeDropdown();
                    }
                });

            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const kodeInput = document.getElementById('kode_barang');
                const namaInput = document.getElementById('nama_barang');
                const keluarId = document.getElementById('keluar_id');

                if (!kodeInput) return;

                let lastKode = null;
                let swalShown = false;

                kodeInput.addEventListener('blur', function () {

                    let kode = this.value.trim();
                    if (!kode) return;

                    if (kode === lastKode) return;
                    lastKode = kode;

                    // 1️⃣ CEK STATUS DULU
                    fetch(`/peminjaman/cek-status/${kode}`)
                        .then(res => res.json())
                        .then(status => {

                            // ❌ MASIH DIPINJAM
                            if (status.dipinjam) {

                                if (!swalShown) {
                                    swalShown = true;

                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Barang Belum Dikembalikan',
                                        text: 'Kode barang ini masih dipinjam dan belum dikembalikan',
                                        confirmButtonColor: '#d33'
                                    });
                                }

                                kodeInput.value = '';
                                namaInput.value = '';
                                keluarId.value = '';
                                kodeInput.focus();
                                return; // STOP JANGAN AMBIL NAMA BARANG
                            }

                            // 2️⃣ JIKA TIDAK DIPINJAM → BARU AMBIL NAMA BARANG
                            fetch(`/dashboard/peminjaman/get-nama-barang/${kode}`)
                                .then(res => res.json())
                                .then(data => {

                                    if (data.status === 'ok') {
                                        namaInput.value = data.nama_barang;
                                        keluarId.value = data.keluar_id;
                                        swalShown = false;
                                    } else {

                                        namaInput.value = '';
                                        keluarId.value = '';

                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Data Tidak Ditemukan',
                                            text: 'Kode barang tidak ditemukan'
                                        });
                                    }

                                });

                        })
                        .catch(() => {
                            console.log('Gagal cek status barang');
                        });

                });

            });

        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                // ================= SWITCH TIPE =================
                const tipe = document.getElementById('tipe_peminjam');
                const fieldKaryawan = document.getElementById('field_karyawan');
                const fieldEksternal = document.getElementById('field_eksternal');
                const fieldPerusahaan = document.getElementById('field_perusahaan');

                tipe.addEventListener('change', function () {
                    if (this.value === 'internal') {
                        fieldKaryawan.classList.remove('d-none');
                        fieldEksternal.classList.add('d-none');
                        fieldPerusahaan.classList.add('d-none');
                    } else {
                        fieldKaryawan.classList.add('d-none');
                        fieldEksternal.classList.remove('d-none');
                        fieldPerusahaan.classList.remove('d-none');
                    }
                });

                // ================= AUTOCOMPLETE =================
                const inputNama = document.getElementById('nama');
                const inputId = document.getElementById('karyawan_id');
                const resultBox = document.getElementById('hasil_nama');

                let debounce;

                inputNama.addEventListener('input', function () {

                    let keyword = this.value.trim();

                    if (keyword.length < 2) {
                        closeDropdown();
                        return;
                    }

                    clearTimeout(debounce);

                    debounce = setTimeout(() => {

                        fetch("{{ route('peminjaman.search') }}?q=" + keyword)
                            .then(res => res.json())
                            .then(data => {

                                resultBox.innerHTML = '';

                                if (!data.length) {
                                    resultBox.innerHTML = `<div class="p-2 text-muted small">Tidak ditemukan</div>`;
                                    resultBox.classList.remove('d-none');
                                    return;
                                }

                                data.forEach(item => {
                                    const div = document.createElement('div');
                                    div.className = 'autocomplete-item';
                                    div.textContent = item.nama_karyawan;

                                    div.addEventListener('mousedown', function (e) {
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

                document.addEventListener('click', function (e) {
                    if (!resultBox.contains(e.target) && e.target !== inputNama) {
                        closeDropdown();
                    }
                });

            });
        </script>