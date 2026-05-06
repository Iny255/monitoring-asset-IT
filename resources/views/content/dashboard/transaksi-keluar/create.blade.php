@extends('layouts/contentNavbarLayout')

@section('title', 'Tambah Transaksi Keluar')

@section('content')

    <div class="card">
        <div class="card-header">
            <h5 class="text-primary mb-0">Tambah Transaksi Keluar</h5>
        </div>

        <div class="card-body">

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('transaksi-keluar.store') }}" method="POST">
                @csrf

                <div class="row">
                    @if (auth()->user()->role === 'super_admin')
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Perusahaan</label>
                            <select name="perusahaan_id" id="perusahaan_select" class="form-select" required>
                                <option value="">-- Pilih Perusahaan --</option>
                                @foreach ($perusahaans as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <!-- KODE -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kode Keluar</label>
                        <input type="text" id="kode_keluar" name="kode_keluar" class="form-control" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kode Masuk</label>
                        <input type="text" id="kode_masuk" class="form-control" required>
                        <input type="hidden" name="id_masuk" id="id_masuk">
                    </div>

                </div>


                <!-- DATA BARANG -->
                <div class="row">

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" id="nama_barang" class="form-control" readonly>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Type</label>
                        <input type="text" id="type" class="form-control" readonly>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Merek</label>
                        <input type="text" id="merek" class="form-control" readonly>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tanggal Beli</label>
                        <input type="text" id="tgl_beli" class="form-control" readonly>
                    </div>

                </div>


                <!-- DATA BARANG KELUAR -->
                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Kode Barang</label>
                        <input type="text" name="kode_barang"
                            class="form-control @error('kode_barang') is-invalid @enderror" placeholder="Kode Barang">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Warna</label>
                        <input type="text" name="warna" class="form-control @error('warna') is-invalid @enderror">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">No Inventaris</label>
                        <input type="text" name="no_inventaris"
                            class="form-control @error('no_inventaris') is-invalid @enderror" placeholder="001">
                    </div>

                </div>


                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Jumlah Keluar</label>
                        <input type="number" name="jumlah" class="form-control" min="1" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Jenis Penerima</label>
                        <select name="jenis_penerima" id="jenis_penerima" class="form-select" required>

                            <option value="">-- Pilih --</option>
                            <option value="Perorangan">Perorangan</option>
                            <option value="Perdivisi">Perdivisi</option>

                        </select>
                    </div>

                </div>


                <!-- PERORANGAN -->
                <div id="group_karyawan">

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nama Karyawan</label>
                            <input type="text" id="nama_karyawan" class="form-control">
                            <input type="hidden" name="id_karyawan" id="id_karyawan">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Divisi</label>
                            <input type="text" id="divisi" class="form-control" readonly>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Perusahaan</label>
                            <input type="text" id="perusahaan" class="form-control" readonly>
                        </div>

                    </div>

                </div>


                <!-- PERDIVISI -->
                <div id="group_divisi" style="display:none;">
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Divisi</label>
                            <input type="text" name="divisi_klr" id="divisi_klr" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Perusahaan</label>

                            {{-- SUPER ADMIN --}}
                            @if (auth()->user()->role === 'super_admin')
                                <select id="perusahaan_select_divisi" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    @foreach ($perusahaans as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                    @endforeach
                                </select>

                                <input type="hidden" name="perusahaan_klr" id="perusahaan_hidden">

                                {{-- PETUGAS --}}
                            @else
                                <input type="text" class="form-control"
                                    value="{{ auth()->user()->perusahaan->nama_perusahaan }}" readonly>

                                <input type="hidden" name="perusahaan_klr"
                                    value="{{ auth()->user()->perusahaan->nama_perusahaan }}">
                            @endif

                        </div>

                    </div>
                </div>


                <!-- KETERANGAN -->

                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2" required></textarea>
                </div>


                <!-- BUTTON -->

                <div class="d-flex justify-content-end gap-2 mt-3">

                    <a href="{{ route('transaksi-keluar.index') }}" class="btn btn-secondary">

                        Kembali

                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save"></i> Simpan
                    </button>

                </div>


            </form>

        </div>

    @endsection

    @section('scripts')
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const perusahaanSelect = document.getElementById('perusahaan_select');
                const kodeKeluar = document.getElementById('kode_keluar');

                // =========================
                // 🔥 KODE KELUAR
                // =========================

                // SUPER ADMIN (hanya di perdivisi)
                if (perusahaanSelect) {
                    perusahaanSelect.addEventListener('change', function() {

                        let id = this.value;
                        if (!id) return;

                        fetch('/dashboard/get-kode-keluar/' + id)
                            .then(res => res.json())
                            .then(data => {

                                // isi kode keluar
                                if (kodeKeluar) kodeKeluar.value = data.kode;

                                // ambil nama perusahaan
                                let text = this.options[this.selectedIndex].text;

                                // isi hidden perusahaan_klr
                                let perusahaanHidden = document.getElementById('perusahaan_hidden');
                                if (perusahaanHidden) perusahaanHidden.value = text;

                            });

                    });
                }

                // PETUGAS (auto tanpa pilih perusahaan)
                else {
                    fetch('/dashboard/get-kode-keluar/{{ auth()->user()->id_perusahaan }}')
                        .then(res => res.json())
                        .then(data => {
                            if (kodeKeluar) kodeKeluar.value = data.kode;
                        });
                }

            });


            // =========================
            // 🔥 AUTOFILL BARANG
            // =========================
            let kodeMasukEl = document.getElementById('kode_masuk');

            if (kodeMasukEl) {
                kodeMasukEl.addEventListener('blur', function() {

                    let kode = this.value.trim();
                    if (!kode) return;

                    fetch("{{ route('transaksi-keluar.autofill') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name=\"csrf-token\"]').content
                            },
                            body: JSON.stringify({
                                kode_masuk: kode
                            })
                        })
                        .then(res => res.json())
                        .then(res => {

                            if (!res.status) {
                                Swal.fire('Gagal', 'Kode masuk tidak ditemukan', 'error');
                                return;
                            }

                            document.getElementById('id_masuk').value = res.data.id_masuk;
                            document.getElementById('nama_barang').value = res.data.nama_barang;
                            document.getElementById('type').value = res.data.type;
                            document.getElementById('merek').value = res.data.merek;
                            document.getElementById('tgl_beli').value = res.data.tgl_beli;
                        });

                });
            }


            // =========================
            // 🔥 AUTOFILL KARYAWAN
            // =========================
            let karyawanEl = document.getElementById('nama_karyawan');

            if (karyawanEl) {
                karyawanEl.addEventListener('blur', function() {

                    let nama = this.value.trim();
                    if (!nama) return;

                    fetch("{{ route('keluar.getKaryawanByNama') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name=\"csrf-token\"]').content
                            },
                            body: JSON.stringify({
                                nama_karyawan: nama
                            })
                        })
                        .then(res => res.json())
                        .then(res => {

                            if (!res.status) {
                                Swal.fire('Gagal', 'Nama karyawan tidak ditemukan', 'error');
                                return;
                            }

                            document.getElementById('id_karyawan').value = res.data.id;
                            document.getElementById('divisi').value = res.data.divisi;
                            document.getElementById('perusahaan').value = res.data.perusahaan;
                        });

                });
            }


            // =========================
            // 🔥 TOGGLE JENIS PENERIMA
            // =========================
            let jenisEl = document.getElementById('jenis_penerima');

            if (jenisEl) {
                jenisEl.addEventListener('change', function() {

                    let jenis = this.value;

                    let groupKaryawan = document.getElementById('group_karyawan');
                    let groupDivisi = document.getElementById('group_divisi');

                    if (jenis === 'Perorangan') {

                        if (groupKaryawan) groupKaryawan.style.display = 'block';
                        if (groupDivisi) groupDivisi.style.display = 'none';

                        // reset divisi
                        if (document.getElementById('divisi_klr'))
                            document.getElementById('divisi_klr').value = '';

                    } else if (jenis === 'Perdivisi') {

                        if (groupKaryawan) groupKaryawan.style.display = 'none';
                        if (groupDivisi) groupDivisi.style.display = 'block';

                        // reset karyawan
                        if (document.getElementById('nama_karyawan'))
                            document.getElementById('nama_karyawan').value = '';

                        if (document.getElementById('id_karyawan'))
                            document.getElementById('id_karyawan').value = '';

                        if (document.getElementById('divisi'))
                            document.getElementById('divisi').value = '';

                        if (document.getElementById('perusahaan'))
                            document.getElementById('perusahaan').value = '';
                    }

                });
            }
        </script>
    @endsection
