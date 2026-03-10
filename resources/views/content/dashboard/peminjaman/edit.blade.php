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

                        <div class="row">

                            {{-- ================= KIRI ================= --}}
                            <div class="col-md-6">

                                {{-- KODE BARANG (AUTOFILL) --}}
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Kode Barang</label>
                                    <input type="text"
                                        id="kode_barang"
                                        class="form-control"
                                        value="{{ $peminjaman->keluar->kode_barang ?? '' }}"
                                        autocomplete="off">

                                    <input type="hidden"
                                        name="keluar_id"
                                        id="keluar_id"
                                        value="{{ $peminjaman->keluar_id }}">
                                </div>

                                {{-- NAMA BARANG --}}
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Nama Barang</label>
                                    <input type="text"
                                        id="nama_barang"
                                        class="form-control"
                                        value="{{ $peminjaman->keluar->masuk->kategori->nama_barang ?? '' }}"
                                        readonly>
                                </div>

                                {{-- PEMINJAM AUTOCOMPLETE --}}
                                <div class="mb-3 position-relative">
                                    <label class="form-label fw-medium">Peminjam</label>

                                    <input type="text"
                                        id="nama"
                                        class="form-control"
                                        value="{{ $peminjaman->karyawan->nama_karyawan ?? '' }}"
                                        autocomplete="off">

                                    <input type="hidden"
                                        name="karyawan_id"
                                        id="karyawan_id"
                                        value="{{ $peminjaman->karyawan_id }}">

                                    <div id="hasil_nama" class="autocomplete-box d-none"></div>
                                </div>

                                {{-- PERUSAHAAN --}}
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Perusahaan</label>
                                    <select name="perusahaan_id" class="form-control" required>
                                        @foreach($perusahaans as $p)
                                        <option value="{{ $p->id }}"
                                            {{ $peminjaman->perusahaan_id == $p->id ? 'selected' : '' }}>
                                            {{ $p->nama_perusahaan }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- LOKASI --}}
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Lokasi</label>
                                    <select name="lokasi_id" class="form-control" required>
                                        @foreach($lokasis as $l)
                                        <option value="{{ $l->id }}"
                                            {{ $peminjaman->lokasi_id == $l->id ? 'selected' : '' }}>
                                            {{ $l->nama_lokasi }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>


                            {{-- ================= KANAN ================= --}}
                            <div class="col-md-6">

                                <div class="mb-3">
                                    <label class="form-label fw-medium">Tanggal Pinjam</label>
                                    <input type="date"
                                        name="tanggal_pinjam"
                                        class="form-control"
                                        value="{{ $peminjaman->tanggal_pinjam }}"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-medium">Rencana Kembali</label>
                                    <input type="date"
                                        name="tanggal_rencana_kembali"
                                        class="form-control"
                                        value="{{ $peminjaman->tanggal_rencana_kembali }}"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-medium">Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="Dipinjam" {{ $peminjaman->status=='Dipinjam'?'selected':'' }}>Dipinjam</option>
                                        <option value="Dikembalikan" {{ $peminjaman->status=='Dikembalikan'?'selected':'' }}>Dikembalikan</option>
                                        <option value="Hilang" {{ $peminjaman->status=='Hilang'?'selected':'' }}>Hilang</option>
                                        <option value="Rusak" {{ $peminjaman->status=='Rusak'?'selected':'' }}>Rusak</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-medium">Keperluan</label>
                                    <textarea name="keperluan" class="form-control" rows="2">{{ $peminjaman->keperluan }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-medium">Catatan</label>
                                    <textarea name="catatan" class="form-control" rows="2">{{ $peminjaman->catatan }}</textarea>
                                </div>

                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-4">Update</button>
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

        const kodeInput = document.getElementById('kode_barang');
        const namaInput = document.getElementById('nama_barang');
        const keluarId = document.getElementById('keluar_id');

        if (!kodeInput) return;

        let lastKode = null;
        let swalShown = false;

        kodeInput.addEventListener('blur', function() {

            let kode = this.value.trim();
            if (!kode) return;

            if (kode === lastKode) return;
            lastKode = kode;

            // 1️⃣ CEK STATUS MASIH DIPINJAM
            fetch(`/peminjaman/cek-status/${kode}`)
                .then(res => res.json())
                .then(status => {

                    if (status.dipinjam) {

                        Swal.fire({
                            icon: 'warning',
                            title: 'Barang Masih Dipinjam',
                            text: 'Barang ini belum dikembalikan',
                            confirmButtonColor: '#d33'
                        });

                        kodeInput.value = '';
                        namaInput.value = '';
                        keluarId.value = '';
                        kodeInput.focus();
                        return;
                    }

                    // 2️⃣ AMBIL NAMA BARANG
                    fetch(`/peminjaman/get-nama-barang/${kode}`)
                        .then(res => res.json())
                        .then(data => {

                            if (data.status === 'ok') {
                                namaInput.value = data.nama_barang;
                                keluarId.value = data.keluar_id;
                            } else {

                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Tidak Ditemukan',
                                    text: 'Kode barang tidak ada'
                                });

                                namaInput.value = '';
                                keluarId.value = '';
                            }
                        });

                })
                .catch(() => {
                    console.log('Gagal cek status');
                });

        });

    });
</script>


//peminjam
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const inputNama = document.getElementById('nama');
        const inputId = document.getElementById('karyawan_id');
        const resultBox = document.getElementById('hasil_nama');

        if (!inputNama || !inputId || !resultBox) return;

        inputNama.addEventListener('keyup', function() {

            let keyword = this.value.trim();

            if (keyword.length < 2) {
                closeDropdown();
                return;
            }

            fetch(`/peminjaman/search-karyawan?q=${encodeURIComponent(keyword)}`)
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
                        div.dataset.id = item.id;

                        div.addEventListener('mousedown', function(e) {
                            e.preventDefault();
                            inputNama.value = item.nama_karyawan;
                            inputId.value = item.id;
                            closeDropdown();
                        });

                        resultBox.appendChild(div);
                    });

                    resultBox.classList.remove('d-none');
                })
                .catch(() => closeDropdown());
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