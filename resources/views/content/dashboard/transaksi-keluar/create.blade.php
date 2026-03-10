@extends('layouts/contentNavbarLayout')

@section('title', 'Tambah Transaksi Keluar')

@section('content')

<div class="card">
    <div class="card-header">
        <h5 style="text-primary mb-0">Tambah Transaksi Keluar</h5>
    </div>

    <div class="card-body">
        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form action="{{ route('transaksi-keluar.store') }}" method="POST">
            @csrf

            {{-- KODE KELUAR --}}
            <div class="mb-3">
                <label class="form-label">Kode Keluar</label>
                <input type="text"
                    name="kode_keluar"
                    class="form-control"
                    value="{{ $kodeKeluar }}"
                    readonly>
            </div>

            {{-- KODE MASUK --}}
            <div class="mb-3">
                <label class="form-label">Kode Masuk</label>
                <input type="text" id="kode_masuk" class="form-control" required>
                <input type="hidden" name="id_masuk" id="id_masuk">
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nama Barang</label>
                    <input type="text" id="nama_barang" class="form-control" readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Type</label>
                    <input type="text" id="type" class="form-control" readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Merek</label>
                    <input type="text" id="merek" class="form-control" readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Tanggal Beli</label>
                    <input type="text" id="tgl_beli" class="form-control" readonly>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Kode Barang</label>
                <input type="text"
                    name="kode_barang"
                    class="form-control form-control-lg @error('kode_barang') is-invalid @enderror"
                    placeholder="Masukkan Kode Barang"
                    value="{{ old('kode_barang') }}">

                @error('kode_barang')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Warna</label>
                <input type="text"
                    name="warna"
                    class="form-control form-control-lg @error('warna') is-invalid @enderror"
                    placeholder="Tulis Warna"
                    value="{{ old('warna') }}">

                @error('warna')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">No Inventaris</label>
                <input type="text"
                    name="no_inventaris"
                    class="form-control form-control-lg @error('no_inventaris') is-invalid @enderror"
                    placeholder="001"
                    value="{{ old('no-inventaris') }}">

                @error('no_inventaris')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- JUMLAH --}}
            <div class="mb-3">
                <label class="form-label">Jumlah Keluar</label>
                <input type="number" name="jumlah" class="form-control" min="1" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Jenis Penerima</label>
                <select name="jenis_penerima" id="jenis_penerima" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    <option value="Perorangan">Perorangan</option>
                    <option value="Perdivisi">Perdivisi</option>
                </select>
            </div>

            {{-- PERORANGAN --}}
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


            {{-- DIVISI --}}
            <div id="group_divisi" style="display:none;">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Divisi</label>
                        <input type="text" name="divisi_klr" id="divisi_klr" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Perusahaan</label>
                        <input type="text" name="perusahaan_klr" id="perusahaan_klr" class="form-control">
                    </div>
                </div>
            </div>

            {{-- KETERANGAN --}}
            <div class="mb-3">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3" required></textarea>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">
                    <i class="bx bx-save"></i> Simpan
                </button>

                <a href="{{ route('transaksi-keluar.index') }}" class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </form>
    </div>
</div>

@endsection

@section('scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
    document.getElementById('kode_masuk').addEventListener('blur', function() {

        let kode = this.value.trim();
        if (!kode) return;

        fetch("{{ route('transaksi-keluar.autofill') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
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

    // KARYAWAN
    document.getElementById('nama_karyawan').addEventListener('blur', function() {

        let nama = this.value.trim();
        if (!nama) return;

        fetch("{{ route('keluar.getKaryawanByNama') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
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

    document.getElementById('jenis_penerima').addEventListener('change', function() {

        let jenis = this.value;

        let groupKaryawan = document.getElementById('group_karyawan');
        let groupDivisi = document.getElementById('group_divisi');

        if (jenis === 'Perorangan') {

            groupKaryawan.style.display = 'block';
            groupDivisi.style.display = 'none';

            document.getElementById('nama_karyawan').required = true;
            document.getElementById('divisi_klr').required = false;
            document.getElementById('perusahaan_klr').required = false;

        } else if (jenis === 'Perdivisi') {

            groupKaryawan.style.display = 'none';
            groupDivisi.style.display = 'block';

            document.getElementById('nama_karyawan').required = false;

            // reset karyawan
            document.getElementById('nama_karyawan').value = '';
            document.getElementById('id_karyawan').value = '';
            document.getElementById('divisi').value = '';
            document.getElementById('perusahaan').value = '';
        }
    });
</script>



@endsection