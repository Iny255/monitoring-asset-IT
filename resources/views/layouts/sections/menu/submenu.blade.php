<ul class="menu-sub">
    @if (isset($menu))
        @foreach ($menu as $submenu)

            {{-- ✅ FILTER ROLE DI SINI --}}
            @if (!isset($submenu->roles) || in_array(Auth::user()->role, $submenu->roles))

                @php
                    $currentRoute = request()->route() ? request()->route()->getName() : null;
                    $currentUrl = request()->path();

                    $isActive = false;

                    if (!empty($submenu->slug) && $currentRoute && str_starts_with($currentRoute, $submenu->slug)) {
                        $isActive = true;
                    } elseif (isset($submenu->url) && str_starts_with($currentUrl, trim($submenu->url, '/'))) {
                        $isActive = true;
                    }
                @endphp

                <li class="menu-item {{ $isActive ? 'active' : '' }}">
                    <a href="{{ isset($submenu->url) ? url($submenu->url) : 'javascript:void(0)' }}"
                        class="{{ isset($submenu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
                        @if (isset($submenu->target) && !empty($submenu->target)) target="_blank" @endif>

                        @if (isset($submenu->icon))
                            <i class="{{ $submenu->icon }}"></i>
                        @endif

                        <div>{{ isset($submenu->name) ? __($submenu->name) : '' }}</div>

                        @isset($submenu->badge)
                            <div class="badge bg-{{ $submenu->badge[0] }} rounded-pill ms-auto">
                                {{ $submenu->badge[1] }}
                            </div>
                        @endisset
                    </a>

                    {{-- 🔁 recursive submenu --}}
                    @if (isset($submenu->submenu))
                        @include('layouts.sections.menu.submenu', ['menu' => $submenu->submenu])
                    @endif
                </li>

            @endif

        @endforeach
    @endif
</ul>