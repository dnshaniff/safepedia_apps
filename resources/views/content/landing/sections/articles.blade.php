<section id="projects" class="articles-section">
    <div class="container">

        <div class="section-header">

            <h2>LATEST PROJECTS</h2>

            <a href="{{ route('landing.projects') }}" class="section-link">
                VIEW ALL PROJECTS
                <i class="bx bx-chevron-right"></i>
            </a>

        </div>

        <div class="row g-4">

            @foreach ($articles as $article)
                <div class="col-lg-3 col-md-6">

                    <a href="{{ route('landing.project', $article->slug) }}" class="text-decoration-none">

                        <article class="article-card">

                            <div class="article-image">
                                <img src="{{ $article->thumbnail
                                    ? asset('storage/' . $article->thumbnail->file_path)
                                    : asset('assets/img/placeholder-article.png') }}"
                                    alt="{{ $article->title }}" class="img-fluid">
                            </div>

                            <div class="article-body">

                                <h5>
                                    {{ \Illuminate\Support\Str::limit($article->title, 60) }}
                                </h5>

                                <div class="article-meta">

                                    <div class="article-date">
                                        <i class="bx bx-calendar"></i>
                                        {{ optional($article->project_at)->format('d F, Y') }}
                                    </div>

                                    @if ($article->location)
                                        <div class="article-location">
                                            <i class="bx bx-map"></i>
                                            {{ $article->location }}
                                        </div>
                                    @endif

                                </div>

                            </div>

                        </article>

                    </a>

                </div>
            @endforeach

        </div>

    </div>
</section>
