@php
    $featuredGatesProject = $gatesProjects->first();
    $gatesLatestItems = $gatesProjects
        ->filter(fn ($item) => !str_contains(strtolower((string) ($item->type ?? '')), 'news'))
        ->sortByDesc(fn ($item) => optional($item->date)->timestamp ?? 0)
        ->take(8)
        ->map(function ($item) {
            $normalizedType = strtolower((string) ($item->type ?? ''));
            $collectionSlug = str_contains($normalizedType, 'issuance')
                ? 'issuances'
                : (str_contains($normalizedType, 'video') ? 'video-presentations' : 'projects');
            $label = str_contains($normalizedType, 'issuance')
                ? 'Issuance'
                : (str_contains($normalizedType, 'video') ? 'Video Presentation' : 'Project');

            return [
                'label' => $label,
                'title' => $item->title,
                'date' => optional($item->date)->format('M d, Y'),
                'url' => $item->url ?: route('portal.gates.show', ['collectionSlug' => $collectionSlug]),
            ];
        })
        ->values();
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
                                <h2 class="gates-title mt-3 mb-3">DOST<br><span class="split-title-accent">GATES Project 1</span></h2>
                                <p class="section-copy gates-copy">A focused overview of DOST GATES Project 1 and the project files currently shared through the PES portal. This section keeps the public-facing brief concise while giving visitors quick access to the supporting project references and data-driven initiatives.</p>
                                <div class="gates-hero-actions">
                                    <a class="btn btn-accent btn-lg rounded-pill px-4" href="#gates-achieves">Learn More <i class="bi bi-chevron-right ms-2"></i></a>
                                    <a class="btn dx-outline-btn btn-lg rounded-pill px-4" href="{{ route('portal.gates.show', ['collectionSlug' => 'projects']) }}" data-transition-label="Projects">Browse Project 1 Library <i class="bi bi-chevron-right ms-2"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="gates-hero-visual">
                                <div class="gates-hero-decoration"></div>
                                <div class="gates-logo-display">
                                    <img src="{{ asset('images/GATES LOGO.png') }}" alt="DOST GATES Project 1 Logo" class="gates-hero-logo">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="gates-achieves-section" id="gates-achieves">
                    <div class="gates-achieves-shell">
                        <div class="gates-achieves-header">
                            <h3 class="section-title split-title">What GATES Project 1 <span class="split-title-accent">Aims to Achieve</span></h3>
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
                    <span class="eyebrow">GATES PROJECT 1 NEWS</span>
                    <div class="pes-action-heading-copy">
                        <h2 class="section-title split-title">GATES Project 1 <span class="split-title-accent">News</span></h2>
                        <p class="section-copy mb-0">Latest stories and program updates from DOST GATES Project 1.</p>
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
                                    <div class="pes-action-news-eyebrow">{{ data_get($item, 'eyebrow', 'GATES PROJECT 1 NEWS') }}</div>
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
                            <strong>No GATES Project 1 news yet.</strong>
                            <span>Stories published from the DOST GATES Project 1 admin workspace will appear here.</span>
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
                        <div class="pes-action-modal-eyebrow">{{ data_get($item, 'eyebrow', 'GATES PROJECT 1 NEWS') }}</div>
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

<section class="section-space gates-band-section gates-whats-new-band" id="gates-whats-new" data-scroll-scene="gates-whats-new">
    <div class="container">
        <div class="gates-whats-new-shell">
            <div class="whats-new-orb whats-new-orb-left"></div>
            <div class="whats-new-orb whats-new-orb-right"></div>

            <div class="section-header whats-new-header gates-whats-new-header">
                <div>
                    <div class="whats-new-kicker gates-whats-new-kicker">GATES PROJECT 1 LIVE UPDATES</div>
                    <h2 class="section-title split-title">What's <span class="split-title-accent">New in GATES Project 1</span></h2>
                    <p class="section-copy mb-0">Latest GATES Project 1 issuances, projects, and presentations moving across the portal in real time.</p>
                </div>
            </div>

            @if ($gatesLatestItems->isNotEmpty())
                <div class="whats-new-marquee gates-whats-new-marquee" aria-label="Latest GATES Project 1 updates">
                    <div class="whats-new-track gates-whats-new-track">
                        @foreach ($gatesLatestItems->concat($gatesLatestItems) as $item)
                            <a class="whats-new-card gates-whats-new-card" href="{{ $item['url'] }}">
                                <span class="whats-new-card-type gates-whats-new-card-type">{{ $item['label'] }}</span>
                                <h3>{{ $item['title'] }}</h3>
                                <div class="whats-new-card-meta">
                                    <span>{{ $item['date'] }}</span>
                                    <span class="whats-new-card-link">View <i class="bi bi-arrow-up-right"></i></span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="pes-action-empty-state gates-news-empty-state mt-3">
                    <strong>No GATES Project 1 updates yet.</strong>
                    <span>Latest project, issuance, and video updates will appear here.</span>
                </div>
            @endif
        </div>
    </div>
</section>

<section class="section-space gates-band-section gates-library-band" id="gates-projects-section">
    <div class="container">
        <div class="gates-projects-section">
            <div class="gates-projects-shell" id="gates-projects">
                <div class="gates-projects-head">
                    <div>
                        <span class="eyebrow">Project Library</span>
                        <h3 class="section-title split-title mb-2">Browse by <span class="split-title-accent">Category</span></h3>
                        <p class="section-copy mb-0">Explore GATES Project 1 projects, issuances, and video presentations organized by category.</p>
                    </div>
                </div>

                <div class="gates-collection-grid">
                    @php
                        $projectsCount = $gatesProjects->filter(fn($p) => strtolower($p->type ?? '') === 'project')->count();
                        $issuancesCount = $gatesProjects->filter(fn($p) => strtolower($p->type ?? '') === 'issuance')->count();
                        $videosCount = $gatesProjects->filter(fn($p) => strtolower($p->type ?? '') === 'video presentation')->count();
                    @endphp
                    
                    <a href="{{ route('portal.gates.show', ['collectionSlug' => 'projects']) }}" class="gates-collection-card" data-transition-label="Projects">
                        <div class="gates-collection-icon">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <h4 class="gates-collection-title">Projects</h4>
                        <p class="gates-collection-copy">{{ $projectsCount }} project{{ $projectsCount !== 1 ? 's' : '' }} available</p>
                        <div class="gates-collection-action">
                            Browse Projects <i class="bi bi-arrow-right ms-2"></i>
                        </div>
                    </a>

                    <a href="{{ route('portal.gates.show', ['collectionSlug' => 'issuances']) }}" class="gates-collection-card" data-transition-label="Issuances">
                        <div class="gates-collection-icon">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <h4 class="gates-collection-title">Issuances</h4>
                        <p class="gates-collection-copy">{{ $issuancesCount }} issuance{{ $issuancesCount !== 1 ? 's' : '' }} available</p>
                        <div class="gates-collection-action">
                            Browse Issuances <i class="bi bi-arrow-right ms-2"></i>
                        </div>
                    </a>

                    <a href="{{ route('portal.gates.show', ['collectionSlug' => 'video-presentations']) }}" class="gates-collection-card" data-transition-label="Video Presentations">
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
