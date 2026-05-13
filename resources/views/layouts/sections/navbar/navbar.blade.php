@php

    $containerNav = $containerNav ?? 'container-fluid';
    $navbarDetached = $navbarDetached ?? '';

    // =====================================
    // THEME GLOBAL
    // dari AppServiceProvider
    // =====================================

    $primaryColor = $theme['primary_color'] ?? '#0b2f57';

    $secondaryColor = $theme['secondary_color'] ?? '#154b87';

    $companyName =  'Monitoring Aset Divisi IT';

@endphp


<style>
    :root {

        --primary-theme: {{ $primaryColor }};
        --secondary-theme: {{ $secondaryColor }};

    }


    /* =====================================
       NAVBAR
    ===================================== */
    .layout-navbar {

        background:
            linear-gradient(135deg,
                var(--primary-theme),
                var(--secondary-theme)) !important;

        border-radius: 16px;

        margin-top: 10px;

        min-height: 72px;

        box-shadow:
            0 4px 18px rgba(0, 0, 0, .10);

        border: none !important;

        padding-left: 14px;

        padding-right: 14px;

    }


    /* =====================================
       TITLE
    ===================================== */
    .navbar-title {

        color: white;

        font-weight: 700;

        font-size: 28px;

        letter-spacing: .3px;

        line-height: 1;

    }


    /* =====================================
       MENU ICON
    ===================================== */
    .layout-navbar .bx-menu {

        color: white !important;

        font-size: 28px;

    }


    /* =====================================
       DARK MODE BUTTON
    ===================================== */
    .dark-toggle-btn {

        border: none;

        background: transparent;

        color: white;

        font-size: 24px;

        display: flex;

        align-items: center;

        justify-content: center;

        transition: .25s ease;

    }


    .dark-toggle-btn:hover {

        transform: scale(1.08);

    }


    /* =====================================
       USER AVATAR
    ===================================== */
    .navbar-user-avatar {

        width: 42px;

        height: 42px;

        object-fit: cover;

        border-radius: 50%;

        border: 2px solid rgba(255, 255, 255, .35);

        box-shadow:
            0 2px 10px rgba(0, 0, 0, .15);

    }


    /* =====================================
       DROPDOWN
    ===================================== */
    .dropdown-user .dropdown-menu {

        border: none;

        border-radius: 14px;

        overflow: hidden;

        box-shadow:
            0 8px 25px rgba(0, 0, 0, .12);

    }


    /* =====================================
       RESPONSIVE
    ===================================== */
    @media(max-width:768px) {

        .navbar-title {

            font-size: 20px;

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

            <button id="darkModeToggle" class="dark-toggle-btn" type="button">

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
