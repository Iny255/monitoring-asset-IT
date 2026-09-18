@php
    // =====================================
    // THEME GLOBAL dari AppServiceProvider
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
       SIDEBAR CONTAINER (LEBAR PROPORSIONAL & LEGA)
    ===================================== */
    .layout-menu,
    #layout-menu {
        background: #ffffff !important;
        border-right: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04) !important;
        width: 300px !important;
        height: 100vh !important;
        max-height: 100vh !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        bottom: 0 !important;
        z-index: 1050 !important;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important; /* Wajib hidden agar flex child dapat menggulir / scroll */
    }

    @media (min-width: 1200px) {
        .layout-page {
            padding-left: 300px !important;
        }
        #layout-menu {
            transform: none !important;
        }
        .layout-navbar .layout-menu-toggle {
            display: none !important;
        }
    }

    /* =====================================
       RESPONSIVE MOBILE (< 1200px)
       Tombol hamburger navbar & drawer sidebar
    ===================================== */
    @media (max-width: 1199.98px) {
        .layout-page {
            padding-left: 0 !important;
        }

        /* Tombol Hamburger Navbar Mobile WAJIB Muncul & Jelas */
        .layout-navbar .layout-menu-toggle,
        #layout-navbar .layout-menu-toggle {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            width: auto !important;
            height: auto !important;
            pointer-events: auto !important;
            cursor: pointer !important;
            margin-right: 14px !important;
            align-items: center !important;
        }

        .layout-navbar .layout-menu-toggle a,
        #layout-navbar #sidebarToggle {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            visibility: visible !important;
            opacity: 1 !important;
            width: auto !important;
            height: auto !important;
            pointer-events: auto !important;
            cursor: pointer !important;
            padding: 6px 8px !important;
        }

        .layout-navbar .layout-menu-toggle i,
        #layout-navbar .bx-menu {
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
            color: #ffffff !important;
            font-size: 30px !important;
            line-height: 1 !important;
        }

        /* Sidebar Offcanvas pada layar Mobile */
        #layout-menu {
            transform: translate3d(-100%, 0, 0) !important;
            transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
            z-index: 1060 !important;
            box-shadow: none !important;
        }

        html.layout-menu-expanded #layout-menu {
            transform: translate3d(0, 0, 0) !important;
            box-shadow: 0 0 45px rgba(0, 0, 0, 0.35) !important;
        }

        /* Backdrop Overlay saat menu mobile terbuka */
        .layout-overlay {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            background: rgba(0, 0, 0, 0.5) !important;
            z-index: 1055 !important;
            display: none !important;
            cursor: pointer !important;
        }

        html.layout-menu-expanded .layout-overlay {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            pointer-events: auto !important;
        }
    }

    /* =====================================
       HAPUS KOTAK BIRU SEBELAH KANAN LOGO (DESKTOP TOGGLE PIN)
       (HANYA target di dalam #layout-menu, JANGAN sembunyikan navbar toggle!)
    ===================================== */
    #layout-menu .layout-menu-toggle,
    #layout-menu a.layout-menu-toggle,
    #layout-menu .app-brand .layout-menu-toggle {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        width: 0 !important;
        height: 0 !important;
        pointer-events: none !important;
    }

    /* Hapus semua pseudo element / kotak / strip biru di sebelah kanan menu */
    .layout-menu .menu-link::after,
    .layout-menu .menu-link::before,
    .layout-menu .menu-item::after,
    .layout-menu .menu-item::before,
    .layout-menu .menu-sub::after,
    .layout-menu .menu-sub::before {
        display: none !important;
        content: none !important;
        width: 0 !important;
        height: 0 !important;
        border: none !important;
        background: transparent !important;
    }

    /* Sembunyikan rel PerfectScrollbar yang berwarna */
    .ps__rail-x,
    .ps__rail-y,
    .ps__thumb-x,
    .ps__thumb-y {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
    }

    .menu-inner-shadow,
    .layout-menu .menu-inner-shadow {
        display: none !important;
        height: 0 !important;
    }

    /* =====================================
       BRAND LOGO AREA (UKURAN BESAR & JELAS)
    ===================================== */
    .layout-menu .app-brand {
        flex: 0 0 150px !important;
        height: 150px !important;
        min-height: 150px !important;
        max-height: 150px !important;
        width: 100% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 12px 18px !important;
        background: #ffffff !important;
        border-bottom: 1px solid #f1f5f9 !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
    }

    .layout-menu .app-brand-logo-wrapper {
        width: 100% !important;
        height: 100% !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        text-decoration: none !important;
    }

    .layout-menu .logo-sembilan {
        max-width: 260px !important;
        max-height: 125px !important;
        width: auto !important;
        height: auto !important;
        object-fit: contain !important;
        display: block !important;
        transition: transform 0.2s ease;
    }

    .layout-menu .logo-sembilan:hover {
        transform: scale(1.02);
    }

    /* =====================================
       SCROLLABLE MENU CONTAINER (BISA DIGULIR DENGAN MOUSE)
    ===================================== */
    .layout-menu .menu-inner,
    #sidebarMenuInner {
        flex: 1 1 0% !important;
        min-height: 0 !important; /* KUNCI UTAMA flexbox agar bisa menggulir */
        height: calc(100vh - 150px) !important;
        max-height: calc(100vh - 150px) !important;
        width: 100% !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch !important; /* WAJIB STRETCH agar lebar Dashboard & semua menu melebar penuh 100% ke kanan */
        overflow-y: auto !important;
        overflow-x: hidden !important;
        scroll-behavior: auto !important; /* Respon instan tanpa lag animasi penahanan */
        -webkit-overflow-scrolling: touch !important;
        overscroll-behavior-y: contain !important;
        padding: 16px 14px 120px 14px !important;
        margin: 0 !important;
        box-sizing: border-box !important;
        scrollbar-width: thin !important;
        scrollbar-color: #cbd5e1 transparent !important;
    }

    /* Custom Modern Slim Scrollbar */
    .layout-menu .menu-inner::-webkit-scrollbar,
    #sidebarMenuInner::-webkit-scrollbar {
        width: 6px !important;
    }

    .layout-menu .menu-inner::-webkit-scrollbar-track,
    #sidebarMenuInner::-webkit-scrollbar-track {
        background: transparent !important;
    }

    .layout-menu .menu-inner::-webkit-scrollbar-thumb,
    #sidebarMenuInner::-webkit-scrollbar-thumb {
        background: #cbd5e1 !important;
        border-radius: 10px !important;
    }

    .layout-menu .menu-inner::-webkit-scrollbar-thumb:hover,
    #sidebarMenuInner::-webkit-scrollbar-thumb:hover {
        background: #94a3b8 !important;
    }

    /* =====================================
       GROUP / SECTION HEADERS & SINGLE MENU LINKS
       (TAMPILAN 100% SERAGAM & HARMONIS)
    ===================================== */
    .layout-menu .menu-header-group {
        margin-top: 10px;
        margin-bottom: 0;
        list-style: none;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    .layout-menu .menu-header-group:first-child {
        margin-top: 0 !important;
    }

    /* Jarak antar menu single berurutan yang kompak & proporsional */
    .layout-menu .menu-header-group.menu-single-item + .menu-header-group.menu-single-item {
        margin-top: 6px !important;
    }

    .layout-menu .menu-group-title {
        display: flex !important;
        align-items: center !important;
        width: 100% !important;
        padding: 10px 16px !important;
        border-radius: 10px !important;
        background: rgba(11, 47, 87, 0.06) !important;
        color: var(--menu-primary, #0b2f57) !important;
        font-size: 15.5px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.6px !important;
        border-left: 4px solid var(--menu-primary, #0b2f57) !important;
        box-sizing: border-box !important;
        user-select: none;
    }

    .layout-menu .menu-group-title i {
        font-size: 22px;
        margin-right: 12px;
        color: var(--menu-primary, #0b2f57);
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    /* Tautan Menu Tunggal tanpa submenu (Dashboard, History Tracking Device, Users, Aset Saya) */
    .layout-menu a.menu-single-link {
        text-decoration: none !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        display: flex !important;
        align-items: center !important;
        width: 100% !important;
        color: var(--menu-primary, #0b2f57) !important;
        box-sizing: border-box !important;
    }

    .layout-menu a.menu-single-link:hover {
        background: rgba(11, 47, 87, 0.12) !important;
        color: var(--menu-primary, #0b2f57) !important;
        border-left: 4px solid var(--menu-primary, #0b2f57) !important;
        transform: translateX(4px);
    }

    .layout-menu a.menu-single-link.active {
        background: linear-gradient(135deg, var(--menu-primary, #0b2f57), var(--menu-secondary, #154b87)) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(11, 47, 87, 0.25) !important;
        border-left: 4px solid #ffffff !important;
    }

    .layout-menu a.menu-single-link.active i,
    .layout-menu a.menu-single-link.active span {
        color: #ffffff !important;
    }

    /* =====================================
       SUBMENU CONTAINER (SELALU MUNCUL & SEJAJAR RATA KIRI)
    ===================================== */
    .layout-menu .menu-sub {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        height: auto !important;
        width: 100% !important;
        box-sizing: border-box !important;
        padding: 0 !important;
        margin: 4px 0 !important; /* Rata kiri tanpa indentasi & tanpa garis abu */
        border-left: none !important; /* Hapus garis abu vertikal agar sejajar lurus dengan Dashboard */
        list-style: none;
    }

    /* =====================================
       MENU LINKS (UKURAN LEGA & SEJAJAR DENGAN DASHBOARD)
    ===================================== */
    .layout-menu .menu-item {
        margin-bottom: 4px;
        list-style: none;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    /* Hapus margin bawah pada item terakhir submenu */
    .layout-menu .menu-sub .menu-item:last-child {
        margin-bottom: 0 !important;
    }

    /* Link Submenu (Di bawah grup) */
    .layout-menu .menu-link,
    .layout-menu .menu-item > .menu-link,
    .layout-menu .menu-sub .menu-item > .menu-link {
        border-radius: 10px !important;
        font-size: 16px !important;
        font-weight: 500 !important;
        padding: 10px 16px !important;
        width: 100% !important;
        color: #334155 !important;
        transition: all 0.2s ease !important;
        display: flex !important;
        align-items: center !important;
        text-decoration: none !important;
        border-left: 4px solid transparent !important; /* Lebar & posisi sejajar sempurna dengan Dashboard */
        box-sizing: border-box !important;
    }

    .layout-menu .menu-link i,
    .layout-menu .menu-item > .menu-link i,
    .layout-menu .menu-sub .menu-item > .menu-link i {
        font-size: 20px !important;
        margin-right: 12px !important;
        color: #64748b !important;
        transition: all 0.2s ease !important;
        flex-shrink: 0;
    }

    .layout-menu .menu-link div {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Hover State */
    .layout-menu .menu-link:hover,
    .layout-menu .menu-sub .menu-item > .menu-link:hover {
        background: rgba(11, 47, 87, 0.08) !important;
        color: var(--menu-primary, #0b2f57) !important;
        border-left: 4px solid var(--menu-primary, #0b2f57) !important;
        transform: translateX(4px);
    }

    .layout-menu .menu-link:hover i,
    .layout-menu .menu-sub .menu-item > .menu-link:hover i {
        color: var(--menu-primary, #0b2f57) !important;
    }

    /* Active Link State */
    .layout-menu .menu-item.active > .menu-link,
    .layout-menu .menu-sub .menu-item.active > .menu-link {
        background: linear-gradient(135deg, var(--menu-primary, #0b2f57), var(--menu-secondary, #154b87)) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        box-shadow: 0 4px 14px rgba(11, 47, 87, 0.25) !important;
        border-left: 4px solid #ffffff !important;
    }

    .layout-menu .menu-item.active > .menu-link i,
    .layout-menu .menu-sub .menu-item.active > .menu-link i {
        color: #ffffff !important;
    }

    .layout-menu .menu-item.active > .menu-link div,
    .layout-menu .menu-sub .menu-item.active > .menu-link div {
        color: #ffffff !important;
    }
</style>

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    {{-- =====================================
         BRAND LOGO (UKURAN BESAR, JELAS, PROPORSIONAL)
    ===================================== --}}
    <div class="app-brand demo">
        <a href="{{ url('/dashboard') }}" class="app-brand-logo-wrapper">
            <img src="{{ $menuLogo }}" alt="Company Logo" class="logo-sembilan" onerror="this.onerror=null; this.src='{{ asset('assets/img/logo_sembilan.png') }}';">
        </a>
    </div>

    {{-- =====================================
         SCROLLABLE MENU LIST (SEMUA TERBUKA)
    ===================================== --}}
    <ul class="menu-inner" id="sidebarMenuInner">

        @php
            $authUser = Auth::user();
            $authRawRole = (string) ($authUser->role ?? '');
            $authUserRole = is_numeric($authRawRole) ? ($authUser->roleDefinition?->name ?? $authRawRole) : $authRawRole;
            $authNormRole = strtolower(str_replace([' ', '-'], '_', trim($authUserRole)));
            $isSuperAdmin = in_array($authNormRole, ['super_admin', '1', 'superadmin'])
                || in_array($authRawRole, ['super_admin', '1', 1, 'superadmin'])
                || ($authUser->roleDefinition && $authUser->roleDefinition->name === 'super_admin');
        @endphp

        @foreach ($menuData->menu as $menu)
            @if ($isSuperAdmin || (isset($menu->roles) && (in_array($authUser->role, $menu->roles) || in_array($authUserRole, $menu->roles))))
                @php
                    $currentRoute = request()->route() ? request()->route()->getName() : null;
                    $currentUrl = request()->path();

                    $isMenuActive = false;

                    // Cek aktif main link
                    if (!empty($menu->slug) && $currentRoute && str_starts_with($currentRoute, $menu->slug)) {
                        $isMenuActive = true;
                    } elseif (isset($menu->url) && str_starts_with($currentUrl, trim($menu->url, '/'))) {
                        $isMenuActive = true;
                    }

                    $hasSubmenu = isset($menu->submenu) && count($menu->submenu) > 0;
                @endphp

                @if ($hasSubmenu)
                    {{-- =====================================
                         GROUP SECTION HEADER (DENGAN SUBMENU)
                    ===================================== --}}
                    <li class="menu-header-group">
                        <div class="menu-group-title">
                            @if (isset($menu->icon))
                                <i class="{{ $menu->icon }}"></i>
                            @endif
                            <span>{{ isset($menu->name) ? __($menu->name) : '' }}</span>
                        </div>

                        {{-- SUBMENU ITEMS (SELALU TERBUKA) --}}
                        @include('layouts.sections.menu.submenu', ['menu' => $menu->submenu])
                    </li>
                @else
                    {{-- =====================================
                         SINGLE MENU LINK (TANPA SUBMENU)
                         TAMPILAN DISAMAKAN PERSIS SEPERTI HEADER MENU LAIN
                    ===================================== --}}
                    <li class="menu-header-group menu-single-item">
                        <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
                           class="menu-group-title menu-single-link {{ $isMenuActive ? 'active' : '' }}"
                           @if (isset($menu->target) && !empty($menu->target)) target="_blank" @endif>

                            @if (isset($menu->icon))
                                <i class="{{ $menu->icon }}"></i>
                            @endif

                            <span>{{ isset($menu->name) ? __($menu->name) : '' }}</span>

                            @isset($menu->badge)
                                <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">
                                    {{ $menu->badge[1] }}
                                </div>
                            @endisset
                        </a>
                    </li>
                @endif
            @endif
        @endforeach

    </ul>

</aside>

{{-- SCRIPT PENJAMIN GULIR MOUSE (MOUSE WHEEL SCROLL) --}}
<script>
    (function() {
        // Matikan PerfectScrollbar lebih awal
        window.PerfectScrollbar = function() {
            this.destroy = function() {};
            this.update = function() {};
        };
        if (window.Menu && window.Menu.prototype) {
            window.Menu.prototype.manageScroll = function() {};
        }

        function setupSidebarScroll() {
            var layoutMenu = document.getElementById('layout-menu');
            var menuInner = document.getElementById('sidebarMenuInner') || (layoutMenu ? layoutMenu.querySelector('.menu-inner') : null);
            if (!menuInner) return;

            // Hancurkan instance PerfectScrollbar jika sempat dibuat oleh template
            if (window.Helpers) {
                if (window.Helpers.menuPsScroll && typeof window.Helpers.menuPsScroll.destroy === 'function') {
                    try { window.Helpers.menuPsScroll.destroy(); } catch(e) {}
                    window.Helpers.menuPsScroll = null;
                }
                if (window.Helpers.mainMenu && window.Helpers.mainMenu._scrollbar && typeof window.Helpers.mainMenu._scrollbar.destroy === 'function') {
                    try { window.Helpers.mainMenu._scrollbar.destroy(); } catch(e) {}
                    window.Helpers.mainMenu._scrollbar = null;
                }
            }
            if (menuInner._ps && typeof menuInner._ps.destroy === 'function') {
                try { menuInner._ps.destroy(); } catch(e) {}
                menuInner._ps = null;
            }
            menuInner.classList.remove('ps', 'ps--active-y', 'ps--active-x');
            var rails = menuInner.querySelectorAll('.ps__rail-x, .ps__rail-y');
            rails.forEach(function(r) { r.remove(); });

            // Handler scroll roda mouse yang ringan, enteng & responsif
            function onWheel(e) {
                var delta = e.deltaY;
                if (e.deltaMode === 1) { // Line mode (seperti default Firefox)
                    delta *= 45;
                } else if (e.deltaMode === 2) { // Page mode
                    delta *= 120;
                } else {
                    // Pixel mode (Windows Chrome, Edge, mouse standar)
                    // Kalikan 1.6x agar putaran roda mouse terasa ringan, cepat & tidak kaku
                    delta *= 1.6;
                }
                menuInner.scrollTop += delta;
                e.preventDefault();
                e.stopPropagation(); // Cegah eksekusi ganda saat event bubbling
            }

            if (!menuInner._wheelBound) {
                menuInner.addEventListener('wheel', onWheel, { passive: false });
                menuInner._wheelBound = true;
            }

            if (layoutMenu && !layoutMenu._wheelBound) {
                layoutMenu.addEventListener('wheel', onWheel, { passive: false });
                layoutMenu._wheelBound = true;
            }
        }

        // Penjamin event toggle menu navbar pada perangkat mobile
        document.addEventListener('click', function(e) {
            var toggleBtn = e.target.closest('#sidebarToggle, .layout-navbar .layout-menu-toggle');
            if (toggleBtn) {
                e.preventDefault();
                e.stopPropagation();
                if (window.Helpers && typeof window.Helpers.toggleCollapsed === 'function') {
                    window.Helpers.toggleCollapsed();
                } else {
                    document.documentElement.classList.toggle('layout-menu-expanded');
                }
                return;
            }

            var overlay = e.target.closest('.layout-overlay');
            if (overlay) {
                e.preventDefault();
                if (window.Helpers && typeof window.Helpers.setCollapsed === 'function') {
                    window.Helpers.setCollapsed(true);
                } else {
                    document.documentElement.classList.remove('layout-menu-expanded');
                }
            }
        });

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupSidebarScroll);
        } else {
            setupSidebarScroll();
        }
        window.addEventListener('load', setupSidebarScroll);
        setTimeout(setupSidebarScroll, 50);
        setTimeout(setupSidebarScroll, 250);
        setTimeout(setupSidebarScroll, 700);
        setTimeout(setupSidebarScroll, 1500);
    })();
</script>
