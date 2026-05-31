@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Our Projects')

@section('vendor-style')

@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-projects.scss'])
@endsection

@section('vendor-script')

@endsection

@section('page-script')

@endsection

@section('content')

    {{-- Hero --}}
    <section class="projects-hero">

        <div class="container text-center">

            <h1>
                OUR PROJECTS
            </h1>

            <p>
                Explore our completed lighting, electrical, and traffic infrastructure
                projects that demonstrate our commitment to quality and reliability.
            </p>

        </div>

    </section>

    {{-- Projects --}}
    <section class="projects-section">

        <div class="container">

            <div class="row g-4">

                @forelse ($projects as $project)
                    <div class="col-lg-4 col-md-6">

                        <div class="project-card">

                            <a href="{{ route('landing.project', $project->slug) }}" class="project-image">

                                <img src="{{ asset('storage/' . $project->thumbnail->file_path) }}"
                                    alt="{{ $project->title }}">

                            </a>

                            <div class="project-body">

                                <div class="project-date">

                                    <i class="bx bx-calendar"></i>

                                    {{ $project->created_at->format('d M Y') }}

                                </div>

                                <h3>

                                    <a href="{{ route('landing.project', $project->slug) }}">

                                        {{ $project->title }}

                                    </a>

                                </h3>

                                <p>

                                    {{ \Illuminate\Support\Str::limit(strip_tags($project->content), 140) }}

                                </p>

                                <a href="{{ route('landing.project', $project->slug) }}" class="project-link">

                                    View Project

                                    <i class="bx bx-right-arrow-alt"></i>

                                </a>

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="col-12">

                        <div class="empty-projects">

                            <i class="bx bx-folder-open"></i>

                            <h4>
                                No Projects Available
                            </h4>

                            <p>
                                Project information will be available soon.
                            </p>

                        </div>

                    </div>
                @endforelse

            </div>

        </div>

    </section>

@endsection
