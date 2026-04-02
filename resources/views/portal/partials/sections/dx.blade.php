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
                <article class="dx-roadmap-card">
                    <div class="dx-roadmap-year">2021</div>
                    <h4 class="dx-roadmap-title">Plan &amp; Assess</h4>
                    <p class="dx-roadmap-copy">The initial stage focused on defining the DOST-DX direction, assessing readiness, and securing the first set of approvals.</p>
                    <ul class="dx-roadmap-list">
                        <li>Roadmap</li>
                        <li>Gap Analysis</li>
                        <li>Initial Approvals</li>
                    </ul>
                </article>
                <article class="dx-roadmap-card">
                    <div class="dx-roadmap-year">2022</div>
                    <h4 class="dx-roadmap-title">Build Foundations</h4>
                    <p class="dx-roadmap-copy">This stage established the first operational building blocks needed to support transformation initiatives across DOST.</p>
                    <ul class="dx-roadmap-list">
                        <li>Software Dev Pack</li>
                        <li>Structure Rationalization</li>
                        <li>iLab Launch</li>
                    </ul>
                </article>
                <article class="dx-roadmap-card">
                    <div class="dx-roadmap-year">2023</div>
                    <h4 class="dx-roadmap-title">Strengthen Governance</h4>
                    <p class="dx-roadmap-copy">Governance, portfolio coordination, and cyber capability-building were reinforced to prepare for wider digital delivery.</p>
                    <ul class="dx-roadmap-list">
                        <li>Portfolio Groups</li>
                        <li>EA Training</li>
                        <li>Cybersecurity Expansion</li>
                    </ul>
                </article>
                <article class="dx-roadmap-card">
                    <div class="dx-roadmap-year">2024</div>
                    <h4 class="dx-roadmap-title">Standardize Processes</h4>
                    <p class="dx-roadmap-copy">DOST-DX advanced process standardization, project management structures, and broader stakeholder engagement.</p>
                    <ul class="dx-roadmap-list">
                        <li>PM Process</li>
                        <li>DX Core Team Proposal</li>
                        <li>Engagement Expansion</li>
                    </ul>
                </article>
                <article class="dx-roadmap-card">
                    <div class="dx-roadmap-year">2025</div>
                    <h4 class="dx-roadmap-title">System Integration</h4>
                    <p class="dx-roadmap-copy">Major integration workstreams were lined up to harmonize systems and strengthen portal and knowledge-management development.</p>
                    <ul class="dx-roadmap-list">
                        <li>Harmonized iHRMIS</li>
                        <li>Portal &amp; KM Dev</li>
                        <li>Phase 2 IS</li>
                    </ul>
                </article>
                <article class="dx-roadmap-card">
                    <div class="dx-roadmap-year">2026&ndash;2028</div>
                    <h4 class="dx-roadmap-title">Modernize &amp; Expand</h4>
                    <p class="dx-roadmap-copy">The long-range phase focuses on infrastructure modernization, harmonized rollouts, cyber capability expansion, and broader transformation support.</p>
                    <ul class="dx-roadmap-list">
                        <li>Data Centers</li>
                        <li>Cloud Plans</li>
                        <li>Harmonized IS Rollout</li>
                        <li>Cybersecurity Unit and ITD Transformation</li>
                        <li>Geospatial</li>
                    </ul>
                </article>
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
                        <article class="dx-domain-card" role="button" tabindex="0" onclick="dxGoToSubProgram('{{ $item['slug'] }}', '{{ $item['default_sub_program'] }}')" onkeydown="if(event.key === 'Enter' || event.key === ' '){ event.preventDefault(); dxGoToSubProgram('{{ $item['slug'] }}', '{{ $item['default_sub_program'] }}'); }">
                            <div class="dx-domain-media">
                                <img src="{{ asset($item['image']) }}" alt="{{ $item['title'] }}" class="dx-domain-image">
                            </div>
                            <div class="dx-domain-overlay"></div>
                            <div class="dx-domain-body">
                                <div class="dx-domain-icon"><i class="bi {{ $item['icon'] }}"></i></div>
                                <h3 class="dx-domain-title">{{ $item['title'] }}</h3>
                                <p class="dx-domain-copy mb-0">{{ $item['description'] }}</p>
                            </div>
                        </article>
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
                        >
                            <div class="program-index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                            <h3 class="dx-program-title">{{ $item['title'] }}</h3>
                            <p class="text-white-50 mb-0">{{ $item['description'] }}</p>
                            <span class="dx-program-domain">{{ strtoupper($item['domain_label']) }}</span>
                            <span class="dx-sub-card-hint">Open dedicated page <i class="bi bi-arrow-up-right"></i></span>
                        </a>
                    @empty
                        <div class="dx-empty-state">No sub-programs available yet.</div>
                    @endforelse
                </div>

                @if ($dxSubPrograms->isNotEmpty())
                    <div class="dx-project-viewer">
                        @foreach ($dxCoreDomains as $domain)
                            @php
                                $domainPrograms = $dxSubPrograms->where('domain', $domain['slug'])->values();
                                $domainProjects = $domainPrograms->flatMap(function (array $program) {
                                    return collect($program['projects'])->map(function (array $project) use ($program) {
                                        $project['program_title'] = $program['title'];
                                        $project['program_slug'] = $program['slug'];

                                        return $project;
                                    });
                                })->values();
                            @endphp
                            <section
                                class="dx-project-panel{{ $loop->first ? ' is-active' : '' }}"
                                data-domain-panel="{{ $domain['slug'] }}"
                                @if (! $loop->first) hidden @endif
                            >
                                <div class="dx-project-panel-head">
                                    <div>
                                        <div class="dx-project-domain-chip">{{ $domain['title'] }}</div>
                                        <span class="eyebrow text-accent">Core Domain</span>
                                        <h3 class="section-title split-title text-white mb-2">{{ $domain['title'] }}</h3>
                                        <p class="section-copy text-white-50 mb-0">{{ $domain['description'] }}</p>
                                    </div>
                                    <div class="dx-project-panel-side">
                                        <div class="dx-project-domain-summary">
                                            <span class="dx-project-domain-label">Sub-programs</span>
                                            <strong>{{ $domainPrograms->count() }}</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="dx-project-list" aria-label="{{ $domain['title'] }} projects">
                                    @foreach ($domainProjects as $project)
                                        <a
                                            class="dx-project-item"
                                            id="project-{{ $project['slug'] }}"
                                            href="{{ route('portal.dx.program.show', ['domainSlug' => $domain['slug'], 'subProgramSlug' => $project['program_slug']]) }}#project-{{ $project['slug'] }}"
                                        >
                                            <span class="dx-project-code">{{ $project['code'] }}</span>
                                            <span class="dx-project-name-wrap">
                                                <span class="dx-project-name">{{ $project['title'] }}</span>
                                                <span class="dx-project-program">{{ $project['program_title'] }}</span>
                                            </span>
                                            <i class="bi bi-arrow-up-right"></i>
                                        </a>
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    </div>
                @endif
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

