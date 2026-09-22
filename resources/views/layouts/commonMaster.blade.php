<!DOCTYPE html>

<html class="light-style layout-menu-fixed" data-theme="theme-default" data-assets-path="{{ asset('/assets') . '/' }}"
    data-base-url="{{ url('/') }}" data-framework="laravel" data-template="vertical-menu-laravel-template-free">

<head>
    <script>
        // Inisialisasi tema instan sebelum render untuk mencegah FOUC (white flash)
        (function() {
            try {
                var theme = localStorage.getItem('theme');
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark-style');
                    document.documentElement.classList.remove('light-style');
                    document.documentElement.setAttribute('data-bs-theme', 'dark');
                } else {
                    document.documentElement.classList.add('light-style');
                    document.documentElement.classList.remove('dark-style');
                    document.documentElement.setAttribute('data-bs-theme', 'light');
                }
            } catch (e) {}
        })();
    </script>

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
    <link rel="icon" type="image/png" sizes="32x32"
        href="{{ asset('assets/img/favicon-monitoring.png?v=' . time()) }}">

    <link rel="shortcut icon" href="{{ asset('assets/img/favicon-monitoring.png?v=' . time()) }}">

    <!-- Styles Template -->
    @include('layouts/sections/styles')

    <!-- Core-dark CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/core-dark.css') }}">


    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    <!-- Scripts Helper -->
    @include('layouts/sections/scriptsIncludes')

    <style>


        /* SWEETALERT ZINDEX */

        .swal2-container {
            z-index: 9999 !important;
        }

        .app-footer {
            width: 100%;
            background: #fff;
            border-top: 1px solid #e5e7eb;
            padding: 12px 24px;
            position: relative;
            z-index: 99;
        }

        .layout-page {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content-wrapper {
            flex: 1;
        }

        .app-footer {
            width: 100%;
            background: #fff;
            border-top: 1px solid #e5e7eb;
            padding: 12px 24px;
        }

        .dark-style .app-footer {
            background: #2b2c40 !important;
            border-top-color: #444564 !important;
            color: #a3a4cc !important;
        }

        .footer-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        (function() {
            var html = document.documentElement;

            function applyTheme(theme) {
                var isDark = (theme === 'dark');

                if (isDark) {
                    html.classList.add("dark-style");
                    html.classList.remove("light-style");
                    html.setAttribute("data-bs-theme", "dark");
                } else {
                    html.classList.remove("dark-style");
                    html.classList.add("light-style");
                    html.setAttribute("data-bs-theme", "light");
                }

                try {
                    localStorage.setItem("theme", isDark ? "dark" : "light");
                } catch (e) {}

                // Sinkronkan semua ikon toggle mode malam di seluruh halaman
                var icons = document.querySelectorAll("#darkIcon, .dark-toggle-btn i");
                icons.forEach(function(icon) {
                    if (isDark) {
                        icon.classList.remove("bx-moon");
                        icon.classList.add("bx-sun");
                    } else {
                        icon.classList.remove("bx-sun");
                        icon.classList.add("bx-moon");
                    }
                });

                // Dispatch event tema jika chart atau komponen lain membutuhkan re-render
                window.dispatchEvent(new CustomEvent("themeChanged", { detail: { theme: isDark ? "dark" : "light" } }));
            }

            function syncCurrentTheme() {
                var currentTheme = "light";
                try {
                    var saved = localStorage.getItem("theme");
                    if (saved) {
                        currentTheme = saved;
                    } else if (html.classList.contains("dark-style")) {
                        currentTheme = "dark";
                    }
                } catch (e) {}

                applyTheme(currentTheme);
            }

            // Event delegation untuk tombol toggle mode malam (berfungsi di halaman mana pun dan tahan render dinamis)
            document.addEventListener("click", function(e) {
                var btn = e.target.closest("#darkModeToggle, .dark-toggle-btn");
                if (btn) {
                    e.preventDefault();
                    e.stopPropagation();
                    var isCurrentlyDark = html.classList.contains("dark-style");
                    applyTheme(isCurrentlyDark ? "light" : "dark");
                }
            });

            // Sinkronkan ikon dan state sesegera mungkin
            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", syncCurrentTheme);
            } else {
                syncCurrentTheme();
            }
            window.addEventListener("load", syncCurrentTheme);
        })();
    </script>



</body>

</html>
