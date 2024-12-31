
    <style>
        /* Wrapper logo di sidebar */
.app-brand-logo-wrapper {
    display: flex;
    justify-content: center; /* Pusatkan secara horizontal */
    align-items: center;    /* Pusatkan secara vertikal */
    width: 100%;            /* Sesuaikan lebar dengan kontainer */
    height: 100%;           /* Biarkan tinggi sesuai dengan kontainer */
    overflow: hidden;       /* Hindari elemen keluar kontainer */
    padding: 0;             /* Hilangkan padding jika perlu */
    box-sizing: border-box; /* Perbaiki hitungan dimensi */
}

/* Gambar logo */
.logo-posyandu {
    max-width: 100%;         /* Sesuaikan lebar logo dengan kontainer */
    max-height: 100%;        /* Sesuaikan tinggi logo dengan kontainer */
    object-fit: contain;     /* Jaga proporsi asli logo */
    margin: 0;               /* Hindari margin tambahan */
}

/* Sidebar layout */
aside.layout-menu {
    width: 267px;            /* Lebar sidebar */
    background-color: #ffffff; /* Tambahkan warna putih jika perlu */
    min-height: 100vh;       /* Tinggi penuh viewport */
    overflow-y: auto;        /* Gulir jika konten terlalu panjang */
}

/* Responsif untuk perangkat kecil */
@media (max-width: 768px) {
    .app-brand-logo-wrapper {
        height: 80px;       /* Sesuaikan tinggi untuk layar kecil */
    }
    .logo-posyandu {
        max-height: 80px;   /* Batasi tinggi maksimum logo */
        max-width: 80%;     /* Batasi lebar logo */
    }
}

    </style>
    
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">

    <!-- Logo Posyandu -->
    <div class="app-brand demo">
        <a href="{{ url('/') }}" class="app-brand-link">
            <div class="app-brand-logo-wrapper">
                <img src="{{ asset('assets/img/Landing/logoposyandu.png') }}" alt="Posyandu Logo" class="logo-posyandu">
            </div>
              

        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @foreach ($menuData[0]->menu as $menu)
            @if (in_array(Auth::user()->role, $menu->roles))
                @php
                    $activeClass = null;
                    $isMenuActive = false;
                    $currentRouteName = Route::currentRouteName();

                    if ($currentRouteName === $menu->slug) {
                        $activeClass = 'active';
                        $isMenuActive = true;
                    } elseif (isset($menu->submenu)) {
                        foreach ($menu->submenu as $submenu) {
                            if (isset($submenu->slug)) {
                                if ($currentRouteName === $submenu->slug) {
                                    $isMenuActive = true;
                                    break;
                                }
                            }
                        }
                        if ($isMenuActive) {
                            $activeClass = 'active open';
                        }
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
