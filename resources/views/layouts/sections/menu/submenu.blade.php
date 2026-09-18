<ul class="menu-sub">

    @php
        $authUser = Auth::user();
        $authRawRole = (string) ($authUser->role ?? '');
        $authUserRole = is_numeric($authRawRole) ? ($authUser->roleDefinition?->name ?? $authRawRole) : $authRawRole;
        $authNormRole = strtolower(str_replace([' ', '-'], '_', trim($authUserRole)));
        $isSuperAdmin = in_array($authNormRole, ['super_admin', '1', 'superadmin'])
            || in_array($authRawRole, ['super_admin', '1', 1, 'superadmin'])
            || ($authUser->roleDefinition && $authUser->roleDefinition->name === 'super_admin');
    @endphp

    @if (isset($menu))
        @foreach ($menu as $submenu)
            {{-- FILTER ROLE --}}
            @if ($isSuperAdmin || !isset($submenu->roles) || in_array($authUser->role, $submenu->roles) || in_array($authUserRole, $submenu->roles))
                @php
                    $currentRoute = request()->route()?->getName();
                    $currentUrl = request()->path();
                    $isActive = false;

                    // Active berdasarkan URL
                    if (isset($submenu->url) && str_starts_with($currentUrl, trim($submenu->url, '/'))) {
                        $isActive = true;
                    }

                    // Active berdasarkan Slug
                    if (!$isActive && !empty($submenu->slug) && $currentRoute && str_starts_with($currentRoute, $submenu->slug)) {
                        $isActive = true;
                    }

                    // Active berdasarkan Child Submenu
                    if (!$isActive && isset($submenu->submenu)) {
                        foreach ($submenu->submenu as $child) {
                            if (isset($child->url) && str_starts_with($currentUrl, trim($child->url, '/'))) {
                                $isActive = true;
                                break;
                            }
                            if (!empty($child->slug) && $currentRoute && str_starts_with($currentRoute, $child->slug)) {
                                $isActive = true;
                                break;
                            }
                        }
                    }
                @endphp

                <li class="menu-item {{ $isActive ? 'active' : '' }}">
                    <a href="{{ isset($submenu->url) ? url($submenu->url) : 'javascript:void(0)' }}"
                       class="menu-link"
                       @if (isset($submenu->target) && !empty($submenu->target)) target="_blank" @endif>

                        {{-- ICON --}}
                        @if (isset($submenu->icon))
                            <i class="{{ $submenu->icon }}"></i>
                        @endif

                        {{-- TITLE --}}
                        <div>{{ isset($submenu->name) ? __($submenu->name) : '' }}</div>

                        {{-- BADGE --}}
                        @isset($submenu->badge)
                            <div class="badge bg-{{ $submenu->badge[0] }} rounded-pill ms-auto">
                                {{ $submenu->badge[1] }}
                            </div>
                        @endisset
                    </a>

                    {{-- RECURSIVE SUBMENU IF ANY --}}
                    @if (isset($submenu->submenu))
                        @include('layouts.sections.menu.submenu', ['menu' => $submenu->submenu])
                    @endif
                </li>
            @endif
        @endforeach
    @endif

</ul>
