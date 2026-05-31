@php
    use Illuminate\Support\Facades\Route;

    $route = Route::currentRouteName();
@endphp

<nav class="navbar navbar-expand-lg landing-navbar sticky-top">
    <div class="container">

        <a class="navbar-brand" href="{{ route('landing.index') }}">

            @if (file_exists(public_path('assets/img/branding/logo-dna.png')))
                <img class="logo-light" src="{{ asset('assets/img/branding/logo-dna-3.png') }}" alt="DNA Lighting">

                <img class="logo-dark" src="{{ asset('assets/img/branding/logo-dna-2.png') }}" alt="DNA Lighting">
            @else
                <span class="navbar-brand-text">
                    <span>D</span>NA LIGHTING
                </span>
            @endif

        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#landingNavbar"
            aria-controls="landingNavbar" aria-expanded="false">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse" id="landingNavbar">

            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">

                <li class="nav-item">

                    <a class="nav-link {{ $route === 'landing.index' ? 'active' : '' }}"
                        href="{{ route('landing.index') }}">

                        Home

                    </a>

                </li>

                <li class="nav-item">

                    <a class="nav-link {{ in_array($route, ['landing.products', 'landing.product']) ? 'active' : '' }}"
                        href="{{ route('landing.products') }}">

                        Products

                    </a>

                </li>

                <li class="nav-item">

                    <a class="nav-link {{ in_array($route, ['landing.projects', 'landing.project']) ? 'active' : '' }}"
                        href="{{ route('landing.projects') }}">

                        Projects

                    </a>

                </li>

            </ul>

        </div>

    </div>
</nav>
