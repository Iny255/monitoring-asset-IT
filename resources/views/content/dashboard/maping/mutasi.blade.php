@extends('layouts/contentNavbarLayout')

@section('title', 'Mutasi Data Maping')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/mutasi.css') }}">

    <div class="container-fluid">

        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0" style="color:white;">Form Mutasi Barang</h5>
            </div>

            <div class="card-body">

                <form action="{{ route('maping.mutasi.store', $maping->id) }}" method="POST">
                    @csrf

                    <div class="row">

                        {{-- LOKASI --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lokasi Baru</label>
                            <select name="ke_lokasi" class="form-control" required>
                                <option value="">-- Pilih Lokasi --</option>
                                @foreach ($lokasi as $l)
                                    <option value="{{ $l->id }}">
                                        {{ $l->nama_lokasi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- PERUSAHAAN (SUDAH FILTER AMAN) --}}
                        {{-- PERUSAHAAN --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Perusahaan Baru</label>

                            @if ($modePerusahaan === 'select')
                                <select name="ke_perusahaan" class="form-control" required>
                                    <option value="">-- Pilih Perusahaan --</option>
                                    @foreach ($perusahaan as $p)
                                        <option value="{{ $p->id }}">
                                            {{ $p->nama_perusahaan }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="ke_perusahaan" value="{{ $perusahaan->id }}">

                                <div class="form-control bg-light">
                                    {{ $perusahaan->nama_perusahaan }}
                                </div>
                            @endif
                        </div>

                        {{-- KARYAWAN AUTOCOMPLETE --}}
                        <div class="col-md-6 mb-3 position-relative">
                            <label class="form-label">Karyawan Baru</label>

                            <input type="text" id="search_karyawan" class="form-control"
                                placeholder="Ketik nama karyawan...">

                            <input type="hidden" name="ke_karyawan" id="ke_karyawan">

                            <div id="result_karyawan" class="autocomplete-box" style="display:none;"></div>
                        </div>

                        {{-- NO INVENTARIS --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No Inventaris Baru</label>
                            <input type="text" name="ke_no_inventaris" class="form-control"
                                placeholder="Masukkan No Inventaris">
                        </div>

                        {{-- APLIKASI --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Aplikasi</label>
                            <input type="text" name="ke_aplikasi" class="form-control"
                                placeholder="Contoh: Office, Accurate, dll">
                        </div>

                        {{-- DATA PPN --}}
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Data PPN</label>
                            <input type="text" name="ke_data_ppn" class="form-control">
                        </div>

                        {{-- DATA NON PPN --}}
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Data Non PPN</label>
                            <input type="text" name="ke_data_non_ppn" class="form-control">
                        </div>

                        {{-- TANGGAL MUTASI --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Mutasi</label>
                            <input type="date" name="tanggal_mutasi" class="form-control" required>
                        </div>

                    </div>

                    <div class="text-end">
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

    {{-- AUTOCOMPLETE KARYAWAN --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const input = document.getElementById("search_karyawan");
            const resultBox = document.getElementById("result_karyawan");
            const hiddenId = document.getElementById("ke_karyawan");

            console.log("INPUT:", input);

            if (!input || !resultBox || !hiddenId) {
                console.log("ELEMENT TIDAK LENGKAP");
                return;
            }

            input.addEventListener("input", function() {

                let keyword = this.value;

                console.log("typing:", keyword);

                if (keyword.length < 2) {
                    resultBox.style.display = "none";
                    resultBox.innerHTML = "";
                    return;
                }

                fetch("{{ route('karyawan.search') }}?q=" + encodeURIComponent(keyword))
                    .then(res => res.json())
                    .then(data => {

                        console.log("RESULT:", data);

                        resultBox.innerHTML = "";

                        if (!data || data.length === 0) {
                            resultBox.style.display = "none";
                            return;
                        }

                        data.forEach(k => {

                            const div = document.createElement("div");

                            div.textContent = k.nama_karyawan;
                            div.style.padding = "8px";
                            div.style.cursor = "pointer";
                            div.style.borderBottom = "1px solid #eee";
                            div.style.background = "#fff";

                            div.addEventListener("mouseenter", () => {
                                div.style.background = "#f2f2f2";
                            });

                            div.addEventListener("mouseleave", () => {
                                div.style.background = "#fff";
                            });

                            div.addEventListener("click", () => {
                                input.value = k.nama_karyawan;
                                hiddenId.value = k.id;
                                resultBox.style.display = "none";
                                resultBox.innerHTML = "";
                            });

                            resultBox.appendChild(div);
                        });

                        resultBox.style.display = "block";
                    })
                    .catch(err => {
                        console.log("FETCH ERROR:", err);
                    });
            });

            // klik luar untuk tutup
            document.addEventListener("click", function(e) {
                if (!resultBox.contains(e.target) && e.target !== input) {
                    resultBox.style.display = "none";
                }
            });

        });
    </script>
@endsection
