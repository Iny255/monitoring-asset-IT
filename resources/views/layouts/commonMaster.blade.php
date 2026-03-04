<!DOCTYPE html>

<html class="light-style layout-menu-fixed" data-theme="theme-default" data-assets-path="{{ asset('/assets') . '/' }}"
    data-base-url="{{ url('/') }}" data-framework="laravel" data-template="vertical-menu-laravel-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title') | Monitoring Sembilan </title>
    <meta name="description"
        content="{{ config('variables.templateDescription') ? config('variables.templateDescription') : '' }}" />
    <meta name="keywords"
        content="{{ config('variables.templateKeyword') ? config('variables.templateKeyword') : '' }}">
    <!-- laravel CRUD token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Canonical SEO -->
    <link rel="canonical" href="{{ config('variables.productPage') ? config('variables.productPage') : '' }}">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/logo-bulat.png') }}" />
 



    <!-- Include Styles -->
    @include('layouts/sections/styles')

    <!-- Include Scripts for customizer, helper, analytics, config -->
    @include('layouts/sections/scriptsIncludes')
</head>

<body>


    <!-- Layout Content -->
    @yield('layoutContent')
    <!--/ Layout Content -->



    <!-- Include Scripts -->
    @include('layouts/sections/scripts')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('scripts')

    <style>
        .swal2-container {
            z-index: 9999 !important;
        }
    </style>
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

            // Load saved theme
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
