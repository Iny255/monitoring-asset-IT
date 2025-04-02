@extends('layouts/contentNavbarLayout')

@section('title', 'edit-jadwal')

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

    <a href="/dashboard/lokasi-posyandu" class="btn btn-secondary mb-3">Kembali</a>

    <div class="row">
        <div class="col-lg-12">
            <form method="post" action="/dashboard/posyandu/update/{{ $lokasis->id }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Kolom Kiri -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Edit Data Posyandu</h5>
                            </div>
                            <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label" for="nama_posyandu">Nama Posyandu</label>
                                <select class="form-control @error('nama_posyandu') is-invalid @enderror" id="nama_posyandu" name="nama_posyandu">
                                    <option value="">Pilih Nama Posyandu</option>
                                    <option value="Kipas" {{ old('nama_posyandu', $lokasis->nama_posyandu) == 'Kipas' ? 'selected' : '' }}>Kipas</option>
                                    <option value="Asyifa 1" {{ old('nama_posyandu', $lokasis->nama_posyandu) == 'Asyifa 1' ? 'selected' : '' }}>Asyifa 1</option>
                                    <option value="Asyifa 2" {{ old('nama_posyandu', $lokasis->nama_posyandu) == 'Asyifa 2' ? 'selected' : '' }}>Asyifa 2</option>
                                    <option value="Srikandi 1" {{ old('nama_posyandu', $lokasis->nama_posyandu) == 'Srikandi 1' ? 'selected' : '' }}>Srikandi 1</option>
                                    <option value="Srikandi 2" {{ old('nama_posyandu', $lokasis->nama_posyandu) == 'Srikandi 2' ? 'selected' : '' }}>Srikandi 2</option>
                                    <option value="Anggrek" {{ old('nama_posyandu', $lokasis->nama_posyandu) == 'Anggrek' ? 'selected' : '' }}>Anggrek</option>
                                    <option value="Melati Putih 1" {{ old('nama_posyandu', $lokasis->nama_posyandu) == 'Melati Putih 1' ? 'selected' : '' }}>Melati Putih 1</option>
                                    <option value="Melati Putih 2" {{ old('nama_posyandu', $lokasis->nama_posyandu) == 'Melati Putih 2' ? 'selected' : '' }}>Melati Putih 2</option>
                                    <option value="Pergiwati" {{ old('nama_posyandu', $lokasis->nama_posyandu) == 'Pergiwati' ? 'selected' : '' }}>Pergiwati</option>
                                </select>
                                @error('nama_posyandu')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        
                                <div class="mb-3">
                                    <label class="form-label" for="dukuh">Dukuh</label>
                                    <select class="form-control" id="dukuh" name="dukuh">
                                        <option value="">Pilih Nama Dukuh</option>
                                        <option value="Cendono" {{ $lokasis->dukuh == 'Cendono' ? 'selected' : '' }}>
                                            Cendono</option>
                                        <option value="Madu" {{ $lokasis->dukuh == 'Madu' ? 'selected' : '' }}>Madu
                                        </option>
                                        <option value="Kawakan" {{ $lokasis->dukuh == 'Kawakan' ? 'selected' : '' }}>
                                            Kawakan</option>
                                        <option value="Dawe" {{ $lokasis->dukuh == 'Dawe' ? 'selected' : '' }}>Dawe
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="rt">RT</label>
                                    <select class="form-control" id="rt" name="rt">
                                        <option value="">Pilih RT</option>
                                        <option value="01" {{ $lokasis->rt == '01' ? 'selected' : '' }}>01</option>
                                        <option value="02" {{ $lokasis->rt == '02' ? 'selected' : '' }}>02</option>
                                        <option value="03" {{ $lokasis->rt == '03' ? 'selected' : '' }}>03</option>
                                        <option value="04" {{ $lokasis->rt == '04' ? 'selected' : '' }}>04</option>
                                        <option value="05" {{ $lokasis->rt == '05' ? 'selected' : '' }}>05</option>
                                        <option value="06" {{ $lokasis->rt == '06' ? 'selected' : '' }}>06</option>
                                        <option value="07" {{ $lokasis->rt == '07' ? 'selected' : '' }}>07</option>
                                        <option value="08" {{ $lokasis->rt == '08' ? 'selected' : '' }}>08</option>
                                        <option value="09" {{ $lokasis->rt == '09' ? 'selected' : '' }}>09</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="rw">RW</label>
                                    <select class="form-control" id="rw" name="rw">
                                        <option value="">Pilih RW</option>
                                        <option value="01" {{ $lokasis->rw == '01' ? 'selected' : '' }}>01</option>
                                        <option value="02" {{ $lokasis->rw == '02' ? 'selected' : '' }}>02</option>
                                        <option value="03" {{ $lokasis->rw == '03' ? 'selected' : '' }}>03</option>
                                        <option value="04" {{ $lokasis->rw == '04' ? 'selected' : '' }}>04</option>
                                        <option value="05" {{ $lokasis->rw == '05' ? 'selected' : '' }}>05</option>
                                        <option value="06" {{ $lokasis->rw == '06' ? 'selected' : '' }}>06</option>
                                        <option value="07" {{ $lokasis->rw == '07' ? 'selected' : '' }}>07</option>
                                        <option value="08" {{ $lokasis->rw == '08' ? 'selected' : '' }}>08</option>
                                        <option value="09" {{ $lokasis->rw == '09' ? 'selected' : '' }}>09</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    {{-- <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label" for="tanggal">Tanggal Posyandu</label>
                                    <input type="date" id="tanggal" name="tanggal" class="form-control"
                                        value="{{ $lokasis->tanggal }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="jam_mulai">Jam Mulai</label>
                                    <input type="time" id="jam_mulai" name="jam_mulai" class="form-control"
                                        value="{{ $lokasis->jam_mulai }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="jam_selesai">Jam Selesai</label>
                                    <input type="time" id="jam_selesai" name="jam_selesai" class="form-control"
                                        value="{{ $lokasis->jam_selesai }}">
                                </div>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </form>
        </div>
    </div>

@endsection
