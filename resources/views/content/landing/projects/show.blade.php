@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', $project->title)

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-projects-detail.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/fancybox/fancybox.js'])
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const mainImage = document.getElementById('mainProjectImage');
            const mainLink = document.getElementById('mainProjectLink');

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
    <section class="projects-detail-hero">

        <div class="container text-center">

            <h1>PROJECT DETAILS</h1>

            <p>
                Explore our completed lighting, electrical, and infrastructure projects.
            </p>

        </div>

    </section>

    <section class="project-detail-page">

        <div class="container">

            {{-- Breadcrumb --}}
            <nav class="project-breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item">
                        <a href="{{ route('landing.index') }}">
                            Home
                        </a>
                    </li>

                    <li class="breadcrumb-item">
                        <a href="{{ route('landing.projects') }}">
                            Projects
                        </a>
                    </li>

                    <li class="breadcrumb-item active">
                        {{ $project->title }}
                    </li>

                </ol>

            </nav>

            {{-- Header --}}
            <div class="project-header">

                <h2 class="project-title">
                    {{ $project->title }}
                </h2>

                <div class="project-meta">

                    <div class="project-meta-item">

                        <i class="bx bx-calendar"></i>

                        <span>
                            {{ $project->created_at->format('d F Y') }}
                        </span>

                    </div>

                    @if ($project->location)
                        <div class="project-meta-item">

                            <i class="bx bx-map"></i>

                            <span>
                                {{ $project->location }}
                            </span>

                        </div>
                    @endif

                </div>

            </div>

            {{-- Gallery --}}
            <div class="project-gallery">

                <a href="{{ asset('storage/' . $project->thumbnail->file_path) }}" data-fancybox="gallery">

                    <img id="mainProjectImage" src="{{ asset('storage/' . $project->thumbnail->file_path) }}"
                        alt="{{ $project->title }}">

                </a>

            </div>

            @if ($galleryImages->count())

                <div class="project-thumbnails">

                    <div class="thumbnail-item active"
                        data-image="{{ asset('storage/' . $project->thumbnail->file_path) }}">

                        <img src="{{ asset('storage/' . $project->thumbnail->file_path) }}" alt="{{ $project->title }}">
                    </div>

                    @foreach ($galleryImages as $image)
                        <div class="thumbnail-item" data-image="{{ asset('storage/' . $image->file_path) }}">

                            <img src="{{ asset('storage/' . $image->file_path) }}" alt="{{ $project->title }}">

                        </div>
                    @endforeach

                </div>

                <div class="d-none">

                    <a href="{{ asset('storage/' . $project->thumbnail->file_path) }}" data-fancybox="gallery"></a>

                    @foreach ($galleryImages as $image)
                        <a href="{{ asset('storage/' . $image->file_path) }}" data-fancybox="gallery"></a>
                    @endforeach

                </div>

            @endif

            {{-- Content --}}
            <div class="project-content-card">

                <h3>
                    Project Details
                </h3>

                {!! $project->content !!}

            </div>

        </div>

    </section>

    @if ($relatedProjects->count())

        <section class="related-projects-section">

            <div class="container">

                <div class="section-heading">

                    <h2>
                        Related Projects
                    </h2>

                </div>

                <div class="row g-4">

                    @foreach ($relatedProjects as $item)
                        <div class="col-lg-4 col-md-6">

                            <div class="project-card">

                                <div class="project-card-image">

                                    <img src="{{ asset('storage/' . $item->thumbnail->file_path) }}"
                                        alt="{{ $item->title }}">

                                </div>

                                <div class="project-card-body">

                                    <h5>
                                        {{ $item->title }}
                                    </h5>

                                    <a href="{{ route('landing.project', $item->slug) }}" class="btn btn-outline-primary">

                                        View Project

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
