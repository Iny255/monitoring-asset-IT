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
                                    <option value="{{ $l->id }}">{{ $l->nama_lokasi }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- PERUSAHAAN --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Perusahaan Baru</label>
                            <select name="ke_perusahaan" class="form-control" required>
                                <option value="">-- Pilih Perusahaan --</option>
                                @foreach ($perusahaan as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                @endforeach
                            </select>
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

                        {{-- TANGGAL --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Mutasi</label>
                            <input type="date" name="tanggal_mutasi" class="form-control" required>
                        </div>


                    </div>

                    <div class="text-end">
                        <a href="{{ route('maping.index') }}" class="btn btn-secondary">
                            Kembali
                        </a>
                        <button class="btn btn-primary">
                            Simpan Mutasi
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const input = document.getElementById("search_karyawan");
            const resultBox = document.getElementById("result_karyawan");
            const hiddenId = document.getElementById("ke_karyawan");

            input.addEventListener("keyup", function() {
                let keyword = this.value;

                if (keyword.length < 2) {
                    resultBox.style.display = "none";
                    return;
                }

                fetch("{{ route('karyawan.search') }}?q=" + keyword)
                    .then(res => res.json())
                    .then(data => {

                        console.log(data); // DEBUG

                        resultBox.innerHTML = "";

                        if (data.length === 0) {
                            resultBox.style.display = "none";
                            return;
                        }

                        let html = `
                    <table class="autocomplete-table">
                        <thead>
                            <tr>
                                <th>Nama Karyawan</th>
                            </tr>
                        </thead>
                        <tbody class="autocomplete-body">
                `;

                        data.forEach(k => {
                            html += `
                        <tr class="autocomplete-row" data-id="${k.id}" data-nama="${k.nama_karyawan}">
                            <td>${k.nama_karyawan}</td>
                        </tr>
                    `;
                        });

                        html += `</tbody></table>`;

                        resultBox.innerHTML = html;
                        resultBox.style.display = "block";

                        // EVENT CLICK
                        document.querySelectorAll('.autocomplete-row').forEach(row => {
                            row.addEventListener('click', function() {
                                input.value = this.dataset.nama;
                                hiddenId.value = this.dataset.id;
                                resultBox.style.display = "none";
                            });
                        });

                    })
                    .catch(err => {
                        console.error('ERROR:', err);
                    });
            });

            // Klik luar → close
            document.addEventListener("click", function(e) {
                if (!input.contains(e.target) && !resultBox.contains(e.target)) {
                    resultBox.style.display = "none";
                }
            });

        });
    </script>
@endsection
