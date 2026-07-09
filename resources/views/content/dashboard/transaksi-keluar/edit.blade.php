@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Pemakaian Aset')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .compact-card {
            margin-bottom: 15px;
        }

        .compact-card .card-header {
            padding: 10px 15px;
            font-size: 14px;
            font-weight: 600;
        }

        .compact-card .card-body {
            padding: 15px;
        }

        .compact-form label {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .compact-form .form-control,
        .compact-form .form-select {
            height: 38px;
            font-size: 14px;
        }

        .preview-image {
            max-width: 220px;
            max-height: 220px;
            object-fit: contain;
        }

        .result-box {
            max-height: 200px;
            overflow-y: auto;
        }
    </style>

    <form action="{{ route('transaksi-keluar.update', $keluar->id) }}" method="POST" enctype="multipart/form-data"
        class="compact-form">

        @csrf
        @method('PUT')

        {{-- INFORMASI ASET --}}
        <div class="card compact-card shadow-sm border-0">

            <div class="card-header bg-light">
                Informasi Aset
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4 mb-2">
                        <label>Kode Aset</label>
                        <input type="text" class="form-control" value="{{ $keluar->inventaris->kode_aset }}" readonly>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label>No Inventaris</label>
                        <input type="text" class="form-control" value="{{ $keluar->inventaris->no_inventaris }}"
                            readonly>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label>Status</label>
                        <input type="text" class="form-control" value="{{ $keluar->inventaris->status }}" readonly>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label>Nama Barang</label>
                        <input type="text" class="form-control"
                            value="{{ $keluar->inventaris->dataAset->kategori->nama_barang ?? '-' }}" readonly>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label>Merek</label>
                        <input type="text" class="form-control" value="{{ $keluar->inventaris->dataAset->merek ?? '-' }}"
                            readonly>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label>Type</label>
                        <input type="text" class="form-control" value="{{ $keluar->inventaris->dataAset->type ?? '-' }}"
                            readonly>
                    </div>

                </div>

            </div>

        </div>

        {{-- DETAIL PEMAKAIAN --}}
        <div class="card compact-card shadow-sm border-0">

            <div class="card-header bg-light">
                Detail Pemakaian
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6">
                        <label>Tanggal Keluar</label>
                        <input type="date" name="tgl_keluar" class="form-control"
                            value="{{ old('tgl_keluar', \Carbon\Carbon::parse($keluar->tgl_keluar)->format('Y-m-d')) }}">
                    </div>

                    <div class="col-md-6">
                        <label>Jenis Penerima</label>

                        <select name="jenis_penerima" id="jenis_penerima" class="form-select">

                            <option value="Perorangan" {{ $keluar->jenis_penerima == 'Perorangan' ? 'selected' : '' }}>
                                Perorangan
                            </option>

                            <option value="Perdivisi" {{ $keluar->jenis_penerima == 'Perdivisi' ? 'selected' : '' }}>
                                Perdivisi
                            </option>

                        </select>

                    </div>

                </div>

            </div>

        </div>

        {{-- PENERIMA + FOTO --}}
        <div class="row">

            <div class="col-lg-7">

                <div class="card compact-card shadow-sm border-0">

                    <div class="card-header bg-light">
                        Informasi Penerima
                    </div>

                    <div class="card-body">

                        <div id="group_karyawan">

                            <input type="hidden" name="karyawan_id" id="karyawan_id" value="{{ $keluar->karyawan_id }}">

                            <label>User Aset</label>

                            <input type="text" id="search_karyawan" class="form-control"
                                value="{{ $keluar->karyawan->nama_karyawan ?? '' }}"
                                placeholder="Cari UID / Nama Karyawan">

                            <div id="result_karyawan" class="list-group result-box mt-2"></div>

                        </div>

                        <div id="group_divisi">

                            <label>Divisi</label>

                            <input type="text" name="divisi_klr" class="form-control" value="{{ $keluar->divisi_klr }}">

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-lg-5">

                {{-- LOKASI --}}
                <div class="card compact-card shadow-sm border-0 mb-3">

                    <div class="card-header bg-light">
                        Lokasi Penempatan
                    </div>

                    <div class="card-body">

                        <label class="form-label">Lokasi</label>

                        <select name="lokasi_id" id="lokasi_id" class="form-select" required>

                            @foreach ($lokasis as $lokasi)
                                <option value="{{ $lokasi->id }}"
                                    {{ $keluar->lokasi_id == $lokasi->id ? 'selected' : '' }}>

                                    {{ $lokasi->nama_lokasi }}

                                </option>
                            @endforeach

                        </select>

                    </div>

                </div>

                {{-- FOTO --}}
                <div class="card compact-card shadow-sm border-0">

                    <div class="card-header bg-light">
                        Foto Aset
                    </div>

                    <div class="card-body text-center">

                        @if ($keluar->gambar)
                            <img src="{{ asset('storage/' . $keluar->gambar) }}" class="preview-image img-thumbnail mb-3">
                        @endif

                        <input type="file" name="gambar" class="form-control">

                    </div>

                </div>

            </div>

        </div>

        <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-3">

            <a href="{{ route('transaksi-keluar.index') }}" class="btn btn-secondary">
                Kembali
            </a>

            <button type="submit" class="btn btn-primary">
                Update Data
            </button>

        </div>

    </form>

    </div>

@endsection

@section('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const jenisPenerima = document.getElementById('jenis_penerima');
            const groupKaryawan = document.getElementById('group_karyawan');
            const groupDivisi = document.getElementById('group_divisi');

            function togglePenerima() {

                if (jenisPenerima.value === 'Perorangan') {

                    groupKaryawan.style.display = 'block';
                    groupDivisi.style.display = 'none';

                } else {

                    groupKaryawan.style.display = 'none';
                    groupDivisi.style.display = 'block';

                }

            }

            togglePenerima();

            jenisPenerima.addEventListener('change', togglePenerima);

            // AUTOCOMPLETE KARYAWAN
            $('#search_karyawan').keyup(function() {

                let keyword = $(this).val();

                if (keyword.length < 2) {

                    $('#result_karyawan').html('');
                    return;

                }

                $.post(
                    '/dashboard/search-karyawan', {
                        keyword: keyword,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    function(data) {

                        let rows = data.data ?? data;
                        let html = '';

                        rows.forEach(function(row) {

                            html += `
                        <a href="#"
                           class="list-group-item list-group-item-action pilih-karyawan"
                           data-id="${row.id}"
                           data-nama="${row.nama_karyawan}">

                            <strong>${row.kode_karyawan}</strong>
                            |
                            ${row.nama_karyawan}
                            |
                            ${row.divisi}

                        </a>
                    `;

                        });

                        $('#result_karyawan').html(html);

                    }
                );

            });

            $(document).on(
                'click',
                '.pilih-karyawan',
                function(e) {

                    e.preventDefault();

                    $('#karyawan_id').val(
                        $(this).data('id')
                    );

                    $('#search_karyawan').val(
                        $(this).data('nama')
                    );

                    $('#result_karyawan').html('');

                }
            );

        });
    </script>

@endsection
