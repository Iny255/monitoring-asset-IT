@extends('layouts/contentNavbarLayout')

@section('title', 'Tambah Peminjaman')

@section('content')

    <form action="{{ route('peminjaman.store') }}" method="POST">
        @csrf

        <div class="row">

            {{-- =======================
            INFORMASI PEMINJAMAN
        ======================== --}}
            <div class="col-lg-7">

                <div class="card mb-4">

                    <div class="card-header">
                        <h5 class="mb-0">Informasi Peminjaman</h5>
                        <small class="text-muted">
                            Lengkapi data transaksi peminjaman.
                        </small>
                    </div>

                    <div class="card-body">

                        <div class="row">

                            {{-- Jenis --}}
                            <div class="row mb-3">

                                {{-- Jenis Peminjaman --}}
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Jenis Peminjaman
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select class="form-select" name="jenis_peminjaman" id="jenis_peminjaman">

                                        <option value="">-- Pilih --</option>

                                        <option value="internal">
                                            Internal
                                        </option>

                                        <option value="antar_perusahaan">
                                            Antar Perusahaan
                                        </option>

                                    </select>

                                </div>

                                {{-- Jenis Aset --}}
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Jenis Aset
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select class="form-select" id="kategori_id">

                                        <option value="">
                                            -- Pilih Jenis Aset --
                                        </option>

                                        @foreach ($kategoris as $kategori)
                                            <option value="{{ $kategori->id }}">

                                                {{ $kategori->nama_barang }}

                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                            </div>

                            {{-- Inventaris --}}
                            <div class="row mb-3">

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Inventaris
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select class="form-select" name="inventaris_id" id="inventaris_id">

                                        <option value="">

                                            Pilih Jenis Aset Terlebih Dahulu

                                        </option>

                                    </select>

                                </div>

                            </div>

                            {{-- Tanggal Pinjam --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Tanggal Pinjam
                                </label>

                                <input type="date" name="tanggal_pinjam" class="form-control"
                                    value="{{ date('Y-m-d') }}">

                            </div>

                            {{-- Tanggal Rencana --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Tanggal Rencana Kembali
                                </label>

                                <input type="date" name="tanggal_rencana_kembali" class="form-control">

                            </div>

                            {{-- Internal --}}
                            <div class="col-md-12 mb-3" id="internal-section" style="display:none;">

                                <label class="form-label">

                                    Karyawan

                                </label>

                                <div class="position-relative">

                                    <input type="text" id="search_karyawan" class="form-control"
                                        placeholder="Cari kode, nama atau divisi..." autocomplete="off">

                                    <input type="hidden" name="karyawan_id" id="karyawan_id">
                                    <div id="resultKaryawan" class="list-group shadow bg-white"
                                        style="
                                                    position:absolute;
                                                    top:100%;
                                                    left:0;
                                                    right:0;
                                                    z-index:9999;
                                                    display:none;
                                                    max-height:250px;
                                                    overflow-y:auto;
                                                    border:1px solid #dee2e6;
                                                    border-radius:.375rem;
                                                    margin-top:2px;
                                                ">
                                    </div>

                                </div>

                            </div>

                            {{-- Antar Perusahaan --}}
                            <div id="antar-section" style="display:none;">

                                <div class="row">

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">

                                            Perusahaan Tujuan

                                        </label>


                                        <select name="perusahaan_tujuan_id" id="perusahaan_tujuan_id" class="form-select">

                                            <option value="">
                                                -- Pilih --
                                            </option>

                                            @foreach ($perusahaans as $perusahaan)
                                                <option value="{{ $perusahaan->id }}">

                                                    {{ $perusahaan->nama_perusahaan }}

                                                </option>
                                            @endforeach

                                        </select>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">

                                            Penanggung Jawab

                                        </label>

                                        <div class="position-relative">

                                            <input type="text" id="search_karyawan_tujuan" class="form-control"
                                                placeholder="Cari kode, nama atau divisi..." autocomplete="off">

                                            <input type="hidden" name="karyawan_tujuan_id" id="karyawan_tujuan_id">

                                            <div id="resultKaryawanTujuan" class="list-group shadow bg-white"
                                                style="
                position:absolute;
                top:100%;
                left:0;
                right:0;
                z-index:9999;
                display:none;
                max-height:250px;
                overflow-y:auto;
                border:1px solid #dee2e6;
                border-radius:.375rem;
                margin-top:2px;
            ">
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            {{-- Keperluan --}}
                            <div class="col-md-12">

                                <label class="form-label">

                                    Keperluan

                                </label>

                                <textarea name="keperluan" rows="4" class="form-control"></textarea>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            {{-- =======================
            INFORMASI INVENTARIS
        ======================== --}}

            <div class="col-lg-5">

                <div class="card">

                    <div class="card-header">

                        <h5 class="mb-0">

                            Informasi Inventaris

                        </h5>

                    </div>

                    <div class="card-body">

                        <table class="table table-borderless">

                            <tr>
                                <th width="40%">Kode Aset</th>
                                <td id="info_kode_aset">-</td>
                            </tr>

                            <tr>
                                <th>No. Inventaris</th>
                                <td id="info_no_inventaris">-</td>
                            </tr>

                            <tr>
                                <th>Nama Barang</th>
                                <td id="info_barang">-</td>
                            </tr>

                            <tr>
                                <th>Data Aset</th>
                                <td id="info_data_aset">-</td>
                            </tr>

                            <tr>
                                <th>Perusahaan</th>
                                <td id="info_perusahaan">-</td>
                            </tr>

                            <tr>
                                <th>Status</th>
                                <td id="info_status">-</td>
                            </tr>

                        </table>

                    </div>

                </div>

            </div>

        </div>

        <div class="text-end">

            <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">

                Kembali

            </a>

            <button class="btn btn-primary">

                Simpan

            </button>

        </div>

    </form>

@endsection
@section('page-script')

    <script>
        $(function() {

            $('#jenis_peminjaman').change(function() {

                let jenis = $(this).val();

                if (jenis == 'internal') {

                    $('#internal-section').show();

                    $('#antar-section').hide();

                } else if (jenis == 'antar_perusahaan') {

                    $('#internal-section').hide();

                    $('#antar-section').show();

                } else {

                    $('#internal-section').hide();

                    $('#antar-section').hide();

                }

            });

        });

        $('#inventaris_id').change(function() {

            let option = $(this).find(':selected');

            $('#info_kode_aset').text(option.data('kode-aset'));
            $('#info_no_inventaris').text(option.data('no-inventaris'));
            $('#info_barang').text(option.data('nama-barang'));
            $('#info_data_aset').text(option.data('data-aset'));
            $('#info_perusahaan').text(option.data('perusahaan'));
            $('#info_status').text(option.data('status'));


        });
        $('#search_karyawan').on('keyup', function() {

            let keyword = $(this).val();

            if (keyword.length < 2) {

                $('#resultKaryawan')
                    .hide()
                    .empty();

                return;
            }

            $.ajax({

                url: "{{ route('peminjaman.search') }}",

                type: "GET",

                data: {
                    keyword: keyword
                },

                success: function(response) {

                    let html = '';

                    if (response.length == 0) {

                        html = `
                    <div class="list-group-item text-center text-muted">
                        Data tidak ditemukan
                    </div>
                `;

                    } else {

                        response.forEach(function(item) {

                            html += `
                        <a href="#"
                            class="list-group-item list-group-item-action pilih-karyawan py-2"
                            data-id="${item.id}"
                            data-nama="${item.nama_karyawan}">

                            <div class="d-flex justify-content-between align-items-center">

                                <div>

                                    <div class="fw-semibold">
                                        ${item.nama_karyawan}
                                    </div>

                                    <small class="text-muted">
                                        ${item.divisi ?? '-'}
                                    </small>

                                </div>

                                <span class="badge bg-label-primary">
                                    ${item.kode_karyawan}
                                </span>

                            </div>

                        </a>
                        `;
                        });

                    }

                    $('#resultKaryawan')
                        .html(html)
                        .show();

                }

            });

        });

        $(document).click(function(e) {

            if (!$(e.target).closest('#search_karyawan').length) {

                $('#resultKaryawan').hide();

            }

        });

        $(document).on('click', '.pilih-karyawan', function(e) {

            e.preventDefault();

            $('#karyawan_id').val($(this).data('id'));

            $('#search_karyawan').val($(this).data('nama'));

            $('#resultKaryawan').hide();

        });
        $('#kategori_id').change(function() {

            let kategori = $(this).val();

            $('#inventaris_id').html(
                '<option>Memuat inventaris...</option>'
            );

            if (kategori == '') {

                $('#inventaris_id').html(
                    '<option>Pilih Jenis Aset</option>'
                );

                return;
            }

            $.get(
                '/dashboard/peminjaman/inventaris-by-kategori/' + kategori,

                function(data) {

                    let option =
                        '<option value="">-- Pilih Inventaris --</option>';

                    $.each(data, function(i, item) {

                        option +=

                            `<option

                    value="${item.id}"

                    data-kode-aset="${item.kode_aset}"

                    data-no-inventaris="${item.no_inventaris}"

                    data-nama-barang="${item.data_aset.nama_barang}"

                    data-data-aset="${item.data_aset.merek} • ${item.data_aset.type} • ${item.data_aset.warna}"

                    data-perusahaan="${item.perusahaan.nama_perusahaan}"

                    data-status="${item.status}"

                >

                    ${item.kode_aset}
                    -
                    ${item.data_aset.nama_barang}
                    ${item.data_aset.merek}
                    ${item.data_aset.type}

                </option>`;

                    });

                    $('#inventaris_id').html(option);

                }

            );

        });
        let daftarKaryawan = [];

        $('#perusahaan_tujuan_id').change(function() {

            let perusahaanId = $(this).val();

            $('#search_karyawan_tujuan').val('');
            $('#karyawan_tujuan_id').val('');

            daftarKaryawan = [];

            if (!perusahaanId) return;

            $.get(
                '/dashboard/peminjaman/karyawan-perusahaan/' + perusahaanId,
                function(response) {

                    daftarKaryawan = response;

                }
            );

        });
        $('#search_karyawan_tujuan').on('keyup', function() {

            let keyword = $(this).val().toLowerCase();

            if (keyword.length < 2) {

                $('#resultKaryawanTujuan')
                    .hide()
                    .empty();

                return;

            }

            let hasil = daftarKaryawan.filter(function(item) {

                return (

                    item.kode_karyawan.toLowerCase().includes(keyword)

                    ||

                    item.nama_karyawan.toLowerCase().includes(keyword)

                    ||

                    (item.divisi ?? '').toLowerCase().includes(keyword)

                );

            });

            let html = '';

            if (hasil.length == 0) {

                html = `
            <div class="list-group-item text-center text-muted">
                Data tidak ditemukan
            </div>
        `;

            } else {

                hasil.forEach(function(item) {

                    html += `

            <a href="#"

                class="list-group-item list-group-item-action pilih-karyawan-tujuan"

                data-id="${item.id}"

                data-nama="${item.nama_karyawan}">

                <div class="d-flex justify-content-between">

                    <div>

                        <strong>

                            ${item.kode_karyawan}

                            -

                            ${item.nama_karyawan}

                        </strong>

                        <br>

                        <small>

                            ${item.divisi ?? '-'}

                        </small>

                    </div>

                </div>

            </a>

            `;

                });

            }

            $('#resultKaryawanTujuan')
                .html(html)
                .show();

        });
        $(document).on('click', '.pilih-karyawan-tujuan', function(e) {

            e.preventDefault();

            $('#karyawan_tujuan_id').val($(this).data('id'));

            $('#search_karyawan_tujuan').val($(this).data('nama'));

            $('#resultKaryawanTujuan').hide();

        });
        $(document).click(function(e) {

            if (

                !$(e.target).closest('#search_karyawan_tujuan').length

                &&

                !$(e.target).closest('#resultKaryawanTujuan').length

            ) {

                $('#resultKaryawanTujuan').hide();

            }

        });
    </script>

@endsection
