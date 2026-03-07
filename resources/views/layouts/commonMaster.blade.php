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

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    <!-- Scripts Helper -->
    @include('layouts/sections/scriptsIncludes')

    <style>
        /* NAVBAR BIRU */

        .layout-navbar,
        .navbar-detached,
        .bg-navbar-theme {
            background: linear-gradient(45deg, #696cff, #5f61e6) !important;
            border: none !important;
        }

        .layout-navbar .nav-link,
        .layout-navbar span,
        .layout-navbar i {
            color: #fff !important;
        }

        .layout-navbar {
            box-shadow: 0 4px 15px rgba(105, 108, 255, .35);
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

    <!-- ========================= -->
    <!-- SIDEBAR COLLAPSE SCRIPT -->
    <!-- ========================= -->

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const toggle = document.getElementById("sidebarToggle");

            if (toggle) {

                toggle.addEventListener("click", function() {

                    document.body.classList.toggle("layout-menu-collapsed");

                });

            }

        });
    </script>
</body>

</html>
