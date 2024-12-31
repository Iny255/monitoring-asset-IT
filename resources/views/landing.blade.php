@extends('layouts.app')

@section('header')
    @include('_partials.header')
@endsection

@section('content')
    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
        <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3 d-block bg-blue"
            data-navbar-on-scroll="data-navbar-on-scroll">
            <div class="container"><a class="navbar-brand" href=""><img src="assets/img/Landing/bgposyandu.png"
                        width="120" alt="logo" /></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="Toggle navigation"><span class="navbar-toggler-icon"> </span></button>
                <div class="collapse navbar-collapse border-top border-lg-0 mt-4 mt-lg-0" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto pt-2 pt-lg-0 font-base">
                        <li class="nav-item px-2"><a class="nav-link" aria-current="page" href="#about">Tentang Kami</a>
                        </li>
                        <li class="nav-item px-2"><a class="nav-link" href="#visi">Visi Misi</a></li>
                        <li class="nav-item px-2"><a class="nav-link" href="#struktur">Struktur Organisasi</a></li>
                        <li class="nav-item px-2"><a class="nav-link" href="#layanan">Layanan </a></li>
                        <li class="nav-item px-2"><a class="nav-link" href="#artikel">Artikel </a></li>
                        {{-- <li class="nav-item px-2"><a class="nav-link" href="#kontak">Kontak Kami</a></li> --}}
                        <ul class="navbar-nav ms-auto">
                            @auth
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        Selamat Datang Kembali {{ auth()->user()->username }}
                                    </a>
                                    <ul class="dropdown-menu">
                                       <a class="dropdown-item" href="{{ auth()->user()->getDashboardUrl() }}">
                                            <i class="bi bi-layout-text-window-reverse"></i>My Dashboard
                                        </a>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <form action="/logout" method="post">
                                                @csrf

                                                <button type="submit" class="dropdown-item"><i
                                                        class="bi bi-box-arrow-in-right"></i>Logout</button>
                                            </form>

                                        </li>
                                    </ul>
                                </li>
                            @else
                            </ul><a class="btn btn-sm btn-outline-primary rounded-pill order-1 order-lg-0 ms-lg-4"
                                href="/login">Masuk</a>
                        @endauth
                </div>
            </div>
        </nav>

        <section class="py-xxl-10 pb-0" id="home">
            <div class="bg-holder bg-size"
                style="background-image:url(assets/img/Landing/bg.png);background-position:top center;background-size:100%;">
            </div>
            <!--/.bg-holder-->

            <div class="container">
                <div class="row min-vh-xl-100 min-vh-xxl-25">
                    {{-- <div class="col-md-5 col-xl-6 col-xxl-7 order-0 order-md-1 text-end"><img class="pt-7 pt-md-0 w-100" src="assets/img/gallery/hero.png" alt="hero-header" /></div> --}}
                    <div class="col-md-75 col-xl-6 col-xxl-5 text-md-start text-center py-6">
                        <h1 class="fw-light font-base fs-6 fs-xxl-7"> <strong>Selamat Datang di Sistem
                                Monitoring</strong><br /> Posyandu Desa Cendono Kabupaten Kudus</strong></h1>
                        <p class="fs-1 mb-5"><br />Dukung kesehatan dan perkembangan anak<br /> dengan datang ke Posyandu
                            secara rutin. </p>
                        {{-- <a class="btn btn-lg btn-primary rounded-pill" href="/register" role="button">Daftar Sekarang</a> --}}
                    </div>
                </div>
            </div>
        </section>


        <!-- ============================================-->
        <!-- <section> begin ============================-->
        {{-- <section class="py-5" id="departments">

        <div class="container">
          <div class="row">
            <div class="col-12 py-3">
              <div class="bg-holder bg-size" style="background-image:url(assets/img/gallery/bg-departments.png);background-position:top center;background-size:contain;">
              </div> --}}
        <!--/.bg-holder-->

        {{-- <h1 class="text-center">OUR DEPARTMENTS</h1>
            </div>
          </div>
        </div>
        <!-- end of .container-->

      </section>
      <!-- <section> close ============================-->
      <!-- ============================================-->




      <!-- ============================================-->
      <!-- <section> begin ============================-->
      <section class="py-0">

        <div class="container">
          <div class="row py-5 align-items-center justify-content-center justify-content-lg-evenly">
            <div class="col-auto col-md-4 col-lg-auto text-xl-start">
              <div class="d-flex flex-column align-items-center">
                <div class="icon-box text-center"><a class="text-decoration-none" href="#!"><img class="mb-3 deparment-icon" src="assets/img/icons/neurology.png" alt="..." /><img class="mb-3 deparment-icon-hover" src="assets/img/icons/neurology.svg" alt="..." />
                    <p class="fs-1 fs-xxl-2 text-center">Neurology</p>
                  </a></div>
              </div>
            </div>
            <div class="col-auto col-md-4 col-lg-auto text-xl-start">
              <div class="d-flex flex-column align-items-center">
                <div class="icon-box text-center"><a class="text-decoration-none" href="#!"><img class="mb-3 deparment-icon" src="assets/img/icons/eye-care.png" alt="..." /><img class="mb-3 deparment-icon-hover" src="assets/img/icons/eye-care.svg" alt="..." />
                    <p class="fs-1 fs-xxl-2 text-center">Eye care</p>
                  </a></div>
              </div>
            </div>
            <div class="col-auto col-md-4 col-lg-auto text-xl-start">
              <div class="d-flex flex-column align-items-center">
                <div class="icon-box text-center"><a class="text-decoration-none" href="#!"><img class="mb-3 deparment-icon" src="assets/img/icons/cardiac.png" alt="..." /><img class="mb-3 deparment-icon-hover" src="assets/img/icons/cardiac.svg" alt="..." />
                    <p class="fs-1 fs-xxl-2 text-center">Cardiac care</p>
                  </a></div>
              </div>
            </div>
            <div class="col-auto col-md-4 col-lg-auto text-xl-start">
              <div class="d-flex flex-column align-items-center">
                <div class="icon-box text-center"><a class="text-decoration-none" href="#!"><img class="mb-3 deparment-icon" src="assets/img/icons/heart.png" alt="..." /><img class="mb-3 deparment-icon-hover" src="assets/img/icons/heart.svg" alt="..." />
                    <p class="fs-1 fs-xxl-2 text-center">Heart care</p>
                  </a></div>
              </div>
            </div>
            <div class="col-auto col-md-4 col-lg-auto text-xl-start">
              <div class="d-flex flex-column align-items-center">
                <div class="icon-box text-center"><a class="text-decoration-none" href="#!"><img class="mb-3 deparment-icon" src="assets/img/icons/osteoporosis.png" alt="..." /><img class="mb-3 deparment-icon-hover" src="assets/img/icons/osteoporosis.svg" alt="..." />
                    <p class="fs-1 fs-xxl-2 text-center">Osteoporosis</p>
                  </a></div>
              </div>
            </div>
            <div class="col-auto col-md-4 col-lg-auto text-xl-start">
              <div class="d-flex flex-column align-items-center">
                <div class="icon-box text-center"><a class="text-decoration-none" href="#!"><img class="mb-3 deparment-icon" src="assets/img/icons/ent.png" alt="..." /><img class="mb-3 deparment-icon-hover" src="assets/img/icons/ent.svg" alt="..." />
                    <p class="fs-1 fs-xxl-2 text-center">ENT</p>
                  </a></div>
              </div>
            </div>
          </div>
        </div>
        <!-- end of .container-->

      </section> --}}
        <!-- <section> close ============================-->
        <!-- ============================================-->

        <!-- ============================================-->
        <!-- <section> begin ============================-->
        <section class="pb-0" id="layanan">

            <div class="container">
                <div class="row">
                    {{-- <div class="col-12 py-3">
              <div class="bg-holder bg-size" style="background-image:url(assets/img/gallery/doctors-us.png);background-position:top center;background-size:contain;">
              </div> --}}
                    <!--/.bg-holder-->

                    <h1 class="text-center">JENIS LAYANAN</h1>
                </div>
            </div>
            </div>
            <!-- end of .container-->

        </section>
        <!-- <section> close ============================-->
        <!-- ============================================-->


        <section class="py-5">
            {{-- <div class="bg-holder bg-size" style="background-image:url(assets/img/Landing/bg1.png);background-position:top center;background-size:auto;">
        </div> --}}
            <!--/.bg-holder-->

            <div class="container">
                <div class="row flex-center">
                    <div class="col-xl-10 px-0">
                        <div class="row h-100 m-lg-1 mx-3 mt-2 mx-md-4 my-md-6">
                            <div class="col-md-3 mb-4">
                                <div class="card card-span h-100 shadow">
                                    <div class="card-body d-flex flex-column flex-center py-5">
                                        <img src="assets/img/Landing/pediatrics.png" width="128" alt="..." />
                                        <h5 class="mt-3">Pemeriksaan Rutin</h5>
                                        <div class="text-center">
                                            <p class="mb-0 fs-xxl-1">Segera periksakan tinggi dan berat badan balita secara
                                                berkala.</p>
                                            {{-- <button class="btn btn-outline-secondary rounded-pill"
                                                type="submit">Selengkapnya</button> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="card card-span h-100 shadow">
                                    <div class="card-body d-flex flex-column flex-center py-5">
                                        <img src="assets/img/Landing/family.png" width="128" alt="..." />
                                        <h5 class="mt-3">Konsultasi Gizi</h5>
                                        <div class="text-center">
                                            <p class="mb-0 fs-xxl-1">Konsultasikan gizi jika terdapat masalah tumbuh,
                                                kembang balita.
                                            </p>

                                            {{-- <button class="btn btn-outline-secondary rounded-pill"
                                                type="submit">Selengkapnya</button> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="card card-span h-100 shadow">
                                    <div class="card-body d-flex flex-column flex-center py-5">
                                        <img src="assets/img/Landing/maternity.png" width="128" alt="..." />
                                        <div class="text-center">
                                            <h5 class="mt-3">Pemantauan Pertumbuhan</h5>

                                            <p class="mb-0 fs-xxl-1">Pantau pertumbuhan balita secara berkala.</p>

                                            {{-- <button class="btn btn-outline-secondary rounded-pill"
                                                type="submit">Selengkapnya</button> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="card card-span h-100 shadow">
                                    <div class="card-body d-flex flex-column flex-center py-5">
                                        <img src="assets/img/Landing/notebook.png" width="128" alt="..." />
                                        <h5 class="mt-3">Pengukuran Rutin</h5>
                                        <div class="text-center">
                                            <p class="mb-0 fs-xxl-1">Segera datang ke posyandu untuk pemeriksaan balita.
                                            </p>
                                            {{-- <button class="btn btn-outline-secondary rounded-pill"
                                                type="submit">Selengkapnya</button> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- <section> begin ============================-->
            <section class="pb-0" id="about">
                {{-- <div class="bg-holder bg-size" style="background-image:url(assets/img/Landing/bg1.png);background-position:top center;background-size:contain;">
              </div> --}}
                <div class="container">
                    <div class="row">
                        <div class="col-12 py-3">

                            <!--/.bg-holder-->

                            <h1 class="text-center">TENTANG KAMI</h1>
                        </div>
                    </div>
                </div>
                <!-- end of .container-->

            </section>
            <!-- <section> close ============================-->
            <!-- ============================================-->



            <section class="py-5">
                <div class="container d-flex justify-content-center">
                    <div class="col-md-8 text-center">
                        {{-- <div class="bg-holder bg-size" style="background-image:url(assets/img/Landing/about-bg1.png);background-position:top center;background-size:contain;">
        </div> --}}
                        <!--/.bg-holder-->

                        {{-- <div class="container"> --}}
                        {{-- <div class="row align-items-center"> --}}
                        {{-- <div class="col-md-6 order-lg-1 mb-5 mb-lg-0"><img class="fit-cover rounded-circle w-100" src="assets/img/gallery/health-care.png" alt="..." /></div> --}}
                        {{-- <div class="col-md-6 text-center text-md-start"> --}}
                        {{-- <h2 class="fw-bold mb-4"> </h2> --}}
                        <p>Posyandu Desa Cendono, Kabupaten Kudus, merupakan salah satu Lembaga yang berperan penting
                            dalam memberikan layanan kesehatan dasar kepada balita dan ibu hamil. Posyandu ini didirikan
                            dengan tujuan meningkatkan
                            kualitas kesehatan masyarakat melalui berbagai program kesehatan yang mudah diakses oleh warga
                            desa. Di Desa Cendono terdapat 9 Posyandu yang tersebar di berbagai dusun.
                            Masing-masing Posyandu dikelola oleh kader posyandu yang terdiri dari anggota masyarakat
                            setempat dan bidan desa.</p>
                        {{-- <div class="py-3"> --}}
                        {{-- <button class="btn btn-lg btn-outline-primary rounded-pill" type="submit">Learn more </button> --}}
                    </div>
                </div>
                </div>
                </div>
            </section>


            <section class="bg-secondary" id="visi">
                {{-- <div class="bg-holder" style="background-image:url(assets/img/gallery/bg-eye-care.png);background-position:center;background-size:contain;">
        </div> --}}
                <!--/.bg-holder-->

                {{-- <div class="container">
          <div class="row align-items-center"> --}}
                {{-- <div class="col-md-5 col-xxl-6"><img class="img-fluid" src="assets/img/gallery/eye-care.png" alt="..." /></div> --}}
                {{-- <div class="col-md-7 col-xxl-6 text-center text-md-start"> --}}
                <div class="container d-flex justify-content-center">
                    <div class="col-md-8 text-center">
                        <h2 class="fw-bold text-light mb-4 mt-4 mt-lg-0 ">VISI</h2>

                        <p class="text-light">Terwujudnya Posyandu Desa Cendono sebagai pusat pelayanan kesehatan ibu dan
                            anak yang mandiri, professional,
                            dan berkualitas dalam mendukung tumbuh kembang balita kesejahteraan keluarga.</p>
                    </div>
                </div>
                <div class="container d-flex justify-content-center">
                    <div class="col-md-8 text-center">
                        <h2 class="fw-bold text-light mb-4 mt-4 mt-lg-0 ">MISI</h2>


                        <ul class="text-light">
                            <p>Meningkatkan kualitas pelayanan kesehatan.<br />
                                Mendorong partisipasi aktif masyarakat.<br />
                                Mengadakan pemantauan dan evaluasi terhadap tumbuh kembang balita dan program-program yang
                                dijalankan.</p>
                        </ul>
                    </div>
                </div>
                {{-- <div class="py-3"><a class="btn btn-lg btn-light rounded-pill" href="#!" role="button">Learn more </a></div>
            </div> --}}

            </section>


            <!-- ============================================-->
            <!-- <section> begin ============================-->
            <style>
                .struktur-img {
                    width: 80%;
                    /* Atur lebar gambar menjadi 80% dari lebar kontainer */
                    max-width: 1000px;
                    /* Atur lebar maksimal gambar */
                    height: auto;
                    /* Mempertahankan rasio aspek gambar */
                    display: block;
                    /* Mengubah gambar menjadi elemen block */
                    margin: 0 auto;
                    /* Mengatur gambar agar berada di tengah */
                }
            </style>

            <section class="pb-0" id="struktur">
                <div class="container">
                    <div class="row">
                        <div class="col-12 py-3">
                            <h1 class="text-center">STRUKTUR ORGANISASI</h1>
                            <div class="text-center">
                                <img src="assets/img/Landing/Struktur Posyandu.png" alt="Struktur Organisasi Posyandu"
                                    class="struktur-img">
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- <section> close ============================-->
            <!-- ============================================-->


            <!-- ============================================-->
            <!-- <section> begin ============================-->
            <section class="py-5" id="artikel">

                <div class="container">
                    <div class="row">
                        {{-- <div class="col-12 py-3">
              {{-- <div class="bg-holder bg-size" style="background-image:url(assets/img/Landing/blog-post.png);background-position:top center;background-size:contain;">
              </div> --}}
                        <!--/.bg-holder-->

                        <h1 class="text-center"></h1>
                    </div>
                </div>
                </div>
                <!-- end of .container-->

            </section>
            <!-- <section> close ============================-->
            <!-- ============================================-->


            <section>
                <div class="bg-holder bg-size"
                    style="background-image:url(assets/img/Landing/bg1.png);background-position:top center;background-size:auto;">
                </div>
                <!--/.bg-holder-->

                <div class="container">
                    <div class="row">
                        <div class="col-sm-6 col-lg-3 mb-4">
                            <div class="card h-100 shadow card-span rounded-3"><img class="card-img-top rounded-top-3"
                                    src="{{ asset('assets/img/Landing/artikel-1.png') }}" alt="news" />
                                <div class="card-body"><span class="fs--1 text-primary me-3">Kesehatan</span>
                                    <svg class="bi bi-calendar2 me-2" xmlns="http://www.w3.org/2000/svg" width="12"
                                        height="12" fill="currentColor" viewBox="0 0 16 16">
                                        <path
                                            d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H2z">
                                        </path>
                                        <path
                                            d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5V4z">
                                        </path>
                                    </svg><span class="fs--1 text-900">Sep 4, 2024</span><span class="fs--1"></span>
                                    <h5 class="font-base fs-lg-0 fs-xl-1 my-3">Indonesia Berhasil Turunkan Stunting 9,63
                                        Persen Selama Lima Tahun</h5><a class="stretched-link" target="_blank"
                                        href="https://www.antaranews.com/berita/4308527/indonesia-berhasil-turunkan-stunting-963-persen-selama-lima-tahun">baca
                                        selengkapnya</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-4">
                            <div class="card h-100 shadow card-span rounded-3"><img class="card-img-top rounded-top-3"
                                    src="{{ asset('assets/img/Landing/artikel-2.jpg') }}" alt="news" />
                                <div class="card-body"><span class="fs--1 text-primary me-3">Kesehatan</span>
                                    <svg class="bi bi-calendar2 me-2" xmlns="http://www.w3.org/2000/svg" width="12"
                                        height="12" fill="currentColor" viewBox="0 0 16 16">
                                        <path
                                            d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H2z">
                                        </path>
                                        <path
                                            d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5V4z">
                                        </path>
                                    </svg><span class="fs--1 text-900">Nov 21, 2024</span><span class="fs--1"></span>
                                    <h5 class="font-base fs-lg-0 fs-xl-1 my-3">Masalah Gizi pada Balita yang perlu
                                        Diperhatikan Orangtua</h5><a class="stretched-link" target="_blank"
                                        href="https://hellosehat.com/parenting/anak-1-sampai-5-tahun/gizi-balita/masalah-gizi-pada-balita/">baca
                                        selengkapnya</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-4">
                            <div class="card h-100 shadow card-span rounded-3"><img class="card-img-top rounded-top-3"
                                    src="{{ asset('assets/img/Landing/artikel-3.jpeg') }}" alt="news" />
                                <div class="card-body"><span class="fs--1 text-primary me-3">Kesehatan</span>
                                    <svg class="bi bi-calendar2 me-2" xmlns="http://www.w3.org/2000/svg" width="12"
                                        height="12" fill="currentColor" viewBox="0 0 16 16">
                                        <path
                                            d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H2z">
                                        </path>
                                        <path
                                            d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5V4z">
                                        </path>
                                    </svg><span class="fs--1 text-900">Nov 25, 2021</span><span class="fs--1"></span>
                                    <h5 class="font-base fs-lg-0 fs-xl-1 my-3">3 Skrining Kesehatan pada Bayi Baru Lahir,
                                        Orang Tua Wajib Tahu!</h5><a class="stretched-link" target="_blank"
                                        href="https://genbest.id/articles/3-skrining-kesehatan-pada-bayi-baru-lahir-orang-tua-wajib-tahu">baca
                                        selengkapnya</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-4">
                            <div class="card h-100 shadow card-span rounded-3"><img class="card-img-top rounded-top-3"
                                    src="{{ asset('assets/img/Landing/artikel-4.jpg') }}" alt="news" />
                                <div class="card-body"><span class="fs--1 text-primary me-3">Kesehatan</span>
                                    <svg class="bi bi-calendar2 me-2" xmlns="http://www.w3.org/2000/svg" width="12"
                                        height="12" fill="currentColor" viewBox="0 0 16 16">
                                        <path
                                            d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H2z">
                                        </path>
                                        <path
                                            d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5V4z">
                                        </path>
                                    </svg><span class="fs--1 text-900">Des 1, 2024</span><span class="fs--1"></span>
                                    <h5 class="font-base fs-lg-0 fs-xl-1 my-3"> Gangguan Tumbuh Kembang Anak: Ciri-ciri,
                                        Dampak hingga Cara Penanganannya</h5>
                                    <a class="stretched-link" target="_blank"
                                        href="https://www.ekahospital.com/articles/gangguan-tumbuh-kembang-anak-ciriciri-dampak-hingga-cara-penanganannya">baca
                                        selengkapnya</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!--/.bg-holder-->


            {{-- <section class="py-0 bg-secondary" id="kontak">
                <div class="bg-holder opacity-25"
                    style="background-image:url(assets/img/gallery/dot-bg.png);background-position:top left;margin-top:-3.125rem;background-size:auto;">
                </div>
                <!--/.bg-holder-->

                <div class="container">
                    <div class="row py-8">
                        <div class="col-12 col-sm-12 col-lg-6 mb-4 order-0 order-sm-0"><a class="text-decoration-none"
                                href="#"><img src="assets/img/gallery/footer-logo.png" height="51"
                                    alt="" /></a>
                            <p class="text-light my-4">The world's most trusted <br />telehealth company.</p>
                        </div>
                        <div class="col-6 col-sm-4 col-lg-2 mb-3 order-2 order-sm-1">
                            <h5 class="lh-lg fw-bold mb-4 text-light font-sans-serif">Departments</h5>
                            <ul class="list-unstyled mb-md-4 mb-lg-0">
                                <li class="lh-lg"><a class="footer-link" href="#!">Eye care</a></li>
                                <li class="lh-lg"><a class="footer-link" href="#!">Cardiac are</a></li>
                                <li class="lh-lg"><a class="footer-link" href="#!">Heart care</a></li>
                            </ul>
                        </div>
                        <div class="col-6 col-sm-4 col-lg-2 mb-3 order-3 order-sm-2">
                            <h5 class="lh-lg fw-bold text-light mb-4 font-sans-serif">Membership</h5>
                            <ul class="list-unstyled mb-md-4 mb-lg-0">
                                <li class="lh-lg"><a class="footer-link" href="#!">Free trial</a></li>
                                <li class="lh-lg"><a class="footer-link" href="#!">Silver</a></li>
                                <li class="lh-lg"><a class="footer-link" href="#!">Premium</a></li>
                            </ul>
                        </div>
                        <div class="col-6 col-sm-4 col-lg-2 mb-3 order-3 order-sm-2">
                            <h5 class="lh-lg fw-bold text-light mb-4 font-sans-serif"> Customer Care</h5>
                            <ul class="list-unstyled mb-md-4 mb-lg-0">
                                <li class="lh-lg"><a class="footer-link" href="#!">About Us</a></li>
                                <li class="lh-lg"><a class="footer-link" href="#!">Contact US</a></li>
                                <li class="lh-lg"><a class="footer-link" href="#!">Get Update</a></li>
                            </ul>
                        </div>
                    </div>
                </div> --}}
            @endsection
