@extends('layouts/contentNavbarLayout')

@section('title', 'create-pendaftaran')

@section('content')

    <style>
        .form-label {
            font-size: 14px;
            /* Ukuran huruf label form */
            font-weight: bold;
            /* Membuat huruf tebal */
        }

        .card-header h5 {
            font-size: 18px;
            /* Ukuran huruf judul form */
            font-weight: bold;
            /* Membuat huruf tebal */
        }
    </style>
    <style>
        fieldset legend {
            font-size: 18px;
            /* Atur ukuran teks legend sesuai kebutuhan */
            font-weight: bold;
            /* Opsi tambahan: membuat teks legend tebal */
            color: #5e6a80;
            /* Opsi tambahan: warna teks legend */
        }
    </style>

    <a href="/dashboard/pendaftaran" class="btn btn-secondary mb-3">Kembali</a>

    <div class="row">
        <div class="col-lg-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="post" action="{{ route('pendaftaran.store') }}">
                @csrf
                <div class="row">
                    <!-- Kolom Kiri -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Masukkan Data Pendaftaran Posyandu</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="nama_posyandu" class="form-label">Nama Posyandu</label>
                                    <select class="form-control" id="nama_posyandu" name="nama_posyandu">
                                        <option value="">Pilih Posyandu</option>
                                        @foreach ($posyanduList as $posyandu)
                                            <option value="{{ $posyandu->nama_posyandu }}">
                                                {{ $posyandu->nama_posyandu }} - {{ $posyandu->dukuh }}

                                                RT{{ $posyandu->rt }}/RW{{ $posyandu->rw }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <fieldset>
                                        <legend>Data Balita </legend>
                                        <label class="form-label" for="nik">NIK</label>
                                        <input type="text" id="nik" name="nik" class="form-control"
                                            placeholder="Masukkan NIK 16 digit" value="{{ old('nik') }}">
                                    </fieldset>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="nama_balita">Nama</label>
                                    <input type="text" id="nama_balita" name="nama_balita" class="form-control"
                                        placeholder="Masukkan Nama Balita" value="{{ old('nama_balita') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="tempat_lahir">Tempat Lahir</label>
                                    <input type="text" id="tempat_lahir" name="tempat_lahir" class="form-control"
                                        placeholder="Masukkan Tempat Lahir" value="{{ old('tempat_lahir') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="tanggal_lahir">Tanggal Lahir</label>
                                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-control"
                                        placeholder="Masukkan Tanggal Lahir" value="{{ old('tanggal_lahir') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="form-control">
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="laki-laki">Laki - Laki</option>
                                        <option value="perempuan">Perempuan</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="bb_lahir">Berat Badan Lahir</label>
                                    <input type="float" id="bb_lahir" name="bb_lahir" class="form-control"
                                        placeholder="Masukkan Berat Badan Lahir" value="{{ old('bb_lahir') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="tb_lahir">Tinggi Badan Lahir</label>
                                    <input type="float" id="tb_lahir" name="tb_lahir" class="form-control"
                                        placeholder="Masukkan Tinggi Badan Lahir" value="{{ old('tb_lahir') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <!-- Form Data Balita -->
                                <div id="ortu-container">
                                    <div class="ortu-item">
                                        <fieldset>
                                            <legend>Data Orang Tua</legend>
                                            <div class="mb-3">
                                                <label class="form-label" for="nama_ortu">Nama Orang Tua</label>
                                                <input type="text" name="nama_ortu" class="form-control"
                                                    placeholder="Masukkan Nama Orang Tua" value="{{ old('nama_ortu') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="email_ortu">Email</label>
                                                <input type="email" id="email_ortu" name="email_ortu"
                                                    class="form-control" placeholder="Masukkan Email"
                                                    value="{{ old('email_ortu') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="no_telepon">No WhatsApp</label>
                                                <input type="text" id="no_telepon" name="no_telepon"
                                                    class="form-control" placeholder="Masukkan Nomor WA"
                                                    value="{{ old('tempat_lahir') }}">
                                            </div>
                                            <div class="mb-3">
                                                <fieldset>
                                                    <legend>Alamat Orang Tua</legend>
                                                    <label class="form-label" for="dukuh">Dukuh</label>
                                                    <select class="form-control" id="dukuh" name="dukuh">
                                                        <option value="">Pilih Nama Dukuh</option>
                                                        <option value="Cendono">Cendono</option>
                                                        <option value="Madu">Madu</option>
                                                        <option value="Kawakan">Kawakan</option>
                                                        <option value="Dawe">Dawe</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="rt">RT</label>
                                                <select class="form-control" id="rt" name="rt">
                                                    <option value="">Pilih RT</option>
                                                    <option value="01">01</option>
                                                    <option value="02">02</option>
                                                    <option value="03">03</option>
                                                    <option value="04">04</option>
                                                    <option value="05">05</option>
                                                    <option value="06">06</option>
                                                    <option value="07">07</option>
                                                    <option value="08">08</option>
                                                    <option value="09">09</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="rw">RW</label>
                                                <select class="form-control" id="rw" name="rw">
                                                    <option value="">Pilih RW</option>
                                                    <option value="01">01</option>
                                                    <option value="02">02</option>
                                                    <option value="03">03</option>
                                                    <option value="04">04</option>
                                                    <option value="05">05</option>
                                                    <option value="06">06</option>
                                                    <option value="07">07</option>
                                                    <option value="08">08</option>
                                                    <option value="09">09</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="pekerjaan">Pekerjaan</label>
                                                <select class="form-control" id="pekerjaan" name="pekerjaan"
                                                    onchange="checkOtherOption()">
                                                    <option value="">Pilih Pekerjaan</option>
                                                    <option value="Belum/Tidak Bekerja">Belum/Tidak Bekerja
                                                    </option>
                                                    <option value="Mengurus Rumah Tangga">Mengurus Rumah Tangga
                                                    </option>
                                                    <option value="Pensiunan">Pensiunan</option>
                                                    <option value="Pegawai Negeri Sipil (PNS)">Pegawai Negeri
                                                        Sipil (PNS)
                                                    </option>
                                                    <option value="Perdagangan">Perdagangan</option>
                                                    <option value="Petani/Pekebun">Petani/Pekebun</option>
                                                    <option value="Peternak">Peternak</option>
                                                    <option value="Industri">Industri</option>
                                                    <option value="Karyawan Swasta">Karyawan Swasta</option>
                                                    <option value="Buruh Harian Lepas">Buruh Harian Lepas
                                                    </option>
                                                    <option value="Perangkat Desa">Perangkat Desa</option>
                                                    <option value="Kepala Desa">Kepala Desa</option>
                                                    <option value="Wiraswasta">Wiraswasta</option>
                                                    <option value="Dosen">Dosen</option>
                                                    <option value="Guru">Guru</option>
                                                    <option value="Bidan">Bidan</option>
                                                    <option value="Lainnya">Lainnya</option>
                                                </select>
                                            </div>
                                            <div class="mb-3" id="pekerjaan_lainnya" style="display: none;">
                                                <label class="form-label" for="pekerjaan_lainnya_input">Pekerjaan
                                                    Lainnya</label>
                                                <input type="text" id="pekerjaan_lainnya_input"
                                                    name="pekerjaan_lainnya" class="form-control"
                                                    placeholder="Masukkan Pekerjaan">
                                            </div>
                                            <button type="submit" class="btn btn-primary">Kirim</button>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>



    <script>
        function checkOtherOption() {
            var selectElement = document.getElementById("pekerjaan");
            var otherInput = document.getElementById("pekerjaan_lainnya");

            if (selectElement.value === "Lainnya") {
                otherInput.style.display = "block";
            } else {
                otherInput.style.display = "none";
                otherInput.value = "";
            }
        }
    </script>

@endsection
