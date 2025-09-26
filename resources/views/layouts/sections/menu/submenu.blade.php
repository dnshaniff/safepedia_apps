@php
  use Illuminate\Support\Facades\Route;
@endphp

<ul class="menu-sub">
  @if (isset($menu))
    @foreach ($menu as $submenu)
      @php
        $activeClass = null;
        $active = $configData['layout'] === 'vertical' ? 'active open' : 'active';
        $currentRouteName = Route::currentRouteName();

        // Extract the base route name without the method (show, edit, etc)
        $baseRouteName = strstr($currentRouteName, '.', true) ?: $currentRouteName;

        // Get parent menu slug and current submenu base name
        $menuParts = explode('-', $submenu->slug);
        if (count($menuParts) > 1) {
            $parentPrefix = $menuParts[0];
            $menuBase = $menuParts[1];

            // Check if current route matches this submenu
            if ($baseRouteName === $menuBase || $currentRouteName === $submenu->slug) {
                $activeClass = 'active';
            }
        }

        // Handle nested submenus if they exist
        if (isset($submenu->submenu)) {
            if (gettype($submenu->slug) === 'array') {
                foreach ($submenu->slug as $slug) {
                    $slugParts = explode('-', $slug);
                    if (count($slugParts) > 1 && $baseRouteName === $slugParts[1]) {
                        $activeClass = $active;
                    }
                }
            } else {
                $slugParts = explode('-', $submenu->slug);
                if (count($slugParts) > 1 && $baseRouteName === $slugParts[1]) {
                    $activeClass = $active;
                }
            }
        }
      @endphp>

      <li class="menu-item {{ $activeClass }}">
        <a href="{{ isset($submenu->url) ? url($submenu->url) : 'javascript:void(0)' }}"
          class="{{ isset($submenu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
          @if (isset($submenu->target) and !empty($submenu->target)) target="_blank" @endif>
          @if (isset($submenu->icon))
            <i class="{{ $submenu->icon }}"></i>
          @endif
          <div>{{ isset($submenu->name) ? __($submenu->name) : '' }}</div>
          @isset($submenu->badge)
            <div class="badge bg-{{ $submenu->badge[0] }} rounded-pill ms-auto">{{ $submenu->badge[1] }}</div>
          @endisset
        </a>

        {{-- submenu --}}
        @if (isset($submenu->submenu))
          @include('layouts.sections.menu.submenu', ['menu' => $submenu->submenu])
        @endif
      </li>
    @endforeach
  @endif
</ul>
