@php
    use Illuminate\Support\Facades\Route;
    $configData = Helper::appClasses();
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <!-- ! Hide app brand if navbar-full -->
    @if (!isset($navbarFull))
        <div class="app-brand demo">
            <a href="{{ url('/') }}" class="app-brand-link">
                <span class="app-brand-logo demo">@include('_partials.macros', ['width' => 25, 'withbg' => 'var(--bs-primary)'])</span>
                <span class="app-brand-text demo menu-text fw-bold ms-2">{{ env('APP_NAME') }}</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                <i class="bx bx-chevron-left bx-sm d-flex align-items-center justify-content-center"></i>
            </a>
        </div>
    @endif

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @foreach ($menuData[0]->menu as $menu)
            @if (isset($menu->menuHeader))
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
                </li>
            @else
                @php
                    $hasPermission = false;
                    $currentRouteName = Route::currentRouteName();
                    $subMenuPermissions = [];
                    $activeClass = null;

                    $isRelatedRoute = function ($menuSlug, $currentRoute, $parentSlug = null) {
                        // Handle the main route
                        if ($currentRoute === $menuSlug) {
                            return true;
                        }

                        // Handle related resource routes
                        if ($parentSlug && str_contains($menuSlug, $parentSlug . '-')) {
                            // Extract the base name by removing the parent prefix
                            $baseName = substr($menuSlug, strlen($parentSlug) + 1);
                            // Check if current route starts with the base resource name
                            return str_starts_with($currentRoute, $baseName . '.');
                        }

                        return false;
                    };

                    if (isset($menu->submenu)) {
                        foreach ($menu->submenu as $submenu) {
                            $thirdLevelPermissions = [];

                            if (isset($submenu->submenu)) {
                                foreach ($submenu->submenu as $thirdLevelMenu) {
                                    if (auth()->user()->can($thirdLevelMenu->slug)) {
                                        $thirdLevelPermissions[] = $thirdLevelMenu;
                                        $hasPermission = true;
                                    }
                                }
                                if (count($thirdLevelPermissions) > 0) {
                                    $submenu->submenu = $thirdLevelPermissions;
                                    $subMenuPermissions[] = $submenu;
                                }
                            } else {
                                if (auth()->user()->can($submenu->slug)) {
                                    $subMenuPermissions[] = $submenu;
                                    $hasPermission = true;
                                }
                            }
                        }
                    } elseif (isset($menu->slug)) {
                        $hasPermission = auth()->user()->can($menu->slug);
                    }

                    // Modified active class logic
                    if ($isRelatedRoute($menu->slug, $currentRouteName)) {
                        $activeClass = 'active';
                    } elseif (isset($menu->submenu)) {
                        foreach ($menu->submenu as $submenu) {
                            if (
                                $isRelatedRoute($submenu->slug, $currentRouteName, $menu->slug) ||
                                (isset($submenu->submenu) &&
                                    collect($submenu->submenu)->contains(function ($thirdLevel) use (
                                        $currentRouteName,
                                        $isRelatedRoute,
                                        $menu,
                                    ) {
                                        return $isRelatedRoute($thirdLevel->slug, $currentRouteName, $menu->slug);
                                    }))
                            ) {
                                $activeClass = 'active open';
                                break;
                            }
                        }
                    }
                @endphp

                @if ($hasPermission)
                    <li class="menu-item {{ $activeClass }}">
                        <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
                            class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
                            @if (isset($menu->target) and !empty($menu->target)) target="_blank" @endif>
                            @isset($menu->icon)
                                <i class="{{ $menu->icon }}"></i>
                            @endisset
                            <div class="text-truncate">{{ isset($menu->name) ? __($menu->name) : '' }}</div>
                            @isset($menu->badge)
                                <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
                            @endisset
                        </a>

                        @isset($menu->submenu)
                            <ul class="menu-sub">
                                @foreach ($subMenuPermissions as $submenu)
                                    @php
                                        $subMenuActiveClass = null;

                                        // Check if the submenu should be active
                                        if ($isRelatedRoute($submenu->slug, $currentRouteName, $menu->slug)) {
                                            $subMenuActiveClass = 'active open';
                                        }
                                    @endphp

                                    <li class="menu-item {{ $subMenuActiveClass }}">
                                        <a href="{{ url($submenu->url ?? 'javascript:void(0);') }}"
                                            class="{{ isset($submenu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}">
                                            <div>{{ __($submenu->name) }}</div>
                                        </a>

                                        @isset($submenu->submenu)
                                            <ul class="menu-sub">
                                                @foreach ($submenu->submenu as $thirdLevelMenu)
                                                    @php
                                                        $thirdLevelActiveClass = null;

                                                        if (
                                                            $isRelatedRoute(
                                                                $thirdLevelMenu->slug,
                                                                $currentRouteName,
                                                                $submenu->slug,
                                                            )
                                                        ) {
                                                            $thirdLevelActiveClass = 'active';
                                                        }
                                                    @endphp

                                                    <li class="menu-item {{ $thirdLevelActiveClass }}">
                                                        <a href="{{ url($thirdLevelMenu->url) }}" class="menu-link">
                                                            <div>{{ __($thirdLevelMenu->name) }}</div>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endisset
                                    </li>
                                @endforeach
                            </ul>
                        @endisset
                    </li>
                @endif
            @endif
        @endforeach
    </ul>

</aside>
