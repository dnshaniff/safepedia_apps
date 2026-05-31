<section id="brands" class="brands-section">
    <div class="container">

        <div class="section-heading text-center mb-5">
            <h2>OUR TRUSTED BRANDS</h2>
        </div>

        <div class="brands-grid">

            <div class="brands-grid-wrapper">

                @foreach ($brands as $brand)
                    <div class="brand-logo">
                        <img src="{{ asset('storage/' . $brand->file_path) }}" alt="{{ $brand->name }}">
                    </div>
                @endforeach

            </div>

        </div>

        <div class="brands-marquee">

            <div class="brands-track">

                <div class="brands-list">

                    @foreach ($brands as $brand)
                        <div class="brand-logo">
                            <img src="{{ asset('storage/' . $brand->file_path) }}" alt="{{ $brand->name }}">
                        </div>
                    @endforeach

                </div>

                <div class="brands-list">

                    @foreach ($brands as $brand)
                        <div class="brand-logo">
                            <img src="{{ asset('storage/' . $brand->file_path) }}" alt="{{ $brand->name }}">
                        </div>
                    @endforeach

                </div>

            </div>

        </div>

    </div>
</section>
