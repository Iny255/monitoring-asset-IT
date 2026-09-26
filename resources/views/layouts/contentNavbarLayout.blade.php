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
            --theme-primary-rgb: {{ $theme['primary_rgb'] ?? '11, 47, 87' }};
            --primary-theme: {{ $theme['primary_color'] ?? '#0b2f57' }};
            --secondary-theme: {{ $theme['secondary_color'] ?? '#154b87' }};
        }

        .category-theme-icon,
        .dashboard-icon.category-theme-icon {
            background: var(--theme-primary) !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(var(--theme-primary-rgb), 0.20);
        }

        .category-theme-card {
            border-radius: 18px !important;
            transition: .25s ease;
            border: 1px solid #eef2f7 !important;
        }

        .category-theme-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, .08) !important;
        }

        /* =====================================
           BUTTON PRIMARY & OUTLINE (SOLID CORPORATE ACCENT)
        ===================================== */
        .btn-primary {
            background: var(--theme-primary) !important;
            background-color: var(--theme-primary) !important;
            border: 1px solid var(--theme-primary) !important;
            color: #fff !important;
            box-shadow: 0 2px 6px rgba(var(--theme-primary-rgb), 0.22) !important;
            transition: all 0.2s ease !important;
        }

        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active {
            background: var(--theme-secondary) !important;
            background-color: var(--theme-secondary) !important;
            border-color: var(--theme-secondary) !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(var(--theme-primary-rgb), 0.30) !important;
            transform: translateY(-1px);
        }

        .btn-outline-primary {
            border-color: var(--theme-primary) !important;
            color: var(--theme-primary) !important;
            background: transparent !important;
        }

        .btn-outline-primary:hover,
        .btn-outline-primary:focus,
        .btn-outline-primary:active {
            background: var(--theme-primary) !important;
            border-color: var(--theme-primary) !important;
            color: #fff !important;
        }

        /* =====================================
           TABLE HEADER THEME (SEMUA MENU INDEX - CLEAN LIGHT)
        ===================================== */
        .table thead,
        .table thead th,
        .table thead.table-primary th,
        .table thead.table-light th,
        .table:not(.table-dark) thead:not(.table-dark) th,
        table.dataTable thead th {
            background: #f8fafc !important;
            color: #475569 !important;
            border-bottom: 2px solid var(--theme-primary) !important;
            border-top: none !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            font-size: 0.75rem !important;
            letter-spacing: 0.05em !important;
            padding-top: 12px !important;
            padding-bottom: 12px !important;
        }

        .dark-style .table thead,
        .dark-style .table thead th,
        .dark-style .table thead.table-primary th,
        .dark-style .table thead.table-light th,
        .dark-style .table:not(.table-dark) thead:not(.table-dark) th,
        .dark-style table.dataTable thead th {
            background: #232333 !important;
            color: #cbd5e1 !important;
            border-bottom: 2px solid var(--theme-primary) !important;
        }

        /* =====================================
           BADGES & AVATARS DENGAN WARNA TEMA
        ===================================== */
        .bg-label-primary {
            background-color: rgba(var(--theme-primary-rgb), 0.10) !important;
            color: var(--theme-primary) !important;
        }

        .avatar.bg-label-primary,
        .avatar-initial.bg-label-primary {
            background-color: rgba(var(--theme-primary-rgb), 0.12) !important;
            color: var(--theme-primary) !important;
        }

        .badge.bg-primary {
            background: var(--theme-primary) !important;
            background-color: var(--theme-primary) !important;
            color: #fff !important;
        }

        .badge.company-theme-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 0.38rem 0.65rem;
            font-weight: 600;
            font-size: 0.75rem;
            border-radius: 6px;
            letter-spacing: 0.3px;
        }

        /* =====================================
           TITLES & TEXT (BERSIH & ELEGAN)
        ===================================== */
        .card-title,
        .page-title,
        h4.card-title,
        h5.card-title {
            color: #1e293b !important;
            font-weight: 700 !important;
        }

        .dark-style .card-title,
        .dark-style .page-title,
        .dark-style h1,
        .dark-style h2,
        .dark-style h3,
        .dark-style h4,
        .dark-style h5,
        .dark-style h6,
        .dark-style h4.card-title,
        .dark-style h5.card-title {
            color: #f1f5f9 !important;
        }

        .text-primary {
            color: var(--theme-primary) !important;
        }

        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, .05);
        }

        /* =====================================
           PAGINATION & INPUTS
        ===================================== */
        .page-item.active .page-link {
            background: var(--theme-primary) !important;
            background-color: var(--theme-primary) !important;
            border-color: var(--theme-primary) !important;
            color: #fff !important;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--theme-primary) !important;
            box-shadow: 0 0 0 .2rem rgba(var(--theme-primary-rgb), 0.15) !important;
        }

        /* =====================================
           BG-PRIMARY & CARD BG-PRIMARY THEME
        ===================================== */
        .bg-primary,
        .card.bg-primary,
        div.bg-primary {
            background: var(--theme-primary) !important;
            background-color: var(--theme-primary) !important;
            color: #ffffff !important;
        }

        .border-primary {
            border-color: var(--theme-primary) !important;
        }

        /* =====================================
           NAV PILLS (TABS HARI JADWAL & MENU LAIN)
        ===================================== */
        .nav-pills .nav-link.active,
        .nav-pills .nav-link.active:hover,
        .nav-pills .nav-link.active:focus,
        .nav-pills .show > .nav-link {
            background: var(--theme-primary) !important;
            background-color: var(--theme-primary) !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(var(--theme-primary-rgb), 0.25) !important;
        }

        .nav-pills .nav-link:not(.active):hover {
            color: var(--theme-primary) !important;
        }

        /* =====================================
           ALERT PRIMARY (BANNER INFO & NOTIFIKASI)
        ===================================== */
        .alert-primary {
            background-color: rgba(var(--theme-primary-rgb), 0.10) !important;
            border: 1px solid rgba(var(--theme-primary-rgb), 0.22) !important;
            color: var(--theme-primary) !important;
        }

        .alert-primary i,
        .alert-primary strong {
            color: var(--theme-primary) !important;
        }

        /* =====================================
           PROGRESS BAR THEME
        ===================================== */
        .progress-bar.bg-primary,
        .progress-bar:not(.bg-success):not(.bg-danger):not(.bg-warning):not(.bg-info) {
            background: var(--theme-primary) !important;
            background-color: var(--theme-primary) !important;
        }

        /* =====================================
           FORM SWITCHES & CHECKBOXES
        ===================================== */
        .form-check-input:checked,
        .form-switch .form-check-input:checked {
            background-color: var(--theme-primary) !important;
            border-color: var(--theme-primary) !important;
            box-shadow: 0 2px 6px rgba(var(--theme-primary-rgb), 0.3) !important;
        }

        /* =====================================
           SWEETALERT2 CONFIRM BUTTON
        ===================================== */
        .swal2-styled.swal2-confirm:not(.swal2-danger-btn):not([style*="background-color: rgb(220, 53, 69)"]):not([style*="background-color: rgb(25, 135, 84)"]) {
            background-color: var(--theme-primary) !important;
            border-color: var(--theme-primary) !important;
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
                    <footer class="app-footer mt-auto">
                        <div class="footer-container">
                            <div class="footer-left">
                                © {{ date('Y') }} IT Developer Sembilan Group
                            </div>

                            <div class="footer-right">
                                Monitoring Asset System
                            </div>
                        </div>
                    </footer>

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

    {{-- NOTIFIKASI REAL-TIME E-TICKET IT --}}
    @include('layouts.sections.menu.ticket-notifier')

    @stack('scripts')
@endsection
