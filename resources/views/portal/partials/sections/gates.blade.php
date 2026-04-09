@php
    $featuredGatesProject = $gatesProjects->first();
@endphp

<section class="gates-stop-shell" id="gates-stop-shell">
    <div class="gates-stop-stage" id="gates-stop-stage">
        <section class="section-space gates-section" id="dost-gates" data-scroll-scene="gates">
            <div class="gates-background-overlay"></div>
            
            <div class="container">
                <div class="gates-hero-section">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-6">
                            <div class="gates-copy-wrap">
                                <span class="eyebrow gates-kicker">Geospatial Analytics Program</span>
                                <h2 class="gates-title mt-3 mb-3">DOST<br><span class="split-title-accent">GATES</span></h2>
                                <p class="section-copy gates-copy">A focused overview of DOST GATES and the project files currently shared through the PES portal. This section keeps the public-facing brief concise while giving visitors quick access to the supporting project references and data-driven initiatives.</p>
                                <div class="gates-hero-actions">
                                    <a class="btn btn-accent btn-lg rounded-pill px-4" href="#gates-achieves">Learn More <i class="bi bi-chevron-right ms-2"></i></a>
                                    <a class="btn dx-outline-btn btn-lg rounded-pill px-4" href="{{ route('portal.gates.show', ['collectionSlug' => 'projects']) }}">Browse GATES Library <i class="bi bi-chevron-right ms-2"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="gates-hero-visual">
                                <div class="gates-hero-decoration"></div>
                                <div class="gates-logo-display">
                                    <img src="{{ asset('images/GATES LOGO.png') }}" alt="DOST GATES Logo" class="gates-hero-logo">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="gates-achieves-section" id="gates-achieves">
                    <div class="gates-achieves-shell">
                        <div class="gates-achieves-header">
                            <h3 class="section-title split-title">What the GATES Program <span class="split-title-accent">Aims to Achieve</span></h3>
                        </div>
                        <div class="gates-achieves-grid">
                            <div class="gates-achieve-item">
                                <div class="gates-achieve-number">1</div>
                                <p class="gates-achieve-text">Support the data mapping and cleansing of various datasets of the DOST system, and develop the corresponding data architecture that will be scalable and AI-ready;</p>
                            </div>
                            <div class="gates-achieve-item">
                                <div class="gates-achieve-number">2</div>
                                <p class="gates-achieve-text">Build and put in place the necessary backend software and hardware required by the data architecture, the frontend dashboard for GATES, and the AI application in line with global standards for the ethical and responsible use of AI;</p>
                            </div>
                            <div class="gates-achieve-item">
                                <div class="gates-achieve-number">3</div>
                                <p class="gates-achieve-text">Develop AI-driven geospatial visualization and analytics solutions leveraging a unified data lakehouse to integrate diverse data sources and generate predictive geospatial insights using advanced analytics and suitability models for targeted applications;</p>
                            </div>
                            <div class="gates-achieve-item">
                                <div class="gates-achieve-number">4</div>
                                <p class="gates-achieve-text">Develop and capacitate a core team of DOST personnel, including agency representatives to upskill and develop them into data science roles, through the support of a dedicated team of high-level data experts;</p>
                            </div>
                            <div class="gates-achieve-item">
                                <div class="gates-achieve-number">5</div>
                                <p class="gates-achieve-text">Mainstream the GATES and its personnel in the DOST and its attached agencies through the necessary supporting policies and protocols, among other related activities; and</p>
                            </div>
                            <div class="gates-achieve-item">
                                <div class="gates-achieve-number">6</div>
                                <p class="gates-achieve-text">By the end of the program, the GATES Hub will be established at the DOST-Central Office, serving as the Department's central touchpoint for providing geospatial and AI analytics services to all DOST agencies and other relevant stakeholders.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</section>

