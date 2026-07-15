@extends('layouts/contentNavbarLayout')

@section('title', 'Tambah Service & Maintenance')

@section('content')

    <form action="{{ route('maintenance.store') }}" method="POST">

        @csrf
        {{-- Mapping --}}
        @if (isset($maping))
            <input type="hidden" name="asal" value="Mapping">

            <input type="hidden" name="maping_id" value="{{ $maping->id }}">
        @endif

        {{-- Peminjaman --}}
        @if (isset($peminjaman))
            <input type="hidden" name="asal" value="Peminjaman">

            <input type="hidden" name="peminjaman_id" value="{{ $peminjaman->id }}">
        @endif

        {{-- Manual --}}
        @if (!isset($maping) && !isset($peminjaman))
            <input type="hidden" name="asal" value="Manual">
        @endif
        <div class="row">

            {{-- =======================================================
            FORM SERVICE
        ======================================================== --}}
            <div class="col-lg-7">

                <div class="card mb-4">

                    <div class="card-header">

                        <h4 class="mb-1">
                            Transaksi Service & Maintenance
                        </h4>

                        <small class="text-muted">
                            Lengkapi data service & maintenance inventaris.
                        </small>

                    </div>

                    <div class="card-body">

                        <div class="row">
                            {{-- Perusahaan --}}
                            @if (auth()->user()->role == 'super_admin')

                                <div class="col-md-12 mb-3">

                                    <label class="form-label">
                                        Perusahaan
                                        <span class="text-danger">*</span>
                                    </label>

                                    @if (isset($inventaris) && $inventaris)

                                        {{-- Dari Mapping / Peminjaman --}}
                                        <input type="hidden" name="perusahaan_id" value="{{ $inventaris->perusahaan_id }}">

                                        <input type="text" class="form-control"
                                            value="{{ $inventaris->perusahaan->nama_perusahaan }}" readonly>
                                    @else
                                        {{-- Manual --}}
                                        <select name="perusahaan_id" id="perusahaan_id" class="form-select">

                                            <option value="">
                                                -- Pilih Perusahaan --
                                            </option>

                                            @foreach ($perusahaans as $perusahaan)
                                                <option value="{{ $perusahaan->id }}">
                                                    {{ $perusahaan->nama_perusahaan }}
                                                </option>
                                            @endforeach

                                        </select>

                                    @endif

                                </div>

                            @endif
                            @if (!isset($inventaris))
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Jenis Aset
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select id="kategori_id" class="form-select">

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
                            @endif

                            {{-- Inventaris --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Inventaris

                                    <span class="text-danger">*</span>

                                </label>

                                @if (isset($inventaris) && $inventaris)

                                    {{-- Dari Mapping / Peminjaman --}}

                                    <input type="hidden" name="inventaris_id" value="{{ $inventaris->id }}">

                                    <input type="text" class="form-control"
                                        value="{{ $inventaris->kode_aset }} - {{ $inventaris->dataAset->kategori->nama_barang }} | {{ $inventaris->dataAset->merek }} {{ $inventaris->dataAset->type }} | {{ $inventaris->dataAset->warna }}"
                                        readonly>
                                @else
                                    {{-- Manual dari menu Service --}}

                                    @if (auth()->user()->role == 'super_admin' && !isset($inventaris))

                                        <select name="inventaris_id" id="inventaris_id" class="form-select">

                                            <option value="">
                                                -- Pilih Perusahaan Terlebih Dahulu --
                                            </option>

                                        </select>
                                    @else
                                        <select name="inventaris_id" id="inventaris_id" class="form-select">

                                            <option value="">
                                                -- Pilih Jenis Aset Terlebih Dahulu --
                                            </option>

                                            @foreach ($inventarisList as $item)
                                                <option value="{{ $item->id }}"
                                                    data-kode-aset="{{ $item->kode_aset }}"
                                                    data-no-inventaris="{{ $item->no_inventaris }}"
                                                    data-barang="{{ $item->dataAset->kategori->nama_barang }}"
                                                    data-merek="{{ $item->dataAset->merek }}"
                                                    data-type="{{ $item->dataAset->type }}"
                                                    data-warna="{{ $item->dataAset->warna }}"
                                                    data-perusahaan="{{ $item->perusahaan->nama_perusahaan }}"
                                                    data-status="{{ $item->status }}">

                                                    {{ $item->kode_aset }}
                                                    -
                                                    {{ $item->dataAset->kategori->nama_barang }}
                                                    |
                                                    {{ $item->dataAset->merek }}
                                                    {{ $item->dataAset->type }}

                                                </option>
                                            @endforeach

                                        </select>
                                    @endif
                                @endif
                            </div>

                            {{-- Tanggal --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Tanggal

                                    <span class="text-danger">*</span>

                                </label>

                                <input type="date" class="form-control" name="tanggal"
                                    value="{{ old('tanggal', date('Y-m-d')) }}">

                            </div>

                            {{-- Jenis --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Jenis

                                    <span class="text-danger">*</span>

                                </label>

                                <select name="jenis" class="form-select">

                                    <option value="">
                                        -- Pilih --
                                    </option>

                                    <option value="Service">
                                        Service
                                    </option>

                                    <option value="Maintenance">
                                        Maintenance
                                    </option>

                                </select>

                            </div>

                            {{-- Kategori --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Kategori

                                </label>

                                <select name="kategori" class="form-select">

                                    <option value="">
                                        -- Pilih --
                                    </option>

                                    <option value="Hardware">
                                        Hardware
                                    </option>

                                    <option value="Software">
                                        Software
                                    </option>

                                    <option value="Cleaning">
                                        Cleaning
                                    </option>

                                    <option value="Jaringan">
                                        Jaringan
                                    </option>

                                    <option value="Upgrade">
                                        Upgrade
                                    </option>

                                    <option value="Lainnya">
                                        Lainnya
                                    </option>

                                </select>

                            </div>

                            {{-- Vendor --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Vendor / Teknisi

                                </label>

                                <input type="text" class="form-control" name="vendor" value="{{ old('vendor') }}">

                            </div>

                            {{-- Biaya --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Biaya

                                </label>

                                <input type="number" class="form-control" id="biaya" name="biaya"
                                    value="{{ old('biaya', 0) }}">

                            </div>

                            {{-- Keluhan --}}
                            <div class="col-md-12 mb-3">

                                <label class="form-label">

                                    Keluhan

                                </label>

                                <textarea name="keluhan" rows="3" class="form-control">{{ old('keluhan') }}</textarea>

                            </div>

                            {{-- Diagnosa --}}
                            <div class="col-md-12 mb-3">

                                <label class="form-label">

                                    Diagnosa

                                </label>

                                <textarea name="diagnosa" rows="3" class="form-control">{{ old('diagnosa') }}</textarea>

                            </div>

                            {{-- Tindakan --}}
                            <div class="col-md-12 mb-3">

                                <label class="form-label">

                                    Tindakan

                                </label>

                                <textarea name="tindakan" rows="3" class="form-control">{{ old('tindakan') }}</textarea>

                            </div>

                            {{-- Catatan --}}
                            <div class="col-md-12 mb-3">

                                <label class="form-label">

                                    Catatan

                                </label>

                                <textarea name="catatan" rows="3" class="form-control">{{ old('catatan') }}</textarea>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            {{-- ======================================================
            INFORMASI INVENTARIS
        ======================================================= --}}
            <div class="col-lg-5">

                <div class="card">

                    <div class="card-header">

                        <h4 class="mb-0">

                            Informasi Inventaris

                        </h4>

                    </div>

                    <div class="card-body">

                        <table class="table table-borderless table-sm">

                            <tr>

                                <th width="45%">
                                    Kode Aset
                                </th>

                                <td id="info_kode_aset">

                                    @if (isset($inventaris))
                                        {{ $inventaris->kode_aset }}
                                    @else
                                        -
                                    @endif

                                </td>

                            </tr>

                            <tr>

                                <th>
                                    No. Inventaris
                                </th>

                                <td id="info_no_inventaris">

                                    @if (isset($inventaris))
                                        {{ $inventaris->no_inventaris }}
                                    @else
                                        -
                                    @endif

                                </td>

                            </tr>

                            <tr>

                                <th>
                                    Nama Barang
                                </th>

                                <td id="info_barang">

                                    @if (isset($inventaris))
                                        {{ $inventaris->dataAset->kategori->nama_barang }}
                                    @else
                                        -
                                    @endif

                                </td>

                            </tr>

                            <tr>

                                <th>
                                    Merek
                                </th>

                                <td id="info_merek">

                                    @if (isset($inventaris))
                                        {{ $inventaris->dataAset->merek }}
                                    @else
                                        -
                                    @endif

                                </td>

                            </tr>

                            <tr>

                                <th>
                                    Type
                                </th>

                                <td id="info_type">

                                    @if (isset($inventaris))
                                        {{ $inventaris->dataAset->type }}
                                    @else
                                        -
                                    @endif

                                </td>

                            </tr>

                            <tr>

                                <th>
                                    Warna
                                </th>

                                <td id="info_warna">

                                    @if (isset($inventaris))
                                        {{ $inventaris->dataAset->warna }}
                                    @else
                                        -
                                    @endif

                                </td>

                            </tr>

                            <tr>

                                <th>
                                    Perusahaan
                                </th>

                                <td id="info_perusahaan">

                                    @if (isset($inventaris))
                                        {{ $inventaris->perusahaan->nama_perusahaan }}
                                    @else
                                        -
                                    @endif

                                </td>

                            </tr>

                            <tr>

                                <th>
                                    Status
                                </th>

                                <td id="info_status">

                                    @if (isset($inventaris))
                                        @php
                                            $badge = match ($inventaris->status) {
                                                'TERSEDIA' => 'success',
                                                'DIPAKAI' => 'primary',
                                                'DIPINJAM' => 'warning',
                                                'RUSAK' => 'danger',
                                                'AFKIR' => 'dark',
                                                default => 'secondary',
                                            };
                                        @endphp

                                        <span class="badge bg-label-{{ $badge }}">

                                            {{ $inventaris->status }}

                                        </span>
                                    @else
                                        -
                                    @endif

                                </td>

                            </tr>

                        </table>

                    </div>

                </div>

            </div>

        </div>

        <div class="text-end mt-3">

            <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary">

                <i class="bx bx-arrow-back"></i>

                Kembali

            </a>

            <button type="submit" class="btn btn-primary">

                <i class="bx bx-save"></i>

                Simpan

            </button>

        </div>

    </form>

@endsection
@section('page-script')

    <script>
        $(document).ready(function() {
            $('#perusahaan_id').change(function() {

                $('#kategori_id').val('');

                $('#inventaris_id').html(
                    '<option value="">-- Pilih Jenis Aset Terlebih Dahulu --</option>'
                );

                $('#info_kode_aset').text('-');
                $('#info_no_inventaris').text('-');
                $('#info_barang').text('-');
                $('#info_merek').text('-');
                $('#info_type').text('-');
                $('#info_warna').text('-');
                $('#info_perusahaan').text('-');
                $('#info_status').html('-');

            });

         

          
        $('#kategori_id').change(function() {

            let kategori = $(this).val();

            let perusahaan = $('#perusahaan_id').length ?
                $('#perusahaan_id').val() :
                '';

            if (kategori == '') {

                $('#inventaris_id').html(
                    '<option value="">-- Pilih Jenis Aset Terlebih Dahulu --</option>'
                );

                return;

            }

            $('#inventaris_id').html(
                '<option>Memuat...</option>'
            );

            $.get(
               "/dashboard/maintenance/inventaris-by-kategori/" + kategori, {
                    perusahaan: perusahaan
                },
                function(data) {

                    let option =
                        '<option value="">-- Pilih Inventaris --</option>';

                    $.each(data, function(i, item) {

                        option += `
<option

value="${item.id}"

data-kode-aset="${item.kode_aset}"

data-no-inventaris="${item.no_inventaris}"

data-barang="${item.data_aset.kategori.nama_barang}"

data-merek="${item.data_aset.merek}"

data-type="${item.data_aset.type}"

data-warna="${item.data_aset.warna}"

data-perusahaan="${item.perusahaan.nama_perusahaan}"

data-status="${item.status}"

>

${item.kode_aset}

-

${item.data_aset.kategori.nama_barang}

|

${item.data_aset.merek}

${item.data_aset.type}

</option>`;
                    });

                    $('#inventaris_id').html(option);

                }

            );

        });

        //--------------------------------------------------
        // Pilih Inventaris (Manual)
        //--------------------------------------------------
        $('#inventaris_id').on('change', function() {

            let item = $(this).find(':selected');

            $('#info_kode_aset').text(item.data('kode-aset') ?? '-');

            $('#info_no_inventaris').text(item.data('no-inventaris') ?? '-');

            $('#info_barang').text(item.data('barang') ?? '-');

            $('#info_merek').text(item.data('merek') ?? '-');

            $('#info_type').text(item.data('type') ?? '-');

            $('#info_warna').text(item.data('warna') ?? '-');

            $('#info_perusahaan').text(item.data('perusahaan') ?? '-');


            let status = item.data('status');

            let badge = '-';

            switch (status) {

                case 'TERSEDIA':
                    badge = '<span class="badge bg-label-success">TERSEDIA</span>';
                    break;

                case 'DIPAKAI':
                    badge = '<span class="badge bg-label-primary">DIPAKAI</span>';
                    break;

                case 'DIPINJAM':
                    badge = '<span class="badge bg-label-warning">DIPINJAM</span>';
                    break;

                case 'RUSAK':
                    badge = '<span class="badge bg-label-danger">RUSAK</span>';
                    break;
                case 'AFKIR':
                    badge = '<span class="badge bg-dark">AFKIR</span>';
                    break;

                default:
                    badge = '-';

            }

            $('#info_status').html(badge);

        });


        //--------------------------------------------------
        // Format Biaya
        //--------------------------------------------------
        $('#biaya').on('keyup', function() {

            let angka = this.value.replace(/\D/g, '');

            this.value = angka;

        });


        //--------------------------------------------------
        // Validasi Inventaris
        //--------------------------------------------------
        $('form').submit(function() {

        if ($('#inventaris_id').length) {

            if ($('#inventaris_id').val() == '') {

                alert('Silakan pilih inventaris terlebih dahulu.');

                $('#inventaris_id').focus();

                return false;

            }

        }

        });

        });
    </script>

@endsection
