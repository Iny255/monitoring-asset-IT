@extends('layouts/contentNavbarLayout')

@section('title', 'Mutasi Data Maping')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/mutasi.css') }}">

    <div class="container-fluid">

        <div class="card shadow-sm border-0">

            {{-- HEADER --}}
            <div class="card-header text-white py-3" style="background: linear-gradient(90deg,#0d3b66,#7b8dff);">

                <h4 class="mb-0 text-white fw-bold">
                    Form Mutasi Barang
                </h4>

            </div>

            <div class="card-body">

                <form action="{{ route('maping.mutasi.store', $maping->id) }}" method="POST">

                    @csrf

                    <div class="row">

                        {{-- ===================================== --}}
                        {{-- PERUSAHAAN --}}
                        {{-- ===================================== --}}
                        <div class="col-md-6 mb-3">

                            <label class="form-label fw-semibold">
                                Perusahaan Baru
                            </label>

                            @if ($modePerusahaan === 'select')

                                <select name="ke_perusahaan" id="ke_perusahaan" class="form-select" required>

                                    <option value="">
                                        -- Pilih Perusahaan --
                                    </option>

                                    @foreach ($perusahaan as $p)
                                        <option value="{{ $p->id }}">

                                            {{ $p->nama_perusahaan }}

                                        </option>
                                    @endforeach

                                </select>
                            @else
                                <input type="hidden" name="ke_perusahaan" id="ke_perusahaan" value="{{ $perusahaan->id }}">

                                <div class="form-control bg-light">

                                    {{ $perusahaan->nama_perusahaan }}

                                </div>

                            @endif

                        </div>

                        {{-- ===================================== --}}
                        {{-- LOKASI --}}
                        {{-- ===================================== --}}
                        <div class="col-md-6 mb-3">

                            <label class="form-label fw-semibold">
                                Lokasi Baru
                            </label>

                            <select name="ke_lokasi" id="ke_lokasi" class="form-select" required>

                                <option value="">
                                    -- Pilih Lokasi --
                                </option>

                                @if ($modePerusahaan !== 'select')

                                    @foreach ($lokasi as $l)
                                        <option value="{{ $l->id }}">

                                            {{ $l->nama_lokasi }}

                                        </option>
                                    @endforeach

                                @endif

                            </select>

                        </div>

                        {{-- ===================================== --}}
                        {{-- KARYAWAN --}}
                        {{-- ===================================== --}}
                        <div class="col-md-6 mb-3 position-relative">

                            <label class="form-label fw-semibold">
                                Karyawan Baru
                            </label>

                            <input type="text" id="search_karyawan" class="form-control" autocomplete="off"
                                placeholder="Ketik nama karyawan...">

                            <input type="hidden" name="ke_karyawan" id="ke_karyawan">

                            {{-- RESULT --}}
                            <div id="result_karyawan" class="autocomplete-box shadow-sm">

                            </div>

                        </div>

                        {{-- ===================================== --}}
                        {{-- NO INVENTARIS --}}
                        {{-- ===================================== --}}
                        <div class="col-md-6 mb-3">

                            <label class="form-label fw-semibold">
                                No Inventaris Baru
                            </label>

                            <input type="text" name="ke_no_inventaris" class="form-control"
                                placeholder="Masukkan No Inventaris">

                        </div>

                        {{-- ===================================== --}}
                        {{-- APLIKASI --}}
                        {{-- ===================================== --}}
                        <div class="col-md-6 mb-3">

                            <label class="form-label fw-semibold">
                                Aplikasi
                            </label>

                            <input type="text" name="ke_aplikasi" class="form-control"
                                placeholder="Contoh: Office, Accurate, dll">

                        </div>

                        {{-- ===================================== --}}
                        {{-- DATA PPN --}}
                        {{-- ===================================== --}}
                        <div class="col-md-3 mb-3">

                            <label class="form-label fw-semibold">
                                Data PPN
                            </label>

                            <input type="text" name="ke_data_ppn" class="form-control">

                        </div>

                        {{-- ===================================== --}}
                        {{-- DATA NON PPN --}}
                        {{-- ===================================== --}}
                        <div class="col-md-3 mb-3">

                            <label class="form-label fw-semibold">
                                Data Non PPN
                            </label>

                            <input type="text" name="ke_data_non_ppn" class="form-control">

                        </div>

                        {{-- ===================================== --}}
                        {{-- TANGGAL --}}
                        {{-- ===================================== --}}
                        <div class="col-md-6 mb-3">

                            <label class="form-label fw-semibold">
                                Tanggal Mutasi
                            </label>

                            <input type="date" name="tanggal_mutasi" class="form-control" required>

                        </div>

                    </div>

                    {{-- BUTTON --}}
                    <div class="text-end mt-3">

                        <a href="{{ route('maping.index') }}" class="btn btn-secondary">

                            Kembali

                        </a>

                        <button type="submit" class="btn btn-primary">

                            Simpan Mutasi

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

    {{-- ===================================== --}}
    {{-- STYLE AUTOCOMPLETE --}}
    {{-- ===================================== --}}
    <style>
        .autocomplete-box {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 10px 10px;
            max-height: 250px;
            overflow-y: auto;
            z-index: 9999;
            display: none;
        }

        .autocomplete-item {
            padding: 10px 14px;
            cursor: pointer;
            transition: .2s;
            border-bottom: 1px solid #f1f1f1;
        }

        .autocomplete-item:hover {
            background: #f5f7ff;
        }
    </style>

    {{-- ===================================== --}}
    {{-- SCRIPT --}}
    {{-- ===================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // =====================================
            // ELEMENT
            // =====================================

            const perusahaanField =
                document.getElementById('ke_perusahaan');

            const lokasiSelect =
                document.getElementById('ke_lokasi');

            const inputKaryawan =
                document.getElementById('search_karyawan');

            const hiddenId =
                document.getElementById('ke_karyawan');

            const resultBox =
                document.getElementById('result_karyawan');

            // =====================================
            // LOAD LOKASI
            // =====================================

            if (
                perusahaanField &&
                perusahaanField.tagName === 'SELECT'
            ) {

                perusahaanField.addEventListener('change', function() {

                    let perusahaanId = this.value;

                    // reset lokasi
                    lokasiSelect.innerHTML =
                        '<option value="">-- Pilih Lokasi --</option>';

                    // reset karyawan
                    inputKaryawan.value = '';
                    hiddenId.value = '';

                    resultBox.innerHTML = '';
                    resultBox.style.display = 'none';

                    if (!perusahaanId) return;

                    fetch(`/maping/lokasi-by-perusahaan/${perusahaanId}`)

                        .then(res => res.json())

                        .then(data => {

                            data.forEach(lokasi => {

                                lokasiSelect.innerHTML += `
                            <option value="${lokasi.id}">
                                ${lokasi.nama_lokasi}
                            </option>
                        `;

                            });

                        })

                        .catch(err => {

                            console.log(err);

                        });

                });

            }

            // =====================================
            // AUTOCOMPLETE
            // =====================================

            inputKaryawan.addEventListener('input', function() {

                let keyword =
                    this.value.trim();

                let perusahaanId =
                    perusahaanField ?
                    perusahaanField.value :
                    '{{ auth()->user()->id_perusahaan ?? '' }}';

                // minimal 2 karakter
                if (keyword.length < 2) {

                    resultBox.style.display = 'none';
                    resultBox.innerHTML = '';

                    return;

                }

                // wajib pilih perusahaan
                if (!perusahaanId) {

                    Swal.fire(
                        'Peringatan',
                        'Pilih perusahaan terlebih dahulu',
                        'warning'
                    );

                    return;

                }

                fetch(
                        `{{ route('karyawan.search') }}?q=${encodeURIComponent(keyword)}&perusahaan_id=${perusahaanId}`
                    )

                    .then(res => res.json())

                    .then(data => {

                        resultBox.innerHTML = '';

                        if (!data || data.length === 0) {

                            resultBox.style.display = 'none';

                            return;

                        }

                        data.forEach(k => {

                            const item =
                                document.createElement('div');

                            item.classList.add('autocomplete-item');

                            item.innerHTML =
                                k.nama_karyawan;

                            // klik sekali langsung pilih
                            item.addEventListener('mousedown', function(e) {

                                e.preventDefault();

                                inputKaryawan.value =
                                    k.nama_karyawan;

                                hiddenId.value =
                                    k.id;

                                resultBox.innerHTML = '';
                                resultBox.style.display = 'none';

                            });

                            resultBox.appendChild(item);

                        });

                        resultBox.style.display = 'block';

                    })

                    .catch(err => {

                        console.log(err);

                    });

            });

            // =====================================
            // KLIK LUAR
            // =====================================

            document.addEventListener('click', function(e) {

                if (
                    !resultBox.contains(e.target) &&
                    e.target !== inputKaryawan
                ) {

                    resultBox.style.display = 'none';

                }

            });

        });
    </script>

@endsection
