@extends('layouts/contentNavbarLayout')

@section('title', 'edit-pendaftaran')

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

            <form method="post" action="/dashboard/pendaftaran/update/{{ $pendaftarans->id }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
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
                                    <br>
                                    <small id="pilihLokasiTxt" class="text-danger fw-bold" hidden>*Pilih lokasi
                                        posyandu. Klik update untuk menyimpan perubahan</small>
                                    <select class="form-control" id="nama_posyandu" name="nama_posyandu" disabled>
                                        <option value="">Pilih Nama Posyandu</option>
                                        @foreach ($posyanduList as $posyandu)
                                            <option value="{{ $posyandu->nama_posyandu }}"
                                                {{ $pendaftarans->nama_posyandu == $posyandu->nama_posyandu ? 'selected' : '' }}>
                                                {{ $posyandu->nama_posyandu }} - {{ $posyandu->dukuh }}

                                                RT{{ $posyandu->rt }}/RW{{ $posyandu->rw }}

                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="text-end">
                                        <button type="button" id="pindahPosyanduBtn"
                                            class="mt-3 btn btn-sm btn-danger">Pindah
                                            Posyandu</button>
                                    </div>
                                </div>
                                <fieldset>
                                    <legend>Data Balita</legend>
                                    <div class="mb-3">
                                        <label class="form-label" for="nik">NIK</label>
                                        <input type="text" id="nik" name="nik" class="form-control"
                                            placeholder="Masukkan NIK 16 digit" value="{{ $pendaftarans->nik }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="nama_balita">Nama</label>
                                        <input type="text" id="nama_balita" name="nama_balita" class="form-control"
                                            placeholder="Masukkan Nama Balita" value="{{ $pendaftarans->nama_balita }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="tempat_lahir">Tempat Lahir </label>
                                        <input type="text" id="tempat_lahir" name="tempat_lahir" class="form-control"
                                            placeholder="Masukkan Tempat Lahir Balita"
                                            value="{{ $pendaftarans->tempat_lahir }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="tanggal_lahir">Tanggal Lahir </label>
                                        <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-control"
                                            placeholder="Masukkan Tanggal Lahir Balita"
                                            value="{{ $pendaftarans->tanggal_lahir }}" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
                                        <select class="form-control" id="jenis_kelamin" name="jenis_kelamin">
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="laki-laki"
                                                {{ $pendaftarans->jenis_kelamin == 'laki-laki' ? 'selected' : '' }}>Laki -
                                                Laki</option>
                                            <option value="perempuan"
                                                {{ $pendaftarans->jenis_kelamin == 'perempuan' ? 'selected' : '' }}>
                                                Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="bb_lahir">Berat Badan Lahir</label>
                                        <input type="float" id="bb_lahir" name="bb_lahir" class="form-control"
                                            placeholder="Masukkan Berat Badan Balita"
                                            value="{{ $pendaftarans->bb_lahir }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="tb_lahir">Tinggi Badan Lahir</label>
                                        <input type="float" id="tb_lahir" name="tb_lahir" class="form-control"
                                            placeholder="Masukkan Tinggi Badan Balita"
                                            value="{{ $pendaftarans->tb_lahir }}">
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <fieldset>
                                    <legend>Data Orang Tua</legend>
                                    <div class="mb-3">
                                        <label class="form-label" for="email_ortu">Email Ortu</label>
                                        <input type="text" id="email_ortu" name="email_ortu" class="form-control"
                                            placeholder="Masukkan Email Orang Tua"
                                            value="{{ $pendaftarans->email_ortu }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="nama_ortu">Nama</label>
                                        <input type="text" id="nama_ortu" name="nama_ortu" class="form-control"
                                            placeholder="Masukkan Nama Orang Tua" value="{{ $pendaftarans->nama_ortu }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="no_telepon">No WhatsApp</label>
                                        <input type="text" id="no_telepon" name="no_telepon" class="form-control"
                                            placeholder="Masukkan Nomor WA" value="{{ $pendaftarans->no_telepon }}">
                                    </div>
                                </fieldset>
                                <fieldset>
                                    <legend>Alamat Orang Tua</legend>
                                    <div class="mb-3">
                                        <label class="form-label" for="dukuh">Dukuh</label>
                                        <select class="form-control" id="dukuh" name="dukuh">
                                            <option value="">Pilih Nama Dukuh</option>
                                            <option value="Cendono"
                                                {{ $pendaftarans->dukuh == 'Cendono' ? 'selected' : '' }}>Cendono</option>
                                            <option value="Madu" {{ $pendaftarans->dukuh == 'Madu' ? 'selected' : '' }}>
                                                Madu</option>
                                            <option value="Kawakan"
                                                {{ $pendaftarans->dukuh == 'Kawakan' ? 'selected' : '' }}>Kawakan</option>
                                            <option value="Dawe" {{ $pendaftarans->dukuh == 'Dawe' ? 'selected' : '' }}>
                                                Dawe</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="rt">RT</label>
                                        <select class="form-control" id="rt" name="rt">
                                            <option value="">Pilih RT</option>
                                            <option value="01" {{ $pendaftarans->rt == '01' ? 'selected' : '' }}>01
                                            </option>
                                            <option value="02" {{ $pendaftarans->rt == '02' ? 'selected' : '' }}>02
                                            </option>
                                            <option value="03" {{ $pendaftarans->rt == '03' ? 'selected' : '' }}>03
                                            </option>
                                            <option value="04" {{ $pendaftarans->rt == '04' ? 'selected' : '' }}>04
                                            </option>
                                            <option value="05" {{ $pendaftarans->rt == '05' ? 'selected' : '' }}>05
                                            </option>
                                            <option value="06" {{ $pendaftarans->rt == '06' ? 'selected' : '' }}>06
                                            </option>
                                            <option value="07" {{ $pendaftarans->rt == '07' ? 'selected' : '' }}>07
                                            </option>
                                             <option value="08" {{ $pendaftarans->rt == '08' ? 'selected' : '' }}>08
            
                                            </option>
                                             <option value="09" {{ $pendaftarans->rt == '09' ? 'selected' : '' }}>09
                                            </option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="rw">RW</label>
                                        <select class="form-control" id="rw" name="rw">
                                            <option value="">Pilih RW</option>
                                            <option value="01" {{ $pendaftarans->rw == '01' ? 'selected' : '' }}>01
                                            </option>
                                            <option value="02" {{ $pendaftarans->rw == '02' ? 'selected' : '' }}>02
                                            </option>
                                            <option value="03" {{ $pendaftarans->rw == '03' ? 'selected' : '' }}>03
                                            </option>
                                            <option value="04" {{ $pendaftarans->rw == '04' ? 'selected' : '' }}>04
                                            </option>
                                            <option value="05" {{ $pendaftarans->rw == '05' ? 'selected' : '' }}>05
                                            </option>
                                            <option value="06" {{ $pendaftarans->rw == '06' ? 'selected' : '' }}>06
                                            </option>
                                            <option value="07" {{ $pendaftarans->rw == '07' ? 'selected' : '' }}>07
                                            </option>
                                            <option value="08" {{ $pendaftarans->rw == '08' ? 'selected' : '' }}>08
            
                                            </option>
                                             <option value="09" {{ $pendaftarans->rw == '09' ? 'selected' : '' }}>09
                                            </option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="pekerjaan">Pekerjaan</label>
                                        <select class="form-control" id="pekerjaan" name="pekerjaan"
                                            onchange="checkOtherOption()">
                                            <option value="">Pilih Pekerjaan</option>
                                            <option value="Belum/Tidak Bekerja"
                                                {{ $pendaftarans->pekerjaan == 'Belum/Tidak Bekerja' ? 'selected' : '' }}>
                                                Belum/Tidak Bekerja</option>
                                            <option value="Mengurus Rumah Tangga"
                                                {{ $pendaftarans->pekerjaan == 'Mengurus Rumah Tangga' ? 'selected' : '' }}>
                                                Mengurus Rumah Tangga</option>
                                            <option value="Pensiunan"
                                                {{ $pendaftarans->pekerjaan == 'Pensiunan' ? 'selected' : '' }}>Pensiunan
                                            </option>
                                            <option value="Pegawai Negeri Sipil (PNS)"
                                                {{ $pendaftarans->pekerjaan == 'Pegawai Negeri Sipil (PNS)' ? 'selected' : '' }}>
                                                Pegawai Negeri Sipil (PNS)</option>
                                            <option value="Perdagangan"
                                                {{ $pendaftarans->pekerjaan == 'Perdagangan' ? 'selected' : '' }}>
                                                Perdagangan</option>
                                            <option value="Petani/Pekebun"
                                                {{ $pendaftarans->pekerjaan == 'Petani/Pekebun' ? 'selected' : '' }}>
                                                Petani/Pekebun</option>
                                            <option value="Peternak"
                                                {{ $pendaftarans->pekerjaan == 'Peternak' ? 'selected' : '' }}>Peternak
                                            </option>
                                            <option value="Industri"
                                                {{ $pendaftarans->pekerjaan == 'Industri' ? 'selected' : '' }}>Industri
                                            </option>
                                            <option value="Karyawan Swasta"
                                                {{ $pendaftarans->pekerjaan == 'Karyawan Swasta' ? 'selected' : '' }}>
                                                Karyawan Swasta</option>
                                            <option value="Buruh Harian Lepas"
                                                {{ $pendaftarans->pekerjaan == 'Buruh Harian Lepas' ? 'selected' : '' }}>
                                                Buruh Harian Lepas</option>
                                            <option value="Perangkat Desa"
                                                {{ $pendaftarans->pekerjaan == 'Perangkat Desa' ? 'selected' : '' }}>
                                                Perangkat Desa</option>
                                            <option value="Kepala Desa"
                                                {{ $pendaftarans->pekerjaan == 'Kepala Desa' ? 'selected' : '' }}>Kepala
                                                Desa</option>
                                            <option value="Wiraswasta"
                                                {{ $pendaftarans->pekerjaan == 'Wiraswasta' ? 'selected' : '' }}>Wiraswasta
                                            </option>
                                            <option value="Dosen"
                                                {{ $pendaftarans->pekerjaan == 'Dosen' ? 'selected' : '' }}>Dosen</option>
                                            <option value="Guru"
                                                {{ $pendaftarans->pekerjaan == 'Guru' ? 'selected' : '' }}>Guru</option>
                                            <option value="Bidan"
                                                {{ $pendaftarans->pekerjaan == 'Bidan' ? 'selected' : '' }}>Bidan</option>
                                            <option value="Lainnya"
                                                {{ $pendaftarans->pekerjaan == 'Lainnya' ? 'selected' : '' }}>Lainnya
                                            </option>
                                        </select>
                                    </div>
                                    <div class="mb-3" id="pekerjaan_lainnya"
                                        style="display: {{ $pendaftarans->pekerjaan == 'Lainnya' ? 'block' : 'none' }};">
                                        <label class="form-label" for="pekerjaan_lainnya_input">Pekerjaan Lainnya</label>
                                        <input type="text" id="pekerjaan_lainnya_input" name="pekerjaan_lainnya"
                                            class="form-control" placeholder="Masukkan Pekerjaan"
                                            value="{{ $pendaftarans->pekerjaan_lainnya }}">
                                    </div>
                                </fieldset>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            @if (!empty($dataMutasi) && $dataMutasi->isNotEmpty())
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Riwayat Perpindahan Posyandu</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered">
                                <thead class="table-primary">
                                    <tr class="center">
                                        <th width="1%">NO.</th>
                                        <th width="5%">Tanggal Pindah</th>
                                        <th>Detail Perpindahan</th>
                                        <th>By Username</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataMutasi as $key => $mutasi)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $mutasi->updated_at }}</td>
                                            <td>Pindah dari posyandu <span
                                                    class="text-info fw-bold">{{ $mutasi->fromPosyandu }} </span> ke
                                                posyandu <span
                                                    class="text-primary fw-bold">{{ $mutasi->toPosyandu }}</span></td>
                                            <td>{{ $mutasi->username }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
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

        // Initialize the form with the correct state
        document.addEventListener("DOMContentLoaded", function() {
            checkOtherOption();
        });

        document.getElementById('pindahPosyanduBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Yakin ingin pindah Posyandu?',
                text: "Anda akan mengganti posyandu balita",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Pindah',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('nama_posyandu').disabled = false;
                    document.getElementById('pilihLokasiTxt').hidden = false;
                    document.getElementById('pindahPosyanduBtn').hidden = true;
                    // Swal.fire(
                    //     'Berhasil!',
                    //     'Posyandu berhasil diubah.',
                    //     'success'
                    // )
                }
            })
        });
    </script>

@endsection
