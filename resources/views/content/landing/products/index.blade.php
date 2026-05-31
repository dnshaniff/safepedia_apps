@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Our Products')

@section('vendor-style')

@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-products.scss'])
@endsection

@section('vendor-script')

@endsection

@section('page-script')

@endsection

@section('content')
    <section class="products-hero">

        <div class="container text-center">

            <h1>OUR PRODUCTS</h1>

            <p>
                Explore our range of lighting components and electrical
                solutions designed to support your project requirements.
            </p>

        </div>

    </section>

    <section class="products-section">

        <div class="container">

            <div class="row g-4">

                @forelse ($products as $product)
                    <div class="col-lg-3 col-md-4 col-sm-6">

                        <div class="product-card">

                            <div class="product-image">

                                <img src="{{ $product->thumbnail ? asset('storage/' . $product->thumbnail->file_path) : asset('assets/img/placeholder.png') }}"
                                    alt="{{ $product->name }}">

                            </div>

                            <div class="product-body">

                                <h5>
                                    {{ $product->name }}
                                </h5>

                                <p>
                                    {{ \Illuminate\Support\Str::limit(strip_tags($product->description), 80) }}
                                </p>

                                <a href="{{ route('landing.product', $product->slug) }}" class="btn btn-outline-primary">

                                    View Details

                                </a>

                            </div>

                        </div>

                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p class="mb-0">
                            No products available.
                        </p>
                    </div>
                @endforelse

            </div>

        </div>

    </section>

@endsection
