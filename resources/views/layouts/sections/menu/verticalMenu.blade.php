@php

    // =====================================
    // THEME GLOBAL
    // dari AppServiceProvider
    // =====================================

    $menuPrimary = $theme['primary_color'] ?? '#0b2f57';

    $menuSecondary = $theme['secondary_color'] ?? '#154b87';

    $menuLogo = $theme['logo'] ?? asset('assets/img/logo_sembilan.png');

@endphp


<style>
    /* =====================================
       ROOT COLOR
    ===================================== */
    :root {

        --menu-primary: {{ $menuPrimary }};

        --menu-secondary: {{ $menuSecondary }};

    }


    /* =====================================
       SIDEBAR
    ===================================== */
    .layout-menu {

        background: #ffffff !important;

        border-right: 1px solid #e9ecef;

        box-shadow: 0 0 15px rgba(0, 0, 0, .03);

    }


    /* =====================================
       BRAND AREA
    ===================================== */
    .layout-menu .app-brand {

        height: 110px !important;

        display: flex;

        align-items: center;

        justify-content: center;

        padding: 18px 0;

        background: transparent !important;

        border-bottom: 1px solid rgba(0, 0, 0, .05);

    }


    .layout-menu .app-brand-logo-wrapper {

        width: 100%;

        display: flex;

        justify-content: center;

        align-items: center;

        background: transparent !important;

        box-shadow: none !important;

        padding: 0;

    }


    /* =====================================
       LOGO
    ===================================== */
    .layout-menu .logo-sembilan {

        width: 250px;

        height: auto;

        object-fit: contain;

        border: none !important;

        outline: none !important;

        background: transparent !important;

        box-shadow: none !important;

        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, .04));

        transition: .25s ease;

    }


    .layout-menu .logo-sembilan:hover {

        transform: scale(1.02);

    }


    /* =====================================
       MENU SPACE
    ===================================== */
    .layout-menu .menu-inner {

        margin-top: 14px !important;

        padding-left: 10px;

        padding-right: 10px;

    }


    /* =====================================
       MENU ITEM
    ===================================== */
    .layout-menu .menu-item {

        margin-bottom: 8px;

    }


    .layout-menu .menu-link {

        border-radius: 14px !important;

        color: #566a7f !important;

        transition: .25s ease;

        font-weight: 600;

        font-size: 28px;
        /* 🔥 TAMBAH */

        padding-top: 16px;
        /* 🔥 TAMBAH */

        padding-bottom: 16px;
        /* 🔥 TAMBAH */

    }


    .layout-menu .menu-link i {

        color: #697a8d !important;
        font-size: 30px;
        /* 🔥 TAMBAH */

        margin-right: 20px;
        /* 🔥 TAMBAH */

    }

    .layout-menu .menu-sub .menu-link i {

        margin-right: 14px !important;

        font-size: 20px !important;

    }


    /* =====================================
       HOVER MENU
    ===================================== */
    .layout-menu .menu-link:hover {

        background: rgba(0, 0, 0, .04) !important;

        transform: translateX(3px);

        color: {{ $menuPrimary }} !important;

    }


    .layout-menu .menu-link:hover i {

        color: {{ $menuPrimary }} !important;

    }

    /* =====================================
   SUBMENU ACTIVE FINAL
===================================== */

    /* DEFAULT BULLET */
    .layout-menu .menu-sub .menu-link:not(.menu-toggle)::before {

        background-color: #b8c2cc !important;

        border: 2px solid #b8c2cc !important;

    }


    /* ACTIVE SUBMENU */
    .layout-menu .menu-sub .menu-item.active>.menu-link {

        background:
            linear-gradient(135deg,
                {{ $menuPrimary }},
                {{ $menuSecondary }}) !important;

        color: #ffffff !important;

        border-radius: 12px !important;

        box-shadow:
            0 4px 12px rgba(0, 0, 0, .10);

    }


    /* ACTIVE ICON */
    .layout-menu .menu-sub .menu-item.active>.menu-link i {

        color: #ffffff !important;

    }


    /* ACTIVE TEXT */
    .layout-menu .menu-sub .menu-item.active>.menu-link div {

        color: #ffffff !important;

    }


    /* ACTIVE BULLET */
    .layout-menu .menu-sub .menu-item.active>.menu-link:not(.menu-toggle)::before {

        background-color: #ffffff !important;

        border-color: #ffffff !important;

        box-shadow:
            0 0 0 4px rgba(255, 255, 255, .25) !important;

    }
</style>


<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">


    {{-- =====================================
         LOGO
    ===================================== --}}
    <div class="app-brand demo">

        <div class="app-brand-logo-wrapper">

            <img src="{{ $menuLogo }}" alt="Company Logo" class="logo-sembilan">

        </div>


        {{-- MOBILE TOGGLE --}}
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">

            <i class="bx bx-chevron-left bx-sm align-middle"></i>

        </a>

    </div>


    <div class="menu-inner-shadow"></div>


    {{-- =====================================
         MENU
    ===================================== --}}
    <ul class="menu-inner py-1">

        @foreach ($menuData->menu as $menu)
            @if (in_array(Auth::user()->role, $menu->roles))
                @php

                    $activeClass = '';

                    $isMenuActive = false;

                    $currentRoute = request()->route() ? request()->route()->getName() : null;

                    $currentUrl = request()->path();

                    // =====================================
                    // MAIN MENU ACTIVE
                    // =====================================
                    if (!empty($menu->slug) && $currentRoute && str_starts_with($currentRoute, $menu->slug)) {
                        $isMenuActive = true;
                    } elseif (isset($menu->url) && str_starts_with($currentUrl, trim($menu->url, '/'))) {
                        $isMenuActive = true;
                    }

                    // =====================================
                    // SUBMENU ACTIVE
                    // =====================================
                    if (isset($menu->submenu)) {
                        foreach ($menu->submenu as $submenu) {
                            if (
                                !empty($submenu->slug) &&
                                $currentRoute &&
                                str_starts_with($currentRoute, $submenu->slug)
                            ) {
                                $isMenuActive = true;

                                break;
                            }

                            if (isset($submenu->url) && str_starts_with($currentUrl, trim($submenu->url, '/'))) {
                                $isMenuActive = true;

                                break;
                            }
                        }
                    }

                    // =====================================
                    // ACTIVE CLASS
                    // =====================================
                    if ($isMenuActive) {
                        $activeClass = isset($menu->submenu) ? 'active open' : 'active';
                    }

                @endphp


                {{-- MENU ITEM --}}
                <li class="menu-item {{ $activeClass }}">

                    <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
                        class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
                        @if (isset($menu->target) && !empty($menu->target)) target="_blank" @endif>

                        {{-- ICON --}}
                        @if (isset($menu->icon))
                            <i class="{{ $menu->icon }}"></i>
                        @endif


                        {{-- TITLE --}}
                        <div>

                            {{ isset($menu->name) ? __($menu->name) : '' }}

                        </div>


                        {{-- BADGE --}}
                        @isset($menu->badge)
                            <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">

                                {{ $menu->badge[1] }}

                            </div>
                        @endisset

                    </a>


                    {{-- SUBMENU --}}
                    @isset($menu->submenu)
                        @include('layouts.sections.menu.submenu', ['menu' => $menu->submenu])
                    @endisset

                </li>
            @endif
        @endforeach

    </ul>

</aside>
