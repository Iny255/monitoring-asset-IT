<style>
    /* ===== PAKSA AREA LOGO LEBIH TINGGI ===== */
    .layout-menu .app-brand {
        height: 100px !important;
        /* override tinggi default 64px */
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px 0;
    }

    .layout-menu .app-brand-logo-wrapper {
        width: 100%;
        text-align: center;
    }

    /* Logo diperbesar tapi aman */
    .layout-menu .logo-sembilan {
        width: 255px;
        height: auto;
        object-fit: contain;
    }

    /* ===== TURUNKAN MENU ===== */
    .layout-menu .menu-inner {
        margin-top: 13px !important;
    }
</style>
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">

    <!-- Logo 9 -->
    <div class="app-brand demo">
        <!-- <a href="{{ url('/') }}" class="app-brand-link"> -->
        <div class="app-brand-logo-wrapper">
            <img src="{{ asset('assets/img/logo_sembilan.png') }}" alt="Sembilan Logo" class="logo-sembilan" width=120>
        </div>


        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
      @foreach ($menuData->menu as $menu)
            @if (in_array(Auth::user()->role, $menu->roles))
                @php
                    $activeClass = '';
                    $isMenuActive = false;

                    $currentRoute = request()->route() ? request()->route()->getName() : null;
                    $currentUrl = request()->path();

                    // ==== CEK MENU UTAMA ====
                    if (!empty($menu->slug) && $currentRoute && str_starts_with($currentRoute, $menu->slug)) {
                        $isMenuActive = true;
                    } elseif (isset($menu->url) && str_starts_with($currentUrl, trim($menu->url, '/'))) {
                        $isMenuActive = true;
                    }

                    // ==== CEK SUBMENU ====
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

                    if ($isMenuActive) {
                        $activeClass = isset($menu->submenu) ? 'active open' : 'active';
                    }
                @endphp


                <li class="menu-item {{ $activeClass }}">
                    <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
                        class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
                        @if (isset($menu->target) && !empty($menu->target)) target="_blank" @endif>
                        @if (isset($menu->icon))
                            <i class="{{ $menu->icon }}"></i>
                        @endif
                        <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
                        @isset($menu->badge)
                            <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
                        @endisset
                    </a>

                    {{-- Render submenu if exists --}}
                    @isset($menu->submenu)
                        @include('layouts.sections.menu.submenu', ['menu' => $menu->submenu])
                    @endisset
                </li>
            @endif
        @endforeach
    </ul>
</aside>
