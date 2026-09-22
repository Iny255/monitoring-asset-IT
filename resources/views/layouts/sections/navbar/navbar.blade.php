@php

    $containerNav = $containerNav ?? 'container-fluid';
    $navbarDetached = $navbarDetached ?? '';

    // =====================================
    // THEME GLOBAL
    // dari AppServiceProvider
    // =====================================

    $primaryColor = !empty($theme['primary_color']) ? $theme['primary_color'] : '#0b2f57';
    if (in_array(strtolower(trim($primaryColor)), ['#fff', '#ffffff', 'white', '#f8fafc', '#f1f5f9'])) {
        $primaryColor = '#0b2f57';
    }

    $secondaryColor = !empty($theme['secondary_color']) ? $theme['secondary_color'] : '#154b87';

    $companyName = !empty($theme['company_name']) ? trim($theme['company_name']) : 'Monitoring Aset Divisi IT';
    if (empty($companyName)) {
        $companyName = 'Monitoring Aset Divisi IT';
    }

@endphp


<style>
    :root {
        --primary-theme: {{ $primaryColor }};
        --secondary-theme: {{ $secondaryColor }};
    }

    /* =====================================
       NAVBAR (TEMA PERUSAHAAN)
    ===================================== */
    .layout-navbar {
        background: linear-gradient(135deg, var(--primary-theme), var(--secondary-theme)) !important;
        border: none !important;
        border-radius: 16px;
        margin-top: 10px;
        min-height: 76px;
        height: 76px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
        padding-left: 20px;
        padding-right: 20px;
        display: flex !important;
        align-items: center !important;
    }

    /* =====================================
       TITLE (PUTIH BERSIH & TEGAS)
    ===================================== */
    .navbar-title,
    .layout-navbar .navbar-title,
    .layout-navbar .nav-item .navbar-title,
    .layout-navbar span.navbar-title {
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 22px !important;
        letter-spacing: .4px !important;
        line-height: 1.2 !important;
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    /* =====================================
       MENU ICON / HAMBURGER (PUTIH TERANG)
    ===================================== */
    .layout-navbar .bx-menu {
        color: #ffffff !important;
        font-size: 28px;
        transition: transform 0.2s ease, opacity 0.2s ease;
    }

    .layout-navbar .bx-menu:hover {
        opacity: 0.85;
        transform: scale(1.05);
    }

    /* =====================================
       DARK MODE BUTTON
    ===================================== */
    .dark-toggle-btn {
        border: none;
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff !important;
        font-size: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: .25s ease;
        padding: 8px;
        border-radius: 10px;
        cursor: pointer !important;
        user-select: none;
    }

    .dark-toggle-btn:hover {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff !important;
        transform: scale(1.05);
    }

    .dark-toggle-btn i {
        color: #ffffff !important;
    }

    /* =====================================
       USER AVATAR
    ===================================== */
    .navbar-user-avatar {
        width: 42px;
        height: 42px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 2px 8px rgba(0, 0, 0, .2);
    }

    /* =====================================
       DROPDOWN (MENU POPUP TETAP BERSIH)
    ===================================== */
    .dropdown-user .dropdown-menu {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .12);
        background: #ffffff !important;
    }

    .dropdown-user .dropdown-menu .dropdown-item {
        color: #334155 !important;
    }

    .dropdown-user .dropdown-menu .dropdown-item:hover {
        background: #f1f5f9 !important;
        color: var(--primary-theme, #0b2f57) !important;
    }

    .dropdown-user .dropdown-menu span,
    .dropdown-user .dropdown-menu small,
    .dropdown-user .dropdown-menu i {
        color: #334155 !important;
    }

    .dropdown-user .dropdown-menu small.text-muted {
        color: #64748b !important;
    }

    /* =====================================
       DARK STYLE COMPATIBILITY
    ===================================== */
    .dark-style .layout-navbar {
        background: #1e1f2f !important;
        border: 1px solid #32344d !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3) !important;
    }

    .dark-style .navbar-title {
        color: #f1f5f9 !important;
    }

    .dark-style .layout-navbar .bx-menu {
        color: #cbd5e1 !important;
    }

    .dark-style .dark-toggle-btn {
        background: rgba(255, 255, 255, 0.1) !important;
        color: #cbd5e1 !important;
    }

    .dark-style .dropdown-user .dropdown-menu {
        background: #2b2c40 !important;
        border-color: #444564 !important;
    }

    .dark-style .dropdown-user .dropdown-menu .dropdown-item,
    .dark-style .dropdown-user .dropdown-menu span,
    .dark-style .dropdown-user .dropdown-menu i {
        color: #cbd5e1 !important;
    }

    /* =====================================
       RESPONSIVE & Z-INDEX FIX
    ===================================== */
    @media (max-width: 1199.98px) {
        .layout-navbar,
        #layout-navbar {
            z-index: 1030 !important; /* Wajib di bawah sidebar offcanvas (1200) agar tidak menutupi menu */
        }
    }

    @media (max-width: 768px) {
        .layout-navbar {
            min-height: 56px !important;
            height: 56px !important;
            margin-top: 6px !important;
            border-radius: 12px !important;
            padding-left: 14px !important;
            padding-right: 14px !important;
        }

        .navbar-title {
            font-size: 16px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            max-width: 200px !important;
            display: inline-block !important;
            vertical-align: middle;
        }
    }

    @media (max-width: 420px) {
        .navbar-title {
            font-size: 14px !important;
            max-width: 140px !important;
        }
    }
