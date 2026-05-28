@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Transaksi Masuk')

@section('content')

    <div class="container-fluid px-3">
        <div class="row">
            <div class="col-12">

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header border-0 px-4 pt-4">
                        <h5 class="text-primary mb-0">Edit Transaksi Masuk</h5>
                        <small class="text-muted">Silakan perbarui data barang masuk</small>
                    </div>

                    <div class="card-body px-4 pb-4">

                        <form action="{{ route('transaksi-masuk.update', $masuk->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">

                                {{-- 🔥 PERUSAHAAN (SUPER ADMIN) --}}
                                @if (auth()->user()->role === 'super_admin')
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Perusahaan</label>

                                        <select name="id_perusahaan" id="perusahaan" class="form-select" required>

                                            @foreach ($perusahaans as $p)
                                                <option value="{{ $p->id }}"
                                                    {{ $masuk->perusahaan_id == $p->id ? 'selected' : '' }}>
                                                    {{ $p->nama_perusahaan }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>
                                @endif

                                {{-- KODE MASUK --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Kode Masuk</label>
                                    <input type="text" name="kode_masuk" class="form-control"
                                        value="{{ $masuk->kode_masuk }}" readonly>
                                </div>

                                {{-- NAMA BARANG --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Nama Barang</label>

                                    <select name="id_kategori" id="kategori"
                                        class="form-select @error('id_kategori') is-invalid @enderror" required>

                                        <option value="">-- Pilih Barang --</option>

                                        @foreach ($kategoris as $kategori)
                                            <option value="{{ $kategori->id }}"
                                                {{ $masuk->id_kategori == $kategori->id ? 'selected' : '' }}>
                                                {{ $kategori->nama_barang }}
                                            </option>
                                        @endforeach

                                    </select>

                                    @error('id_kategori')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- TYPE --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Type</label>
                                    <input type="text" name="type" class="form-control" value="{{ $masuk->type }}"
                                        required>
                                </div>

                                {{-- MEREK --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Merek</label>
                                    <input type="text" name="merek" class="form-control" value="{{ $masuk->merek }}"
                                        required>
                                </div>
                                {{-- KONDISI --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-medium">
                                        Kondisi Asset
                                    </label>

                                    <select name="kondisi" class="form-select" required>

                                        <option value="">
                                            -- Pilih Kondisi --
                                        </option>

                                        <option value="Baru"
                                            {{ old('kondisi', $masuk->kondisi) == 'Baru' ? 'selected' : '' }}>
                                            Baru
                                        </option>

                                        <option value="Bekas"
                                            {{ old('kondisi', $masuk->kondisi) == 'Bekas' ? 'selected' : '' }}>
                                            Bekas
                                        </option>

                                    </select>

                                </div>

                                {{-- JUMLAH --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Jumlah</label>
                                    <input type="number" name="jumlah" class="form-control" value="{{ $masuk->jumlah }}"
                                        required>
                                </div>

                                {{-- TANGGAL --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Tanggal Beli</label>
                                    <input type="date" name="tgl_beli" class="form-control"
                                        value="{{ $masuk->tgl_beli }}" required>
                                </div>

                                {{-- GARANSI --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Garansi</label>
                                    <input type="number" name="garansi" class="form-control" value="{{ $masuk->garansi }}"
                                        required>
                                </div>

                                {{-- SUPPLIER --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Supplier</label>
                                    <input type="text" name="supplier" class="form-control"
                                        value="{{ $masuk->supplier }}" required>
                                </div>

                                {{-- HARGA --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Harga</label>
                                    <input type="text" name="harga" class="form-control" value="{{ $masuk->harga }}"
                                        required>
                                </div>

                              

                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <a href="{{ route('transaksi-masuk.index') }}" class="btn btn-secondary px-4">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
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

            const perusahaan = document.getElementById('perusahaan');
            const kategori = document.getElementById('kategori');

            if (perusahaan) {

                perusahaan.addEventListener('change', function() {

                    let id = this.value;

                    fetch('/dashboard/get-kategori/' + id)
                        .then(res => res.json())
                        .then(data => {

                            let html = '<option value="">-- Pilih Barang --</option>';

                            data.forEach(item => {
                                html +=
                                    `<option value="${item.id}">${item.nama_barang}</option>`;
                            });

                            kategori.innerHTML = html;
                        });

                });

            }

        });
        document.addEventListener('DOMContentLoaded', function() {

            const perusahaan = document.getElementById('perusahaan');
            const kodeInput = document.querySelector('[name="kode_masuk"]');
            const kategori = document.getElementById('kategori');

            if (perusahaan) {

                perusahaan.addEventListener('change', function() {

                    let id = this.value;

                    if (!id) return;

                    // 🔥 UPDATE KODE MASUK
                    fetch('/dashboard/get-kode-masuk/' + id)
                        .then(res => res.json())
                        .then(data => {
                            kodeInput.value = data.kode;
                        });

                    // 🔥 UPDATE KATEGORI
                    fetch('/dashboard/get-kategori/' + id)
                        .then(res => res.json())
                        .then(data => {

                            let html = '<option value="">-- Pilih Barang --</option>';

                            data.forEach(item => {
                                html +=
                                    `<option value="${item.id}">${item.nama_barang}</option>`;
                            });

                            kategori.innerHTML = html;
                        });

                });

            }

        });
    </script>
@endsection
