<section class="section-space dx-section" id="dost-dx" data-scroll-scene="dx">
    @php
        $coreDomains = collect([
            [
                'title' => 'People',
                'description' => 'Individuals within the organization, their skills, knowledge, and how they interact with processes and technology including organizational structures.',
            ],
            [
                'title' => 'Process',
                'description' => 'Encompasses the workflows, procedures, and methodologies used to complete tasks and achieve goals.',
            ],
            [
                'title' => 'Technology',
                'description' => 'Infrastructure, tools, information systems, and software used to support and enhance processes and the work of individuals.',
            ],
        ]);
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
                            <img src="{{ asset('images/p1.png') }}" alt="DOST DX team" class="dx-hero-photo">
                        </div>
                        <div class="dx-hero-badge"><i class="bi bi-lightning-charge-fill"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <section class="dx-roadmap" id="dx-roadmap">
            <div class="dx-roadmap-header">
                <div>
                    <span class="eyebrow text-accent">Implementation Path</span>
                    <h3 class="section-title split-title text-white mb-3">DOST DX<br><span class="split-title-accent">Roadmap</span></h3>
                    <p class="section-copy text-white-50 mb-0">A phased progression for building interoperable platforms, institutional capability, and measurable public-sector digital outcomes.</p>
                </div>
            </div>
            <div class="dx-roadmap-track">
                <article class="dx-roadmap-card">
                    <div class="dx-roadmap-phase">Phase 01</div>
                    <div class="dx-roadmap-year">2025</div>
                    <h4 class="dx-roadmap-title">Foundation and Readiness</h4>
                    <p class="dx-roadmap-copy">Establish governance, baseline systems, policy alignment, and internal readiness for digital transformation execution.</p>
                    <ul class="dx-roadmap-list">
                        <li>DX governance framework activated</li>
                        <li>System inventory and maturity assessment completed</li>
                        <li>Priority services identified for digitization</li>
                    </ul>
                </article>
                <article class="dx-roadmap-card">
                    <div class="dx-roadmap-phase">Phase 02</div>
                    <div class="dx-roadmap-year">2026</div>
                    <h4 class="dx-roadmap-title">Integration and Enablement</h4>
                    <p class="dx-roadmap-copy">Connect platforms, improve internal workflows, and enable interoperable data exchange across DOST units.</p>
                    <ul class="dx-roadmap-list">
                        <li>Shared services and APIs introduced</li>
                        <li>Core systems integration roadmap launched</li>
                        <li>Digital capability building scaled across teams</li>
                    </ul>
                </article>
                <article class="dx-roadmap-card">
                    <div class="dx-roadmap-phase">Phase 03</div>
                    <div class="dx-roadmap-year">2027</div>
                    <h4 class="dx-roadmap-title">Optimization and Intelligence</h4>
                    <p class="dx-roadmap-copy">Refine delivery performance through analytics, automation, and experience-led service design.</p>
                    <ul class="dx-roadmap-list">
                        <li>Performance dashboards operationalized</li>
                        <li>Automation workflows deployed in high-volume processes</li>
                        <li>Citizen-centric digital touchpoints improved</li>
                    </ul>
                </article>
                <article class="dx-roadmap-card">
                    <div class="dx-roadmap-phase">Phase 04</div>
                    <div class="dx-roadmap-year">2028</div>
                    <h4 class="dx-roadmap-title">Unified Government Model</h4>
                    <p class="dx-roadmap-copy">Demonstrate a scalable government-tech model that other agencies can replicate through DOST DX.</p>
                    <ul class="dx-roadmap-list">
                        <li>Interoperable service architecture matured</li>
                        <li>Cross-agency model documented for replication</li>
                        <li>Continuous innovation and governance embedded</li>
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
                <div class="row g-4">
                    @forelse ($coreDomains as $item)
                        <div class="col-md-6 col-xl-4">
                            <article class="dx-domain-card">
                                <div class="dx-domain-media">
                                    <div class="dx-card-placeholder">Domain Photo</div>
                                </div>
                                <div class="dx-domain-overlay"></div>
                                <div class="dx-domain-body">
                                    <h3 class="dx-domain-title">{{ $item['title'] }}</h3>
                                    <p class="dx-domain-copy mb-0">{{ $item['description'] }}</p>
                                </div>
                            </article>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="dx-empty-state">No core domains available yet.</div>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="dx-panel" data-dx-panel="programs" hidden>
                <div class="row g-4">
                    @forelse ($dxPrograms as $item)
                        <div class="col-md-6 col-xl-4">
                            <article class="dx-program-tile">
                                <div class="dx-program-media">
                                    <div class="dx-card-placeholder">Program Photo</div>
                                </div>
                                <div class="dx-program-body">
                                    <div class="program-index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                                    <h3 class="dx-program-title">{{ $item->title }}</h3>
                                    <p class="text-white-50 mb-0">{{ $item->description }}</p>
                                </div>
                            </article>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="dx-empty-state">No sub-programs available yet.</div>
                        </div>
                    @endforelse
                </div>
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
