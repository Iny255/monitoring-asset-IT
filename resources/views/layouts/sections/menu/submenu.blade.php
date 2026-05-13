<ul class="menu-sub">

    @if (isset($menu))

        @foreach ($menu as $submenu)
            {{-- =====================================
                 FILTER ROLE
            ===================================== --}}
            @if (!isset($submenu->roles) || in_array(Auth::user()->role, $submenu->roles))
                @php

                    $currentRoute = request()->route() ? request()->route()->getName() : null;

                    $currentUrl = request()->path();

                    $isActive = false;

                    // =====================================
                    // ACTIVE ROUTE
                    // =====================================
                    if (!empty($submenu->slug) && $currentRoute && str_starts_with($currentRoute, $submenu->slug)) {
                        $isActive = true;
                    }

                    // =====================================
                    // ACTIVE URL
                    // =====================================
                    elseif (isset($submenu->url) && str_starts_with($currentUrl, trim($submenu->url, '/'))) {
                        $isActive = true;
                    }

                @endphp


                {{-- =====================================
                     SUBMENU ITEM
                ===================================== --}}
                <li class="menu-item {{ $isActive ? 'active' : '' }}">

                    <a href="{{ isset($submenu->url) ? url($submenu->url) : 'javascript:void(0)' }}"
                        class="{{ isset($submenu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}

                       {{ $isActive ? 'active-submenu' : '' }}"
                        @if (isset($submenu->target) && !empty($submenu->target)) target="_blank" @endif>


                        {{-- ICON --}}
                        @if (isset($submenu->icon))
                            <i class="{{ $submenu->icon }}"></i>
                        @endif


                        {{-- TITLE --}}
                        <div>

                            {{ isset($submenu->name) ? __($submenu->name) : '' }}

                        </div>


                        {{-- BADGE --}}
                        @isset($submenu->badge)
                            <div class="badge bg-{{ $submenu->badge[0] }} rounded-pill ms-auto">

                                {{ $submenu->badge[1] }}

                            </div>
                        @endisset

                    </a>


                    {{-- =====================================
                         RECURSIVE SUBMENU
                    ===================================== --}}
                    @if (isset($submenu->submenu))
                        @include('layouts.sections.menu.submenu', ['menu' => $submenu->submenu])
                    @endif

                </li>
            @endif
        @endforeach

    @endif

</ul>
