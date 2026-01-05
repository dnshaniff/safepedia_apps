@php
    use Illuminate\Support\Facades\Route;
@endphp

<ul class="menu-sub">
    @foreach ($menu as $submenu)
        @php
            $hasChildren = isset($submenu->submenu) && is_iterable($submenu->submenu) && count($submenu->submenu) > 0;
            $isActive = $isActiveDeep($submenu);

            $activeClass = null;
            if ($isActive) {
                $activeClass = $hasChildren
                    ? ($configData['layout'] === 'vertical'
                        ? 'active open'
                        : 'active')
                    : 'active';
            }
        @endphp

        <li class="menu-item {{ $activeClass }}">
            <a href="{{ isset($submenu->url) ? url($submenu->url) : 'javascript:void(0)' }}"
                class="{{ $hasChildren ? 'menu-link menu-toggle' : 'menu-link' }}"
                @if (isset($submenu->target) && !empty($submenu->target)) target="_blank" @endif>
                @if (isset($submenu->icon))
                    <i class="{{ $submenu->icon }}"></i>
                @endif
                <div>{{ isset($submenu->name) ? __($submenu->name) : '' }}</div>
                @isset($submenu->badge)
                    <div class="badge bg-{{ $submenu->badge[0] }} rounded-pill ms-auto">{{ $submenu->badge[1] }}</div>
                @endisset
            </a>

            @if ($hasChildren)
                @include('layouts.sections.menu.submenu', [
                    'menu' => $submenu->submenu,
                    'configData' => $configData,
                    'isActiveDeep' => $isActiveDeep,
                ])
            @endif
        </li>
    @endforeach
</ul>
