<section id="products" class="products-section">
    <div class="container">

        <div class="section-header">

            <h2>OUR PRODUCT</h2>

            <a href="{{ route('landing.products') }}" class="section-link">
                VIEW ALL PRODUCTS
                <i class="bx bx-chevron-right"></i>
            </a>

        </div>

        <div class="row g-4">

            @foreach ($products as $product)
                <div class="col-lg-3 col-md-6">
                    <div class="product-card">

                        <div class="product-image">
                            <img src="{{ $product->thumbnail
                                ? asset('storage/' . $product->thumbnail->file_path)
                                : asset('assets/img/placeholder-product.png') }}"
                                alt="{{ $product->name }}" class="img-fluid">
                        </div>

                        <div class="product-content">
                            <h5>{{ strtoupper($product->name) }}</h5>

                            <a href="{{ route('landing.product', $product->slug) }}"
                                class="btn btn-outline-primary btn-sm">
                                VIEW PRODUCTS
                            </a>
                        </div>

                    </div>
                </div>
            @endforeach

        </div>

    </div>
</section>
