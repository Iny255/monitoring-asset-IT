@extends('layouts/commonMaster')

@php

    /* Display elements */
    $contentNavbar = true;
    $containerNav = $containerNav ?? 'container-xxl';
    $isNavbar = $isNavbar ?? true;
    $isMenu = $isMenu ?? true;
    $isFlex = $isFlex ?? false;
    $isFooter = $isFooter ?? true;

    /* HTML Classes */
    $navbarDetached = 'navbar-detached';

    /* Content classes */
    $container = $container ?? 'container-xxl';

@endphp

@section('layoutContent')
    {{-- =====================================
   GLOBAL COMPANY THEME
===================================== --}}
    <style>
        :root {

            --theme-primary: {{ $theme['primary_color'] ?? '#0b2f57' }};
            --theme-secondary: {{ $theme['secondary_color'] ?? '#154b87' }};

        }

        /* =====================================
               BUTTON PRIMARY
            ===================================== */

        .btn-primary {

            background:
                linear-gradient(135deg,
                    var(--theme-primary),
                    var(--theme-secondary)) !important;

            border: none !important;

            color: #fff !important;

            box-shadow:
                0 4px 12px rgba(0, 0, 0, .10);

        }

        .btn-primary:hover {

            opacity: .95;

            transform: translateY(-1px);

        }

        /* =====================================
               TABLE HEADER
            ===================================== */

        .table thead {

            background:
                linear-gradient(135deg,
                    var(--theme-primary),
                    var(--theme-secondary)) !important;

        }

        .table thead th {

            color: #fff !important;

            border-color:
                rgba(255, 255, 255, .08) !important;

        }

        /* =====================================
       CARD TITLE / PAGE TITLE
    ===================================== */

        .card-title,
        .page-title,
        h4,
        h5,
        h6 {

            color: var(--theme-primary) !important;

        }


        /* =====================================
       TEXT PRIMARY
    ===================================== */

        .text-primary {

            color: var(--theme-primary) !important;

        }


        /* =====================================
       TABLE HEADER SNEAT
    ===================================== */

        .table:not(.table-dark) thead:not(.table-dark) th {

            background:
                linear-gradient(135deg,
                    var(--theme-primary),
                    var(--theme-secondary)) !important;

            color: #ffffff !important;

            border-color:
                rgba(255, 255, 255, .08) !important;

        }


        /* =====================================
       TABLE HEADER FIX
    ===================================== */

        table.dataTable thead th {

            background:
                linear-gradient(135deg,
                    var(--theme-primary),
                    var(--theme-secondary)) !important;

            color: #ffffff !important;

        }


        /* =====================================
       CARD HEADER
    ===================================== */

        .card-header {

            border-bottom:
                1px solid rgba(0, 0, 0, .05);

        }


        /* =====================================
       OUTLINE BUTTON
    ===================================== */

        .btn-outline-primary {

            border-color:
                var(--theme-primary) !important;

            color:
                var(--theme-primary) !important;

        }

        .btn-outline-primary:hover {

            background:
                linear-gradient(135deg,
                    var(--theme-primary),
                    var(--theme-secondary)) !important;

            color: #fff !important;

            border-color: transparent !important;

        }

        /* =====================================
               PAGINATION
            ===================================== */

        .page-item.active .page-link {

            background:
                linear-gradient(135deg,
                    var(--theme-primary),
                    var(--theme-secondary)) !important;

            border: none !important;

            color: #fff !important;

        }

        /* =====================================
               INPUT FOCUS
            ===================================== */

        .form-control:focus,
        .form-select:focus {

            border-color:
                var(--theme-primary) !important;

            box-shadow:
                0 0 0 .2rem rgba(0, 0, 0, .08) !important;

        }
    </style>


    <div class="layout-wrapper layout-content-navbar {{ $isMenu ? '' : 'layout-without-menu' }}">

        <div class="layout-container">

            @if ($isMenu)
                @include('layouts/sections/menu/verticalMenu')
            @endif

            <!-- Layout page -->
            <div class="layout-page">

                <!-- Navbar -->
                @if ($isNavbar)
                    @include('layouts/sections/navbar/navbar')
                @endif

                <!-- Content wrapper -->
                <div class="content-wrapper">

                    <!-- Content -->
                    @if ($isFlex)
                        <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
                        @else
                            <div class="{{ $container }} flex-grow-1 container-p-y">
                    @endif

                    @yield('content')

                </div>

                <!-- / Content -->

                <div class="content-backdrop fade"></div>

            </div>

        </div>

    </div>

    @if ($isMenu)
        <div class="layout-overlay layout-menu-toggle"></div>
    @endif

    <div class="drag-target"></div>

    </div>

    @stack('scripts')
@endsection
