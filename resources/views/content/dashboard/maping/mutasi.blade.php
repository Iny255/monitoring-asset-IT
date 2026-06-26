@extends('layouts/contentNavbarLayout')

@section('title', 'Mutasi Data Maping')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/mutasi.css') }}">

    <div class="container-fluid">

        <div class="card shadow-sm border-0">

            {{-- HEADER --}}
            <div class="card-header text-white py-3"
                style="
                    background: linear-gradient(
                    90deg,
                    var(--theme-primary),
                    var(--theme-secondary)
                    );
                    ">

                <h4 class="mb-0 text-white fw-bold">
                    Form Mutasi Barang
                </h4>

            </div>

            <div class="card-body">

                <form action="{{ route('maping.mutasi.store', $maping->id) }}" method="POST">
                    @csrf

                    <div class="row g-3">

                        {{-- PERUSAHAAN --}}
                        <div class="col-md-6">

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

                                <input type="text" class="form-control" value="{{ $perusahaan->nama_perusahaan }}"
                                    readonly>

                            @endif

                        </div>

                        {{-- LOKASI --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Lokasi Baru
                            </label>

                            <select name="ke_lokasi" id="ke_lokasi" class="form-select" required>

                                <option value="">
                                    -- Pilih Lokasi --
                                </option>

                                @foreach ($lokasi as $l)
                                    <option value="{{ $l->id }}">
                                        {{ $l->nama_lokasi }}
                                    </option>
                                @endforeach

                            </select>

                        </div>

                        {{-- KARYAWAN --}}
                        <div class="col-md-6 position-relative">

                            <label class="form-label fw-semibold">
                                Karyawan Baru
                            </label>

                            <input type="hidden" name="ke_karyawan" id="ke_karyawan">

                            <input type="text" id="nama_karyawan" class="form-control"
                                placeholder="Ketik kode atau nama karyawan">

                            <div id="autocomplete-list"></div>

                        </div>

                        {{-- TANGGAL --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Tanggal Mutasi
                            </label>

                            <input type="date" name="tanggal_mutasi" class="form-control" value="{{ date('Y-m-d') }}"
                                required>

                        </div>

                        {{-- APLIKASI --}}
                        <div class="col-md-12">

                            <label class="form-label fw-semibold">
                                Aplikasi
                            </label>

                            <textarea name="ke_aplikasi" rows="3" class="form-control"
                                placeholder="Contoh : Microsoft Office, Accurate, Coretax, WhatsApp"></textarea>

                        </div>

                        {{-- DATA PPN --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Hak Akses Data PPN
                            </label>

                            <textarea name="ke_data_ppn" rows="4" class="form-control"></textarea>

                        </div>

                        {{-- DATA NON PPN --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Hak Akses Data Non PPN
                            </label>

                            <textarea name="ke_data_non_ppn" rows="4" class="form-control"></textarea>

                        </div>

                        {{-- KETERANGAN --}}
                        <div class="col-md-12">

                            <label class="form-label fw-semibold">
                                Keterangan Mutasi
                            </label>

                            <textarea name="keterangan" rows="3" class="form-control"
                                placeholder="Alasan mutasi lokasi, user, atau perusahaan"></textarea>

                        </div>

                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">

                        <a href="{{ route('maping.index') }}" class="btn btn-secondary">

                            <i class="bx bx-arrow-back"></i>
                            Kembali

                        </a>

                        <button type="submit" class="btn btn-primary">

                            <i class="bx bx-transfer"></i>
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
        #autocomplete-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #dce1e7;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .15);
            z-index: 9999;
            max-height: 250px;
            overflow-y: auto;
        }

        .autocomplete-item {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }

        .autocomplete-item:last-child {
            border-bottom: none;
        }

        .autocomplete-item:hover {
            background: #f5f8ff;
        }

        ..autocomplete-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .autocomplete-kode {
            font-weight: 700;
            color: #0d6efd;
            min-width: 80px;
        }

        .autocomplete-nama {
            font-weight: 600;
        }

        .autocomplete-divisi {
            color: #6c757d;
        }
    </style>

    {{-- ===================================== --}}
    {{-- SCRIPT --}}
    {{-- ===================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const perusahaanField = document.getElementById('ke_perusahaan');
            const lokasiSelect = document.getElementById('ke_lokasi');

            const inputKaryawan = document.getElementById('nama_karyawan');
            const hiddenKaryawan = document.getElementById('ke_karyawan');
            const resultBox = document.getElementById('autocomplete-list');

            let debounceTimer;

            // =====================================
            // LOAD LOKASI BERDASARKAN PERUSAHAAN
            // =====================================

            if (
                perusahaanField &&
                perusahaanField.tagName === 'SELECT'
            ) {

                perusahaanField.addEventListener('change', function() {

                    const perusahaanId = this.value;

                    lokasiSelect.innerHTML =
                        '<option value="">-- Pilih Lokasi --</option>';

                    inputKaryawan.value = '';
                    hiddenKaryawan.value = '';

                    resultBox.innerHTML = '';

                    if (!perusahaanId) return;

                    fetch(`/maping/lokasi-by-perusahaan/${perusahaanId}`)

                        .then(response => response.json())

                        .then(data => {

                            data.forEach(item => {

                                lokasiSelect.innerHTML += `
                            <option value="${item.id}">
                                ${item.nama_lokasi}
                            </option>
                        `;

                            });

                        })

                        .catch(error => {
                            console.log(error);
                        });

                });

            }

            // =====================================
            // AUTOCOMPLETE KARYAWAN
            // =====================================

            inputKaryawan.addEventListener('keyup', function() {

                clearTimeout(debounceTimer);

                const keyword = this.value.trim();

                const perusahaanId =
                    perusahaanField ?
                    perusahaanField.value :
                    '{{ auth()->user()->id_perusahaan }}';

                if (keyword.length < 1) {

                    resultBox.innerHTML = '';
                    return;

                }

                if (!perusahaanId) {

                    resultBox.innerHTML = '';

                    alert('Pilih perusahaan terlebih dahulu');

                    return;
                }

                debounceTimer = setTimeout(() => {

                    fetch(
                            `{{ route('karyawan.search') }}?q=${encodeURIComponent(keyword)}&perusahaan_id=${perusahaanId}`
                        )

                        .then(response => response.json())

                        .then(data => {

                            resultBox.innerHTML = '';

                            if (!data.length) {

                                resultBox.innerHTML = `
                        <div class="autocomplete-item text-muted">
                            Data tidak ditemukan
                        </div>
                    `;

                                return;
                            }

                            data.forEach(item => {

                                resultBox.innerHTML += `
<div
    class="autocomplete-item"
    data-id="${item.id}"
    data-nama="${item.nama_karyawan}"
>

    <div class="autocomplete-row">

        <span class="autocomplete-kode">
            ${item.kode_karyawan ?? '-'}
        </span>

        <span class="autocomplete-nama">
            ${item.nama_karyawan}
        </span>

        <span class="autocomplete-divisi">
            (${item.divisi ?? '-'})
        </span>

    </div>

</div>
`;

                            });

                        })

                        .catch(error => {

                            console.log(error);

                        });

                }, 300);

            });

            // =====================================
            // PILIH KARYAWAN
            // =====================================

            document.addEventListener('click', function(e) {

                const item = e.target.closest('.autocomplete-item');

                if (item) {

                    hiddenKaryawan.value =
                        item.dataset.id;

                    inputKaryawan.value =
                        item.dataset.nama;

                    resultBox.innerHTML = '';
                }
            });

            // =====================================
            // KLIK DI LUAR
            // =====================================

            document.addEventListener('click', function(e) {

                if (
                    !inputKaryawan.contains(e.target) &&
                    !resultBox.contains(e.target)
                ) {

                    resultBox.innerHTML = '';

                }

            });

        });
    </script>

@endsection