</style>


{{-- =====================================
     NAVBAR
===================================== --}}

@if (isset($navbarDetached) && $navbarDetached == 'navbar-detached')
    <nav class="layout-navbar {{ $containerNav }} navbar navbar-expand-xl {{ $navbarDetached }} align-items-center"
        id="layout-navbar">
@endif


@if (isset($navbarDetached) && $navbarDetached == '')
    <nav class="layout-navbar navbar navbar-expand-xl align-items-center" id="layout-navbar">

        <div class="{{ $containerNav }}">
@endif


{{-- =====================================
     SIDEBAR TOGGLE
===================================== --}}
@if (!isset($navbarHideToggle))
    <div
        class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0
        {{ isset($menuHorizontal) ? ' d-xl-none ' : '' }}
        {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">

        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)" id="sidebarToggle">

            <i class="bx bx-menu bx-sm"></i>

        </a>

    </div>
@endif


{{-- =====================================
     NAVBAR CONTENT
===================================== --}}
<div class="navbar-nav-right d-flex align-items-center w-100" id="navbar-collapse">


    {{-- =====================================
         COMPANY TITLE
    ===================================== --}}
    <div class="navbar-nav align-items-center">

        <div class="nav-item d-flex align-items-center">

            <span class="navbar-title">

                {{ $companyName }}

            </span>

        </div>

    </div>


    {{-- =====================================
         RIGHT MENU
    ===================================== --}}
    <ul class="navbar-nav flex-row align-items-center ms-auto">


        {{-- DARK MODE --}}
        <li class="nav-item me-3">

            <button id="darkModeToggle" class="dark-toggle-btn" type="button" title="Ganti Mode Gelap / Terang" aria-label="Toggle theme">

                <i id="darkIcon" class="bx bx-moon"></i>

            </button>

        </li>


        {{-- =====================================
             USER
        ===================================== --}}
        <li class="nav-item navbar-dropdown dropdown-user dropdown">

            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">

                <div class="avatar avatar-online">

                    <img src="{{ asset('assets/img/person.png') }}" alt="User" class="navbar-user-avatar">

                </div>

            </a>


            {{-- DROPDOWN --}}
            <ul class="dropdown-menu dropdown-menu-end">

                <li>

                    <a class="dropdown-item" href="javascript:void(0);">

                        <div class="d-flex">

                            <div class="flex-shrink-0 me-3">

                                <div class="avatar avatar-online">

                                    <img src="{{ asset('assets/img/person.png') }}" alt="User"
                                        class="navbar-user-avatar">

                                </div>

                            </div>

                            <div class="flex-grow-1">

                                <span class="fw-semibold d-block">

                                    {{ auth()->user()->name }}

                                </span>

                                <small class="text-muted text-uppercase">

                                    {{ auth()->user()->role }}

                                </small>

                            </div>

                        </div>

                    </a>

                </li>


                <li>

                    <div class="dropdown-divider"></div>

                </li>


                {{-- LOGOUT --}}
                @auth

                    <li>

                        <a class="dropdown-item" href="/"
                            onclick="event.preventDefault(); document.getElementById('logout').submit();">

                            <i class='bx bx-power-off me-2'></i>

                            <span class="align-middle">

                                Log Out

                            </span>

                        </a>

                        <form id="logout" action="{{ route('logout') }}" method="POST" style="display:none;">

                            @csrf

                        </form>

                    </li>

                @endauth

            </ul>

        </li>

    </ul>

</div>


@if (!isset($navbarDetached))
    </div>
@endif

</nav>
