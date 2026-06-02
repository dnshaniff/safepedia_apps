@php
    use Illuminate\Support\Facades\Route;

    $configData = Helper::appClasses();
    $currentRouteName = Route::currentRouteName();
    $baseRouteName = strstr($currentRouteName, '.', true) ?: $currentRouteName;

    $slugToBase = function ($slug) {
        if (!is_string($slug) || $slug === '') {
            return null;
        }
        $parts = explode('-', $slug);
        return end($parts) ?: $slug;
    };

    $slugMatchesRoute = function ($slug) use ($currentRouteName, $baseRouteName, $slugToBase) {
        if (is_array($slug)) {
            foreach ($slug as $s) {
                if ($slugMatchesRoute($s)) {
                    return true;
                }
            }
            return false;
        }

        if (!is_string($slug) || $slug === '') {
            return false;
        }

        if ($currentRouteName === $slug) {
            return true;
        }

        $slugBase = $slugToBase($slug);
        if ($slugBase && $baseRouteName === $slugBase) {
            return true;
        }

        return false;
    };

    $slugMatchesRoute = function ($slug) use (&$slugMatchesRoute, $currentRouteName, $baseRouteName, $slugToBase) {
        if (is_array($slug)) {
            foreach ($slug as $s) {
                if ($slugMatchesRoute($s)) {
                    return true;
                }
            }
            return false;
        }

        if (!is_string($slug) || $slug === '') {
            return false;
        }

        if ($currentRouteName === $slug) {
            return true;
        }

        $slugBase = $slugToBase($slug);
        if ($slugBase && $baseRouteName === $slugBase) {
            return true;
        }

        return false;
    };

    $isActiveDeep = function ($item) use (&$isActiveDeep, $slugMatchesRoute) {
        if (isset($item->slug) && $slugMatchesRoute($item->slug)) {
            return true;
        }

        if (isset($item->submenu) && is_iterable($item->submenu)) {
            foreach ($item->submenu as $child) {
                if ($isActiveDeep($child)) {
                    return true;
                }
            }
        }

        return false;
    };

    $filterByPermission = function ($items) use (&$filterByPermission) {
        $out = [];

        foreach ($items as $it) {
            if (isset($it->menuHeader)) {
                $out[] = $it;
                continue;
            }

            $hasSubmenu = isset($it->submenu) && is_iterable($it->submenu);

            $children = [];
            if ($hasSubmenu) {
                $children = $filterByPermission($it->submenu);
            }

            if ($hasSubmenu) {
                if (count($children) === 0) {
                    continue;
                }
                $it->submenu = $children;
                $out[] = $it;
                continue;
            }

            $allowedSelf = false;

            if (isset($it->slug) && $it->slug !== null && $it->slug !== '') {
                if (is_array($it->slug)) {
                    foreach ($it->slug as $s) {
                        if (auth()->user()->can($s)) {
                            $allowedSelf = true;
                            break;
                        }
                    }
                } else {
                    $allowedSelf = auth()->user()->can($it->slug);
                }
            }

            if ($allowedSelf) {
                $out[] = $it;
            }
        }

        return $out;
    };

    $filteredMenu = $filterByPermission($menuData[0]->menu);
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    @if (!isset($navbarFull))
        <div class="app-brand demo">
            <a href="{{ url('/') }}" class="app-brand-link">
                <span class="app-brand-logo demo">@include('_partials.macros', ['width' => 25, 'withbg' => 'var(--bs-primary)'])</span>
                <span class="app-brand-text demo menu-text fw-bold ms-2">{{ config('app.name') }}</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                <i class="bx bx-chevron-left bx-sm d-flex align-items-center justify-content-center"></i>
            </a>
        </div>
    @endif

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @foreach ($filteredMenu as $menu)
            @if (isset($menu->menuHeader))
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
                </li>
            @else
                @php
                    $hasChildren = isset($menu->submenu) && is_iterable($menu->submenu) && count($menu->submenu) > 0;
                    $isActive = $isActiveDeep($menu);

                    $activeClass = $isActive ? ($hasChildren ? 'active open' : 'active') : null;
                @endphp

                <li class="menu-item {{ $activeClass }}">
                    <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
                        class="{{ $hasChildren ? 'menu-link menu-toggle' : 'menu-link' }}"
                        @if (isset($menu->target) && !empty($menu->target)) target="_blank" @endif>
                        @isset($menu->icon)
                            <i class="{{ $menu->icon }}"></i>
                        @endisset
                        <div class="text-truncate">{{ isset($menu->name) ? __($menu->name) : '' }}</div>
                        @isset($menu->badge)
                            <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
                        @endisset
                    </a>

                    @if ($hasChildren)
                        @include('layouts.sections.menu.submenu', [
                            'menu' => $menu->submenu,
                            'configData' => $configData,
                            'isActiveDeep' => $isActiveDeep,
                        ])
                    @endif
                </li>
            @endif
        @endforeach
    </ul>
</aside>
