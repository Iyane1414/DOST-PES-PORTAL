<section class="section-space dx-section" id="dost-dx" data-scroll-scene="dx">
    @php
        $dxProjectTotal = $dxSubPrograms->sum(fn ($item) => count($item['projects']));
    @endphp
    <div class="container">
        <div class="dx-hero">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <div class="dx-copy-wrap">
                        <span class="eyebrow text-accent">The Future is Digital</span>
                        <h2 class="dx-title mt-3 mb-3">DOST Digital<br><span class="split-title-accent">Transformation</span></h2>
                        <p class="section-copy text-white-50 mb-0">DOST DX is our commitment to becoming a data-driven, agile, and citizen-centric organization.</p>
                        <div class="dx-hero-actions">
                            <button class="btn btn-accent btn-lg rounded-pill px-4" type="button" data-bs-toggle="modal" data-bs-target="#dxOverviewModal">Learn More <i class="bi bi-chevron-right ms-2"></i></button>
                            <a class="btn dx-outline-btn btn-lg rounded-pill px-4" href="#dx-roadmap">DOST DX Roadmap <i class="bi bi-chevron-right ms-2"></i></a>
                        </div>
                        <p class="dx-action-subtitle">Start with a quick overview or explore the implementation path.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="dx-hero-visual">
                        <div class="dx-hero-image">
                            <img src="{{ asset('images/dostdx.png') }}" alt="DOST DX logo" class="dx-hero-photo dx-hero-photo-logo">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="dx-video-slot" aria-label="DOST DX overview video">
            <div class="dx-video-frame">
                <video class="dx-video-element" controls preload="metadata">
                    <source src="{{ asset('videos/dost-dx-final-video.mp4') }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </section>

        <section class="dx-roadmap" id="dx-roadmap">
            <div class="dx-roadmap-header">
                <div>
                    <span class="eyebrow text-accent">Implementation Path</span>
                    <h3 class="section-title split-title text-white mb-3">DOST DX<br><span class="split-title-accent">Roadmap</span></h3>
                    <p class="section-copy text-white-50 mb-0">A timeline view of the DOST-DX program from early planning and readiness work through modernization, rollout, and expansion.</p>
                </div>
            </div>
            <div class="dx-roadmap-track">
                @forelse ($dxRoadmapItems as $item)
                    <article class="dx-roadmap-card">
                        <div class="dx-roadmap-year">{{ $item->year_label }}</div>
                        <h4 class="dx-roadmap-title">{{ $item->title }}</h4>
                        <p class="dx-roadmap-copy">{{ $item->description }}</p>
                        @if (!empty($item->milestones))
                            <ul class="dx-roadmap-list">
                                @foreach ($item->milestones as $milestone)
                                    <li>{{ $milestone }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </article>
                @empty
                    <div class="dx-empty-state">No roadmap stages available yet.</div>
                @endforelse
            </div>
        </section>

        <div class="dx-content" id="dx-content">
            <div class="dx-tabbar" role="tablist" aria-label="DOST DX sections">
                <button class="dx-tab is-active" type="button" data-dx-tab="domains" aria-pressed="true">3 Core Domains</button>
                <button class="dx-tab" type="button" data-dx-tab="programs" aria-pressed="false">Sub-programs</button>
            </div>

            <div class="dx-panel is-active" data-dx-panel="domains">
                <div class="dx-domain-grid">
                    @forelse ($dxCoreDomains as $item)
                        <button
                            class="dx-domain-card"
                            type="button"
                            data-dx-domain="{{ $item['slug'] }}"
                            data-dx-default-program="{{ $item['default_sub_program'] }}"
                            aria-label="View {{ $item['title'] }} sub-programs"
                        >
                            <div class="dx-domain-media">
                                <img src="{{ asset($item['image']) }}" alt="{{ $item['title'] }}" class="dx-domain-image">
                            </div>
                            <div class="dx-domain-overlay"></div>
                            <div class="dx-domain-body">
                                <div class="dx-domain-icon"><i class="bi {{ $item['icon'] }}"></i></div>
                                <h3 class="dx-domain-title">{{ $item['title'] }}</h3>
                                <p class="dx-domain-copy mb-0">{{ $item['description'] }}</p>
                            </div>
                        </button>
                    @empty
                        <div class="dx-empty-state">No core domains available yet.</div>
                    @endforelse
                </div>
            </div>

            <div class="dx-panel" data-dx-panel="programs" hidden>
                <div class="dx-program-grid">
                    @forelse ($dxSubPrograms as $item)
                        <a
                            class="dx-sub-card"
                            data-domain="{{ $item['domain'] }}"
                            data-program-slug="{{ $item['slug'] }}"
                            href="{{ route('portal.dx.program.show', ['domainSlug' => $item['domain'], 'subProgramSlug' => $item['slug']]) }}"
                            data-transition-label="{{ $item['title'] }}"
                        >
                            <div class="dx-sub-card-top">
                                <div class="program-index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                                <div class="dx-program-icon" aria-hidden="true">
                                    <i class="bi {{ $item['icon'] }}"></i>
                                </div>
                            </div>
                            <h3 class="dx-program-title">{{ $item['title'] }}</h3>
                            <p class="text-white-50 mb-0">{{ $item['description'] }}</p>
                            <span class="dx-program-domain">{{ strtoupper($item['domain_label']) }}</span>
                            <span class="dx-sub-card-hint">Open dedicated page <i class="bi bi-arrow-up-right"></i></span>
                        </a>
                    @empty
                        <div class="dx-empty-state">No sub-programs available yet.</div>
                    @endforelse
                </div>

            </div>

            <div class="dx-metrics-board" aria-label="DOST DX quick stats">
                <article class="dx-metric-card">
                    <strong class="dx-metric-number" data-target="3">0</strong>
                    <span>Core Domains</span>
                </article>
                <article class="dx-metric-card">
                    <strong class="dx-metric-number" data-target="{{ $dxSubPrograms->count() }}">0</strong>
                    <span>Sub-programs</span>
                </article>
                <article class="dx-metric-card">
                    <strong class="dx-metric-number" data-target="{{ $dxProjectTotal }}">0</strong>
                    <span>Project</span>
                </article>
                <article class="dx-metric-card">
                    <strong class="dx-metric-number" data-target="20">0</strong>
                    <span>Project Done</span>
                </article>
                <div class="dx-metric-void dx-metric-void-left" aria-hidden="true"></div>
                <article class="dx-metric-card dx-metric-card-secondary dx-metric-card-ongoing">
                    <strong class="dx-metric-number" data-target="0">0</strong>
                    <span>Ongoing Project</span>
                </article>
                <article class="dx-metric-card dx-metric-card-secondary dx-metric-card-planned">
                    <strong class="dx-metric-number" data-target="0">0</strong>
                    <span>Project Planned</span>
                </article>
                <div class="dx-metric-void" aria-hidden="true"></div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade dx-modal-shell" id="dxOverviewModal" tabindex="-1" aria-labelledby="dxOverviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content dx-modal-content">
            <div class="modal-header dx-modal-header">
                <div>
                    <span class="eyebrow text-accent">DOST DX Overview</span>
                    <h3 class="modal-title dx-modal-title" id="dxOverviewModalLabel">What is DOST DX?</h3>
                </div>
                <button class="btn dx-modal-close" type="button" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body dx-modal-body">
                <p class="dx-modal-copy mb-0">The DOST DX is the digital transformation initiative of the DOST system. It aims to serve as a blueprint for digital transformation efforts across the government, providing a replicable model for other government agencies. Through this program, DOST envisions a future of interconnected, interoperable, and unified government services, optimizing and reinforcing the DOST's role in integrating technology and innovation across the public sector.</p>
            </div>
            <div class="modal-footer dx-modal-footer">
                <button class="btn btn-accent rounded-pill px-4" type="button" data-dx-modal-action="domains">Explore Core Domains</button>
            </div>
        </div>
    </div>
</div>
