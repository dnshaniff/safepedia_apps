@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', $product->name)

@section('vendor-style')

@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-products-detail.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/fancybox/fancybox.js'])
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const mainImage = document.getElementById('mainProductImage');
            const mainLink = document.getElementById('mainProductLink');

            document.querySelectorAll('.thumbnail-item').forEach(item => {

                item.addEventListener('click', function() {

                    document
                        .querySelectorAll('.thumbnail-item')
                        .forEach(el => el.classList.remove('active'));

                    this.classList.add('active');

                    const image = this.dataset.image;

                    mainImage.src = image;
                    mainLink.href = image;
                });

            });

        });
    </script>
@endsection

@section('content')

    {{-- Hero --}}
    <section class="product-detail-hero">

        <div class="container text-center">

            <h1>
                Detail Product
            </h1>

        </div>

    </section>

    {{-- Detail --}}
    <section class="product-detail-section">

        <div class="container">

            <div class="row g-5">

                {{-- Gallery --}}
                <div class="col-lg-6">

                    <div class="product-gallery">

                        <a id="mainProductLink" href="{{ asset('storage/' . $product->thumbnail->file_path) }}"
                            data-fancybox="product-gallery">

                            <img id="mainProductImage" src="{{ asset('storage/' . $product->thumbnail->file_path) }}"
                                alt="{{ $product->name }}">

                        </a>

                    </div>

                    @if ($product->images->count())

                        <div class="product-thumbnails">

                            <div class="thumbnail-item active"
                                data-image="{{ asset('storage/' . $product->thumbnail->file_path) }}">

                                <img src="{{ asset('storage/' . $product->thumbnail->file_path) }}"
                                    alt="{{ $product->name }}">
                            </div>

                            @foreach ($galleryImages as $image)
                                <div class="thumbnail-item" data-image="{{ asset('storage/' . $image->file_path) }}">
                                    <img src="{{ asset('storage/' . $image->file_path) }}" alt="{{ $product->name }}">
                                </div>

                                <a href="{{ asset('storage/' . $image->file_path) }}" data-fancybox="product-gallery"
                                    class="d-none">
                                </a>
                            @endforeach

                        </div>

                    @endif

                </div>

                {{-- Content --}}
                <div class="col-lg-6">

                    <h1 class="product-title">
                        {{ $product->name }}
                    </h1>

                    @if ($product->brand)
                        <div class="product-meta">
                            <span class="meta-label">Brand:</span>
                            <span class="meta-value">
                                {{ $product->brand->name }}
                            </span>
                        </div>
                    @endif

                    <h4 class="mb-0">Description</h4>

                    <div class="product-description">
                        {!! $product->description !!}
                    </div>

                    <div class="product-features">

                        <div class="feature-item">
                            <i class="bx bx-check-shield"></i>
                            <span>Original Product</span>
                        </div>

                        <div class="feature-item">
                            <i class="bx bx-badge-check"></i>
                            <span>High Quality</span>
                        </div>

                        <div class="feature-item">
                            <i class="bx bx-support"></i>
                            <span>Technical Support</span>
                        </div>

                    </div>

                    <a href="https://wa.me/6285210001116?text=Hello,%20I%20am%20interested%20in%20{{ urlencode($product->name) }}"
                        target="_blank" class="btn product-whatsapp-btn">

                        <i class="bx bxl-whatsapp"></i>
                        Contact via WhatsApp

                    </a>

                </div>

            </div>

        </div>

    </section>

    {{-- Related Products --}}
    @if ($relatedProducts->count())

        <section class="related-products-section mb-12">

            <div class="container">

                <div class="section-heading">

                    <h2>
                        Related Products
                    </h2>

                </div>

                <div class="row g-4">

                    @foreach ($relatedProducts as $item)
                        <div class="col-lg-3 col-md-4 col-sm-6">

                            <div class="product-card">

                                <div class="product-image">

                                    <img src="{{ asset('storage/' . $item->thumbnail->file_path) }}"
                                        alt="{{ $item->name }}">

                                </div>

                                <div class="product-body">

                                    <h5>

                                        {{ $item->name }}

                                    </h5>

                                    <a href="{{ route('landing.product', $item->slug) }}" class="btn btn-outline-primary">

                                        View Product

                                    </a>

                                </div>

                            </div>

                        </div>
                    @endforeach

                </div>

            </div>

        </section>

    @endif

@endsection
