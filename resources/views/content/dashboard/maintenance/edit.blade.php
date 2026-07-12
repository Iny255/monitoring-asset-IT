@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Service & Maintenance')

@section('content')

    <form action="{{ route('maintenance.update', is_object($maintenance) ? $maintenance->id : $maintenance) }}"
        method="POST">
        @csrf
        @method('PUT')
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
                            Edit Service & Maintenance
                        </h4>

                        <small class="text-muted">
                            Ubah data service & maintenance inventaris.
                        </small>
                    </div>

                    <div class="card-body">

                        <div class="row">

                            {{-- Inventaris --}}
                            <div class="col-md-12 mb-3">

                                <label class="form-label">
                                    Inventaris
                                    <span class="text-danger">*</span>
                                </label>

                                {{-- Saat Edit inventaris tidak boleh diganti --}}
                                <input type="hidden" name="inventaris_id" value="{{ $maintenance->inventaris->id }}">

                                <input type="text" class="form-control"
                                    value="{{ $maintenance->inventaris->kode_aset }} -{{ $maintenance->inventaris->dataAset->nama_barang }} |{{ $maintenance->inventaris->dataAset->merek }}{{ $maintenance->inventaris->dataAset->type }} |{{ $maintenance->inventaris->dataAset->warna }}"
                                    readonly>

                            </div>

                            {{-- Tanggal --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Tanggal
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="date" class="form-control" name="tanggal"
                                    value="{{ old('tanggal', $maintenance->tanggal->format('Y-m-d')) }}">

                            </div>

                            {{-- Jenis --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Jenis
                                    <span class="text-danger">*</span>
                                </label>

                                <select name="jenis" class="form-select">

                                    <option value="">-- Pilih --</option>

                                    <option value="Service"
                                        {{ old('jenis', $maintenance->jenis) == 'Service' ? 'selected' : '' }}>
                                        Service
                                    </option>

                                    <option value="Maintenance"
                                        {{ old('jenis', $maintenance->jenis) == 'Maintenance' ? 'selected' : '' }}>
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

                                    <option value="">-- Pilih --</option>

                                    <option value="Hardware"
                                        {{ old('kategori', $maintenance->kategori) == 'Hardware' ? 'selected' : '' }}>
                                        Hardware
                                    </option>

                                    <option value="Software"
                                        {{ old('kategori', $maintenance->kategori) == 'Software' ? 'selected' : '' }}>
                                        Software
                                    </option>

                                    <option value="Cleaning"
                                        {{ old('kategori', $maintenance->kategori) == 'Cleaning' ? 'selected' : '' }}>
                                        Cleaning
                                    </option>

                                    <option value="Jaringan"
                                        {{ old('kategori', $maintenance->kategori) == 'Jaringan' ? 'selected' : '' }}>
                                        Jaringan
                                    </option>

                                    <option value="Upgrade"
                                        {{ old('kategori', $maintenance->kategori) == 'Upgrade' ? 'selected' : '' }}>
                                        Upgrade
                                    </option>

                                    <option value="Lainnya"
                                        {{ old('kategori', $maintenance->kategori) == 'Lainnya' ? 'selected' : '' }}>
                                        Lainnya
                                    </option>

                                </select>

                            </div>

                            {{-- Vendor --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Vendor / Teknisi
                                </label>

                                <input type="text" class="form-control" name="vendor"
                                    value="{{ old('vendor', $maintenance->vendor) }}">

                            </div>

                            {{-- Biaya --}}
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Biaya
                                </label>

                                <input type="number" class="form-control" id="biaya" name="biaya"
                                    value="{{ old('biaya', $maintenance->biaya) }}">

                            </div>

                            {{-- Keluhan --}}
                            <div class="col-md-12 mb-3">

                                <label class="form-label">
                                    Keluhan
                                </label>

                                <textarea name="keluhan" rows="3" class="form-control">{{ old('keluhan', $maintenance->keluhan) }}</textarea>

                            </div>

                            {{-- Diagnosa --}}
                            <div class="col-md-12 mb-3">

                                <label class="form-label">
                                    Diagnosa
                                </label>

                                <textarea name="diagnosa" rows="3" class="form-control">{{ old('diagnosa', $maintenance->diagnosa) }}</textarea>

                            </div>

                            {{-- Tindakan --}}
                            <div class="col-md-12 mb-3">

                                <label class="form-label">
                                    Tindakan
                                </label>

                                <textarea name="tindakan" rows="3" class="form-control">{{ old('tindakan', $maintenance->tindakan) }}</textarea>

                            </div>

                            {{-- Catatan --}}
                            <div class="col-md-12 mb-3">

                                <label class="form-label">
                                    Catatan
                                </label>

                                <textarea name="catatan" rows="3" class="form-control">{{ old('catatan', $maintenance->catatan) }}</textarea>

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
