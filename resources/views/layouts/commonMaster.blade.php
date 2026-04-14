<!DOCTYPE html>

<html class="light-style layout-menu-fixed" data-theme="theme-default" data-assets-path="{{ asset('/assets') . '/' }}"
    data-base-url="{{ url('/') }}" data-framework="laravel" data-template="vertical-menu-laravel-template-free">

<head>

    <meta charset="utf-8" />

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no,
minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title') | Monitoring Sembilan</title>

    <meta name="description" content="{{ config('variables.templateDescription') ?? '' }}" />

    <meta name="keywords" content="{{ config('variables.templateKeyword') ?? '' }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="canonical" href="{{ config('variables.productPage') ?? '' }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/logo-bulat.png') }}" />

    <!-- Styles Template -->
    @include('layouts/sections/styles')

    <!-- Core-dark CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/core-dark.css') }}">


    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    <!-- Scripts Helper -->
    @include('layouts/sections/scriptsIncludes')

    <style>
        /* NAVBAR BIRU */

        .layout-navbar,
        .navbar-detached,
        .bg-navbar-theme {
            /* Menggunakan warna biru solid dari logo untuk background */
            background: #003060 !important;
            /* Menghapus gradient agar solid seperti logo */
            border: none !important;
        }

        .layout-navbar .nav-link,
        .layout-navbar span,
        .layout-navbar i {
            /* Mempertahankan warna teks putih untuk kontras yang baik */
            color: #fff !important;
        }

        .layout-navbar {
            /* Menyesuaikan bayangan agar cocok dengan warna biru yang lebih gelap */
            box-shadow: 0 4px 15px rgba(1, 33, 64, 0.35);
        }

        /* SWEETALERT ZINDEX */

        .swal2-container {
            z-index: 9999 !important;
        }
    </style>

</head>

<body>

    <!-- Layout Content -->
    @yield('layoutContent')
    <!-- /Layout Content -->

    <!-- Scripts Template -->
    @include('layouts/sections/scripts')

    <!-- Sweetalert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @yield('scripts')

    <!-- ========================= -->
    <!-- DARK MODE SCRIPT -->
    <!-- ========================= -->

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const html = document.documentElement;
            const toggleBtn = document.getElementById("darkModeToggle");
            const icon = document.getElementById("darkIcon");

            if (!toggleBtn) return;

            function setDarkMode(isDark) {

                if (isDark) {

                    html.classList.add("dark-style");

                    icon.classList.remove("bx-moon");
                    icon.classList.add("bx-sun");

                    localStorage.setItem("theme", "dark");

                } else {

                    html.classList.remove("dark-style");

                    icon.classList.remove("bx-sun");
                    icon.classList.add("bx-moon");

                    localStorage.setItem("theme", "light");

                }

            }

            const savedTheme = localStorage.getItem("theme");

            setDarkMode(savedTheme === "dark");

            toggleBtn.addEventListener("click", function() {

                const isDark = html.classList.contains("dark-style");

                setDarkMode(!isDark);

            });

        });
    </script>

    
    
</body>

</html>
