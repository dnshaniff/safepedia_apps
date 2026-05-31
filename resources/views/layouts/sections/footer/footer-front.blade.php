<footer class="footer-section">

    <div class="container">

        <div class="row gy-5">

            <div class="col-lg-4">

                {{-- Logo atau teks brand --}}
                @if (file_exists(public_path('assets/img/branding/logo-dna-3.png')))
                    <img src="{{ asset('assets/img/branding/logo-dna-3.png') }}" alt="DNA Lighting" class="footer-logo">
                @elseif (file_exists(public_path('assets/img/branding/logo-dna.png')))
                    <img src="{{ asset('assets/img/branding/logo-dna.png') }}" alt="DNA Lighting" class="footer-logo">
                @else
                    <div class="footer-brand-text mb-3">
                        <span>D</span>NA LIGHTING
                    </div>
                @endif

                <p class="footer-description">
                    Supplier of lighting components, APILL equipment,
                    and professional solutions for your project needs.
                </p>

            </div>

            <div class="col-lg-2 col-md-4 col-6">

                <h5 class="footer-title">MENU</h5>

                <ul class="footer-links">
                    <li><a href="#hero">Home</a></li>
                    <li><a href="#products">Products</a></li>
                    <li><a href="#projects">Projects</a></li>
                </ul>

            </div>

            <div class="col-lg-2 col-md-4 col-6">

                <h5 class="footer-title">E-Commerce</h5>

                <ul class="footer-links">
                    <li>
                        <a href="https://shopee.co.id/dnalighting" target="_blank">
                            <i class="bx bx-store"></i>
                            Shopee
                        </a>
                    </li>
                    <li>
                        <a href="https://www.tokopedia.com/dnalighting" target="_blank">
                            <i class="bx bx-store"></i>
                            Tokopedia
                        </a>
                    </li>
                </ul>

            </div>

        </div>

        <hr class="footer-divider">

        <div class="footer-bottom">
            <p>© {{ date('Y') }} DNA Lighting. All Rights Reserved.</p>
        </div>

    </div>

</footer>
