@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Penerimaan Aset')

@section('content')

    <div class="container-fluid px-3">
        <div class="row">
            <div class="col-12">

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header border-0 px-4 pt-4">
                        <h5 class="text-primary mb-0">Edit Penerimaan Aset</h5>
                        <small class="text-muted">Silakan perbarui data penerimaan aset</small>
                    </div>

                    <div class="card-body px-4 pb-4">

                        <form action="{{ route('transaksi-masuk.update', $masuk->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">

                                <div class="row">

                                    {{-- PERUSAHAAN --}}
                                    @if (auth()->user()->role == 'super_admin')
                                        <div class="col-md-6 mb-3">

                                            <label class="form-label">
                                                Perusahaan
                                            </label>

                                            <input type="text" class="form-control"
                                                value="{{ $masuk->perusahaan->nama_perusahaan }}" readonly>

                                        </div>
                                    @endif
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jenis Penerimaan</label>

                                        <input type="text" class="form-control" value="{{ $masuk->jenis_masuk }}"
                                            readonly>
                                    </div>


                                    {{-- SUPPLIER --}}
                                    @if ($masuk->jenis_masuk == 'Pembelian')

                                        <div class="col-md-6 mb-3">

                                            <label class="form-label">
                                                Supplier
                                            </label>

                                            <select name="supplier_id" class="form-select">

                                                @foreach ($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}"
                                                        {{ $supplier->id == $masuk->supplier_id ? 'selected' : '' }}>

                                                        {{ $supplier->nama_supplier }}

                                                    </option>
                                                @endforeach

                                            </select>

                                        </div>
                                    @else
                                        <div class="col-md-6 mb-3">

                                            <label class="form-label">
                                                Perusahaan Asal
                                            </label>

                                            <input type="text" class="form-control"
                                                value="{{ $masuk->perusahaanAsal->nama_perusahaan }}" readonly>

                                        </div>

                                    @endif

                                    {{-- DATA ASET --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">
                                            Data Aset
                                        </label>

                                        <input type="text" class="form-control"
                                            value="{{ $masuk->dataAset->kategori->nama_barang }} - {{ $masuk->dataAset->merek }} - {{ $masuk->dataAset->type }}{{ $masuk->dataAset->warna ? ' - ' . $masuk->dataAset->warna : '' }}"
                                            readonly>

                                    </div>

                                    {{-- JUMLAH --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">
                                            Jumlah
                                        </label>

                                        <input type="number" class="form-control" value="{{ $masuk->jumlah }}" readonly>

                                    </div>

                                    {{-- TANGGAL PEMBELIAN --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">
                                            Tanggal Pembelian
                                        </label>

                                        <input type="date" name="tanggal_pembelian" class="form-control"
                                            value="{{ old('tanggal_pembelian', $masuk->tanggal_pembelian) }}" required>

                                    </div>

                                    {{-- HARGA SATUAN --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">
                                            Harga Satuan
                                        </label>

                                        <input type="number" name="harga_satuan" class="form-control"
                                            value="{{ $masuk->harga_satuan }}" required>

                                    </div>

                                    {{-- GARANSI --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">
                                            Garansi (Bulan)
                                        </label>

                                        <input type="number" name="garansi" class="form-control"
                                            value="{{ $masuk->garansi }}">

                                    </div>
                                    {{-- KET PENERIMAAN --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">
                                            Ket. Penerimaan
                                        </label>

                                        <select name="ket_penerimaan" class="form-select" required>

                                            <option value="BAIK"
                                                {{ $masuk->ket_penerimaan == 'BAIK' ? 'selected' : '' }}>
                                                BAIK
                                            </option>

                                            <option value="RUSAK"
                                                {{ $masuk->ket_penerimaan == 'RUSAK' ? 'selected' : '' }}>
                                                RUSAK
                                            </option>

                                        </select>

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

            const perusahaanSelect = document.getElementById('perusahaan');
            const dataAsetSelect = document.getElementById('data_aset');

            if (perusahaanSelect) {

                perusahaanSelect.addEventListener('change', function() {

                    let perusahaanId = this.value;

                    fetch('/dashboard/get-data-aset/' + perusahaanId)

                        .then(response => response.json())

                        .then(data => {

                            let html =
                                '<option value="">-- Pilih Data Aset --</option>';

                            data.forEach(item => {
                                let warna = item.warna ? ` - ${item.warna}` : '';
                                html += `
                            <option value="${item.id}">
                                ${item.kategori.nama_barang}
                                - ${item.merek}
                                - ${item.type}${warna}
                            </option>
                        `;

                            });

                            dataAsetSelect.innerHTML = html;

                        });

                });

            }

        });
    </script>

@endsection
