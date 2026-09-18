@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Penerimaan Aset')

@section('content')

    <style>
        .card-dark {
            border-radius: 16px;
            overflow: hidden;
        }

        .form-control,
        .form-select {
            height: 48px;
            border-radius: 12px;
            border: 1px solid #dbe2ea;
            transition: .2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 .15rem rgba(13, 110, 253, .15);
        }

        .form-label {
            font-size: 13px;
            font-weight: 700;
            color: #5b6475;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .btn {
            border-radius: 12px;
            height: 45px;
            min-width: 110px;
            font-weight: 600;
        }

        .form-section-title {
            font-size: 14px;
            font-weight: 700;
            color: #696cff;
            margin-bottom: 18px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
    </style>

    <div class="container-fluid px-3">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">

                <div class="card card-dark border-0 shadow-sm rounded-4">
                    <div class="card-header border-0 px-4 pt-4 pb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="text-primary mb-0">Edit Penerimaan Aset</h5>
                                <small class="text-muted">Perbarui data penerimaan aset sebelum aset di-mapping</small>
                            </div>
                            <span class="badge bg-label-success px-3 py-2 fs-6">
                                <i class="bx bx-edit me-1"></i> Status: Belum Dimapping (Dapat Diedit)
                            </span>
                        </div>
                    </div>

                    <div class="card-body px-4 pb-4 pt-3">

                        {{-- ALERT PEMBERITAHUAN SEBELUM MAPPING --}}
                        <div class="alert alert-primary d-flex align-items-center mb-4" role="alert">
                            <i class="bx bx-info-circle fs-4 me-3"></i>
                            <div>
                                <strong>Informasi Fleksibilitas Data:</strong> Karena aset ini belum di-mapping ke pengguna atau lokasi manapun, Anda dapat mengubah seluruh informasi (termasuk Kategori, Data Aset, dan Jumlah). Sistem akan otomatis memperbarui kode aset dan nomor inventaris.
                            </div>
                        </div>

                        <form action="{{ route('transaksi-masuk.update', $masuk->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-section-title">
                                Informasi Aset & Pembelian
                            </div>

                            <div class="row">

                                {{-- PERUSAHAAN --}}
                                @if (auth()->user()->role == 'super_admin')
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Perusahaan</label>
                                        <select name="perusahaan_id" id="perusahaan"
                                            class="form-select @error('perusahaan_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Perusahaan --</option>
                                            @foreach ($perusahaans as $p)
                                                <option value="{{ $p->id }}"
                                                    {{ old('perusahaan_id', $masuk->perusahaan_id) == $p->id ? 'selected' : '' }}>
                                                    {{ $p->nama_perusahaan }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('perusahaan_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @else
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Perusahaan</label>
                                        <input type="text" class="form-control bg-light"
                                            value="{{ $masuk->perusahaan->nama_perusahaan ?? '-' }}" readonly>
                                    </div>
                                @endif

                                {{-- JENIS PENERIMAAN --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jenis Penerimaan</label>
                                    <input type="text" class="form-control bg-light" value="{{ $masuk->jenis_masuk }}" readonly>
                                </div>

                                {{-- SUPPLIER / ASAL --}}
                                @if ($masuk->jenis_masuk == 'Pembelian')
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Supplier</label>
                                        <select name="supplier_id" id="supplier"
                                            class="form-select @error('supplier_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Supplier --</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}"
                                                    {{ old('supplier_id', $masuk->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->nama_supplier }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('supplier_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @else
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Perusahaan Asal</label>
                                        <input type="text" class="form-control bg-light"
                                            value="{{ $masuk->perusahaanAsal->nama_perusahaan ?? '-' }}" readonly>
                                    </div>
                                @endif

                                {{-- KATEGORI ASET --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kategori Aset</label>
                                    <select name="kategori_id" id="kategori_id"
                                        class="form-select @error('kategori_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Kategori Aset --</option>
                                        @foreach ($kategoris as $kategori)
                                            <option value="{{ $kategori->id }}"
                                                {{ old('kategori_id', $masuk->dataAset->kategori_id ?? '') == $kategori->id ? 'selected' : '' }}>
                                                {{ $kategori->nama_barang }} ({{ $kategori->kode_barang }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kategori_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- DATA ASET (MEREK & TYPE) --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Data Aset (Merek / Type / Warna)</label>
                                    <select name="data_aset_id" id="data_aset"
                                        class="form-select @error('data_aset_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Data Aset --</option>
                                        @foreach ($dataAsets as $item)
                                            @php $warna = $item->warna ? ' - ' . $item->warna : ''; @endphp
                                            <option value="{{ $item->id }}"
                                                {{ old('data_aset_id', $masuk->data_aset_id) == $item->id ? 'selected' : '' }}>
                                                {{ $item->kategori->nama_barang ?? '' }} - {{ $item->merek }} - {{ $item->type }}{{ $warna }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('data_aset_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- TANGGAL PEMBELIAN --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Pembelian</label>
                                    <input type="date" name="tanggal_pembelian"
                                        class="form-control @error('tanggal_pembelian') is-invalid @enderror"
                                        value="{{ old('tanggal_pembelian', $masuk->tanggal_pembelian) }}" required>
                                    @error('tanggal_pembelian')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- JUMLAH (DAPAT DIEDIT SEBELUM DI-MAPPING) --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jumlah Unit Diterima</label>
                                    <input type="number" name="jumlah" min="1"
                                        class="form-control @error('jumlah') is-invalid @enderror"
                                        value="{{ old('jumlah', $masuk->jumlah) }}" required>
                                    <small class="text-muted">Sebelum di-mapping, Anda bebas menyesuaikan jumlah unit.</small>
                                    @error('jumlah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- HARGA SATUAN --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Harga Satuan (Rp)</label>
                                    <input type="number" name="harga_satuan" min="0"
                                        class="form-control @error('harga_satuan') is-invalid @enderror"
                                        value="{{ old('harga_satuan', $masuk->harga_satuan) }}" required>
                                    @error('harga_satuan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- GARANSI --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Garansi (Bulan)</label>
                                    <input type="number" name="garansi" min="0"
                                        class="form-control @error('garansi') is-invalid @enderror"
                                        value="{{ old('garansi', $masuk->garansi) }}">
                                    @error('garansi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- KET. PENERIMAAN --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Ket. Penerimaan</label>
                                    <select name="ket_penerimaan" class="form-select @error('ket_penerimaan') is-invalid @enderror" required>
                                        <option value="BAIK" {{ old('ket_penerimaan', $masuk->ket_penerimaan) == 'BAIK' ? 'selected' : '' }}>
                                            BAIK
                                        </option>
                                        <option value="RUSAK" {{ old('ket_penerimaan', $masuk->ket_penerimaan) == 'RUSAK' ? 'selected' : '' }}>
                                            RUSAK
                                        </option>
                                    </select>
                                    @error('ket_penerimaan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4 pt-2 border-top">
                                <a href="{{ route('transaksi-masuk.index') }}" class="btn btn-secondary px-4">
                                    <i class="bx bx-x me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bx bx-save me-1"></i> Simpan Perubahan
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
            const supplier = document.getElementById('supplier');
            const kategori = document.getElementById('kategori_id');
            const dataAset = document.getElementById('data_aset');

            const currentDataAsetId = "{{ old('data_aset_id', $masuk->data_aset_id) }}";

            function loadDataAset(targetDataAsetId = null) {
                let perusahaanId = perusahaan ? perusahaan.value : "{{ $masuk->perusahaan_id }}";
                let kategoriId = kategori ? kategori.value : '';

                if (!kategoriId) {
                    dataAset.innerHTML = '<option value="">-- Pilih Kategori Terlebih Dahulu --</option>';
                    return;
                }

                if (perusahaan && !perusahaanId) {
                    dataAset.innerHTML = '<option value="">-- Pilih Perusahaan Terlebih Dahulu --</option>';
                    return;
                }

                dataAset.innerHTML = '<option value="">Loading Data Aset...</option>';

                let url = `/dashboard/get-data-aset/${perusahaanId}?kategori_id=${kategoriId}`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        let html = '<option value="">-- Pilih Data Aset --</option>';

                        if (data.length === 0) {
                            html = '<option value="">Data aset tidak tersedia untuk kategori ini</option>';
                        } else {
                            data.forEach(item => {
                                let warna = item.warna ? ` - ${item.warna}` : '';
                                let kategoriNama = item.kategori ? item.kategori.nama_barang : '';
                                let isSelected = (targetDataAsetId && targetDataAsetId == item.id) ? 'selected' : '';
                                html += `
                                    <option value="${item.id}" ${isSelected}>
                                        ${kategoriNama} - ${item.merek} - ${item.type}${warna}
                                    </option>
                                `;
                            });
                        }

                        dataAset.innerHTML = html;
                    })
                    .catch(error => {
                        console.error(error);
                        dataAset.innerHTML = '<option value="">Gagal memuat data aset</option>';
                    });
            }

            if (kategori) {
                kategori.addEventListener('change', function() {
                    loadDataAset();
                });
            }

            if (perusahaan) {
                perusahaan.addEventListener('change', function() {
                    let perusahaanId = this.value;

                    if (supplier) {
                        supplier.innerHTML = '<option value="">Loading Supplier...</option>';
                    }
                    kategori.innerHTML = '<option value="">Loading Kategori...</option>';
                    dataAset.innerHTML = '<option value="">-- Pilih Kategori Terlebih Dahulu --</option>';

                    if (!perusahaanId) {
                        if (supplier) supplier.innerHTML = '<option value="">-- Pilih Supplier --</option>';
                        kategori.innerHTML = '<option value="">-- Pilih Kategori Aset --</option>';
                        return;
                    }

                    // LOAD SUPPLIER
                    if (supplier) {
                        fetch('/dashboard/get-supplier/' + perusahaanId)
                            .then(response => response.json())
                            .then(data => {
                                let html = '<option value="">-- Pilih Supplier --</option>';
                                if (data.length === 0) {
                                    html = '<option value="">Supplier tidak tersedia</option>';
                                } else {
                                    data.forEach(item => {
                                        html += `<option value="${item.id}">${item.nama_supplier}</option>`;
                                    });
                                }
                                supplier.innerHTML = html;
                            })
                            .catch(error => {
                                console.error(error);
                                supplier.innerHTML = '<option value="">Gagal memuat supplier</option>';
                            });
                    }

                    // LOAD KATEGORI
                    fetch('/dashboard/get-kategori-masuk/' + perusahaanId)
                        .then(response => response.json())
                        .then(data => {
                            let html = '<option value="">-- Pilih Kategori Aset --</option>';
                            if (data.length === 0) {
                                html = '<option value="">Kategori tidak tersedia</option>';
                            } else {
                                data.forEach(item => {
                                    html += `<option value="${item.id}">${item.nama_barang} (${item.kode_barang})</option>`;
                                });
                            }
                            kategori.innerHTML = html;
                        })
                        .catch(error => {
                            console.error(error);
                            kategori.innerHTML = '<option value="">Gagal memuat kategori</option>';
                        });
                });
            }
        });
    </script>
@endsection