<section class="section-space gates-band-section gates-news-band" id="gates-p1-news" data-scroll-scene="gates-news">
    <div class="container">
        <div class="gates-news-section">
            <div class="gates-news-shell">
                <div class="pes-action-heading gates-news-head">
                    <span class="eyebrow">GATES P1 NEWS</span>
                    <div class="pes-action-heading-copy">
                        <h2 class="section-title split-title">GATES P1 <span class="split-title-accent">News</span></h2>
                        <p class="section-copy mb-0">Latest stories and program updates from DOST GATES.</p>
                    </div>
                </div>

                <div class="pes-action-grid gates-news-grid">
                    @forelse ($gatesP1NewsItems as $item)
                        @php
                            $thumbnailSrc = data_get($item, 'image_url')
                                ? ((str_starts_with(data_get($item, 'image_url'), '/') || str_starts_with(data_get($item, 'image_url'), 'http'))
                                    ? data_get($item, 'image_url')
                                    : asset(data_get($item, 'image_url')))
                                : null;
                            $modalId = 'gatesP1NewsModal'.$loop->index;
                            $storyUrl = data_get($item, 'story_url');
                        @endphp
                        <article class="pes-action-news-card gates-news-card">
                            @if ($storyUrl)
                                <a
                                    class="pes-action-news-trigger"
                                    href="{{ $storyUrl }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                            @else
                                <button
                                    class="gates-news-slide-trigger pes-action-news-trigger"
                                    type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#{{ $modalId }}"
                                >
                            @endif
                                <div class="pes-action-news-media accent-{{ data_get($item, 'accent', 'cyan') }}">
                                    @if ($thumbnailSrc)
                                        <img src="{{ $thumbnailSrc }}" alt="{{ data_get($item, 'image_alt', data_get($item, 'title')) }}" class="pes-action-news-image">
                                    @else
                                        <div class="pes-action-news-placeholder">
                                            <span>{{ data_get($item, 'image_alt', data_get($item, 'title')) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="pes-action-news-body">
                                    <div class="pes-action-news-eyebrow">{{ data_get($item, 'eyebrow', 'GATES P1 NEWS') }}</div>
                                    <h3 class="pes-action-news-title">{{ data_get($item, 'title') }}</h3>
                                    <div class="pes-action-news-date">{{ optional(data_get($item, 'date'))->format('F d, Y') }}</div>
                                    <p class="pes-action-news-summary">{{ data_get($item, 'summary') }}</p>
                                    <span class="pes-action-news-link">
                                        {{ $storyUrl ? 'Open Article' : 'Read Full' }}
                                        <i class="bi bi-chevron-right"></i>
                                    </span>
                                </div>
                            @if ($storyUrl)
                                </a>
                            @else
                                </button>
                            @endif
                        </article>
                    @empty
                        <div class="pes-action-empty-state gates-news-empty-state">
                            <strong>No GATES P1 News yet.</strong>
                            <span>Stories published from DOST GATES admin will appear here.</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

@foreach ($gatesP1NewsItems as $item)
    @php
        $itemDate = data_get($item, 'date');
        $formattedDate = $itemDate ? \Illuminate\Support\Carbon::parse($itemDate)->format('F d, Y') : null;
        $storyUrl = data_get($item, 'story_url');
        $thumbnailSrc = data_get($item, 'image_url')
            ? ((str_starts_with(data_get($item, 'image_url'), '/') || str_starts_with(data_get($item, 'image_url'), 'http'))
                ? data_get($item, 'image_url')
                : asset(data_get($item, 'image_url')))
            : null;
    @endphp
    <div class="modal fade" id="gatesP1NewsModal{{ $loop->index }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content pes-action-modal gates-news-modal">
                <div class="modal-body p-0">
                    <button class="btn-close pes-action-modal-close gates-news-modal-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>

                    <div class="pes-action-modal-media accent-{{ data_get($item, 'accent', 'cyan') }}">
                        @if ($thumbnailSrc)
                            <img src="{{ $thumbnailSrc }}" alt="{{ data_get($item, 'image_alt', data_get($item, 'title')) }}" class="pes-action-modal-image">
                        @else
                            <div class="pes-action-modal-placeholder">
                                <span>{{ data_get($item, 'image_alt', data_get($item, 'title')) }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="pes-action-modal-content">
                        <div class="pes-action-modal-eyebrow">{{ data_get($item, 'eyebrow', 'GATES P1 NEWS') }}</div>
                        <h3 class="pes-action-modal-title">{{ data_get($item, 'title') }}</h3>
                        @if ($formattedDate)
                            <div class="pes-action-modal-date">{{ $formattedDate }}</div>
                        @endif
                        <p class="pes-action-modal-copy">{{ data_get($item, 'content', data_get($item, 'summary')) }}</p>
                        @if ($storyUrl)
                            <div class="mt-4">
                                <a class="btn btn-accent rounded-pill px-4" href="{{ $storyUrl }}" target="_blank" rel="noopener noreferrer">Open Full Article</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

<section class="section-space gates-band-section gates-library-band" id="gates-projects-section">
    <div class="container">
        <div class="gates-projects-section">
            <div class="gates-projects-shell" id="gates-projects">
                <div class="gates-projects-head">
                    <div>
                        <span class="eyebrow">Project Library</span>
                        <h3 class="section-title split-title mb-2">Browse by <span class="split-title-accent">Category</span></h3>
                        <p class="section-copy mb-0">Explore GATES projects, issuances, and video presentations organized by category.</p>
                    </div>
                </div>

                <div class="gates-collection-grid">
                    @php
                        $projectsCount = $gatesProjects->filter(fn($p) => strtolower($p->type ?? '') === 'project')->count();
                        $issuancesCount = $gatesProjects->filter(fn($p) => strtolower($p->type ?? '') === 'issuance')->count();
                        $videosCount = $gatesProjects->filter(fn($p) => strtolower($p->type ?? '') === 'video presentation')->count();
                    @endphp
                    
                    <a href="{{ route('portal.gates.show', ['collectionSlug' => 'projects']) }}" class="gates-collection-card">
                        <div class="gates-collection-icon">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <h4 class="gates-collection-title">Projects</h4>
                        <p class="gates-collection-copy">{{ $projectsCount }} project{{ $projectsCount !== 1 ? 's' : '' }} available</p>
                        <div class="gates-collection-action">
                            Browse Projects <i class="bi bi-arrow-right ms-2"></i>
                        </div>
                    </a>

                    <a href="{{ route('portal.gates.show', ['collectionSlug' => 'issuances']) }}" class="gates-collection-card">
                        <div class="gates-collection-icon">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <h4 class="gates-collection-title">Issuances</h4>
                        <p class="gates-collection-copy">{{ $issuancesCount }} issuance{{ $issuancesCount !== 1 ? 's' : '' }} available</p>
                        <div class="gates-collection-action">
                            Browse Issuances <i class="bi bi-arrow-right ms-2"></i>
                        </div>
                    </a>

                    <a href="{{ route('portal.gates.show', ['collectionSlug' => 'video-presentations']) }}" class="gates-collection-card">
                        <div class="gates-collection-icon">
                            <i class="bi bi-play-circle"></i>
                        </div>
                        <h4 class="gates-collection-title">Video Presentations</h4>
                        <p class="gates-collection-copy">{{ $videosCount }} video{{ $videosCount !== 1 ? 's' : '' }} available</p>
                        <div class="gates-collection-action">
                            Browse Videos <i class="bi bi-arrow-right ms-2"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
