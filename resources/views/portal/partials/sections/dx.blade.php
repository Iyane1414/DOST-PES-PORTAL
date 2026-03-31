<section class="section-space dx-section" id="dost-dx" data-scroll-scene="dx">
    @php
        $coreDomains = collect([
            [
                'key' => 'people',
                'title' => 'People',
                'icon' => 'bi-person',
                'image' => 'images/people.png',
                'description' => 'Individuals within the organization, their skills, knowledge, and how they interact with processes and technology including organizational structures.',
            ],
            [
                'key' => 'process',
                'title' => 'Process',
                'icon' => 'bi-activity',
                'image' => 'images/process.png',
                'description' => 'Encompasses the workflows, procedures, and methodologies used to complete tasks and achieve goals.',
            ],
            [
                'key' => 'technology',
                'title' => 'Technology',
                'icon' => 'bi-pc-display',
                'image' => 'images/technology.png',
                'description' => 'Infrastructure, tools, information systems, and software used to support and enhance processes and the work of individuals.',
            ],
        ]);

        $subProgramCards = collect([
            [
                'domain' => 'people',
                'domain_label' => 'People',
                'title' => 'Structure Rationalization',
                'code_theme' => 'amber',
                'items' => [
                    ['code' => 'SRZ', 'label' => 'Structure & Organization'],
                    ['code' => 'SRZ', 'label' => 'Structure Optimization'],
                    ['code' => 'SRZ', 'label' => 'New Units'],
                    ['code' => 'SRZ', 'label' => 'ePMO Establishment'],
                    ['code' => 'SRZ', 'label' => 'Foresight Unit Creation'],
                    ['code' => 'SRZ', 'label' => 'NT (New Technology) Unit Creation'],
                    ['code' => 'PMT', 'label' => 'Core Team Creation & Chartering'],
                    ['code' => 'PMT', 'label' => 'DX Organization Chartering'],
                    ['code' => 'PES', 'label' => 'Planning'],
                    ['code' => 'PES', 'label' => 'Budgeting'],
                    ['code' => 'PES', 'label' => 'MERC'],
                    ['code' => 'PES', 'label' => 'Impact Assessment'],
                ],
            ],
            [
                'domain' => 'technology',
                'domain_label' => 'Technology',
                'title' => 'Cybersecurity',
                'code_theme' => 'cyan',
                'items' => [
                    ['code' => 'CSP', 'label' => 'Cybersecurity 101'],
                    ['code' => 'CSP', 'label' => 'Information Radiators'],
                    ['code' => 'CSP', 'label' => 'Use of VPN'],
                    ['code' => 'CSP', 'label' => 'Zero-trust'],
                    ['code' => 'CSP', 'label' => '2-Factor Authentication'],
                    ['code' => 'CSP', 'label' => 'Admin Privileges'],
                    ['code' => 'CSP', 'label' => 'USB Disabled'],
                    ['code' => 'CSP', 'label' => 'CISO'],
                    ['code' => 'CSP', 'label' => 'Ethical Hacking'],
                    ['code' => 'CSP', 'label' => 'Versim'],
                    ['code' => 'CSP', 'label' => 'SCF'],
                    ['code' => 'CSP', 'label' => 'ISO 27001'],
                    ['code' => 'CSP', 'label' => 'NSOC'],
                    ['code' => 'CSP', 'label' => 'Reporting'],
                    ['code' => 'CSP', 'label' => '321 Back-up'],
                    ['code' => 'CSP', 'label' => 'Email Hosting'],
                    ['code' => 'CSP', 'label' => 'DOST IM'],
                    ['code' => 'CSP', 'label' => 'DOST Encryption Code'],
                    ['code' => 'CSP', 'label' => 'Digital Signature'],
                    ['code' => 'CSP', 'label' => 'VPN Implementation'],
                    ['code' => 'CSP', 'label' => 'AD Implementation'],
                    ['code' => 'CSP', 'label' => 'SSO Implementation'],
                    ['code' => 'CSP', 'label' => 'InfoSec'],
                    ['code' => 'CSP', 'label' => 'ICT Policies'],
                    ['code' => 'CSP', 'label' => 'Data Privacy'],
                    ['code' => 'CSP', 'label' => 'Password Policy'],
                    ['code' => 'CSP', 'label' => 'ICT Usage'],
                    ['code' => 'CSP', 'label' => 'Identity and Access Management'],
                    ['code' => 'PMT', 'label' => 'Communications Plan'],
                ],
            ],
            [
                'domain' => 'technology',
                'domain_label' => 'Technology',
                'title' => 'IS Harmonization',
                'code_theme' => 'green',
                'items' => [
                    ['code' => 'ISH', 'label' => 'DEPMIS'],
                    ['code' => 'ISH', 'label' => 'Integrations'],
                    ['code' => 'ISH', 'label' => 'IS Harmonization'],
                    ['code' => 'ISH', 'label' => 'In-depth Analysis'],
                    ['code' => 'ISH', 'label' => 'IS Ranking'],
                    ['code' => 'ISH', 'label' => 'Phase 1 Harmonization'],
                    ['code' => 'ISH', 'label' => 'Phase 2 Integrated IS Development'],
                    ['code' => 'ISH', 'label' => 'Harmonized iHRMIS'],
                    ['code' => 'ISH', 'label' => 'Integrated eULIMS (now iLab)'],
                    ['code' => 'ISH', 'label' => 'KM Portal'],
                    ['code' => 'ISH', 'label' => 'DOST Portal'],
                    ['code' => 'ISH', 'label' => 'Monitoring and Analytics Standard Tools'],
                    ['code' => 'ISH', 'label' => 'DaaS'],
                    ['code' => 'ISH', 'label' => 'Executive Information System (EIS)'],
                ],
            ],
            [
                'domain' => 'technology',
                'domain_label' => 'Technology',
                'title' => 'Infra Harmonization',
                'code_theme' => 'blue',
                'items' => [
                    ['code' => 'INH', 'label' => 'DOST Primary Connectivity'],
                    ['code' => 'INH', 'label' => 'DOST Integrated Cloud'],
                    ['code' => 'INH', 'label' => 'Smart Workplace'],
                    ['code' => 'INH', 'label' => 'Central Repository'],
                    ['code' => 'INH', 'label' => 'DOST Cloud Production'],
                    ['code' => 'INH', 'label' => 'DOST DC1'],
                    ['code' => 'INH', 'label' => 'DOST DC2'],
                    ['code' => 'INH', 'label' => 'Tools Standardization'],
                    ['code' => 'ISS', 'label' => 'OneISSP'],
                ],
            ],
            [
                'domain' => 'technology',
                'domain_label' => 'Technology',
                'title' => 'I.T. Governance',
                'code_theme' => 'ice',
                'items' => [
                    ['code' => 'GOV', 'label' => 'OPM3'],
                    ['code' => 'GOV', 'label' => 'Global PM Training'],
                    ['code' => 'GOV', 'label' => 'Prioritization Model'],
                    ['code' => 'GOV', 'label' => 'Agency & Regional Offices Engagement'],
                    ['code' => 'GOV', 'label' => 'SecSta'],
                    ['code' => 'GOV', 'label' => 'LnD Planning System'],
                    ['code' => 'GOV', 'label' => 'DOST Software Development Pack'],
                    ['code' => 'GOV', 'label' => 'PSCM'],
                    ['code' => 'GOV', 'label' => 'ITSM'],
                    ['code' => 'GOV', 'label' => 'Productivity Analysis'],
                    ['code' => 'GOV', 'label' => 'Knowledge Management'],
                    ['code' => 'PES', 'label' => 'SPMS'],
                    ['code' => 'PES', 'label' => 'NSTIP Development'],
                    ['code' => 'PES', 'label' => 'Planning Officers Capacity Building'],
                    ['code' => 'PES', 'label' => 'Organizational Development Plan'],
                ],
            ],
            [
                'domain' => 'process',
                'domain_label' => 'Process',
                'title' => 'Process Harmonization',
                'code_theme' => 'violet',
                'items' => [
                    ['code' => 'PRH', 'label' => 'Enhanced Proposal Process'],
                    ['code' => 'PRH', 'label' => 'Project Management Process'],
                    ['code' => 'PRH', 'label' => 'Project Change Management'],
                    ['code' => 'PRH', 'label' => 'Tech Transfer Framework'],
                    ['code' => 'PRH', 'label' => 'Process Mapping'],
                    ['code' => 'PRH', 'label' => 'Configuration Management'],
                    ['code' => 'PRH', 'label' => 'Change Management'],
                    ['code' => 'PRH', 'label' => 'Resource Management'],
                    ['code' => 'PRH', 'label' => 'Capacity Management'],
                    ['code' => 'PRH', 'label' => 'Asset Lifecycle Management'],
                    ['code' => 'PRH', 'label' => 'Foresight Framework Development'],
                    ['code' => 'PRH', 'label' => 'TOGAF'],
                    ['code' => 'PRH', 'label' => 'Enterprise Architecture Development'],
                    ['code' => 'PRH', 'label' => 'DOST PMM'],
                    ['code' => 'PRH', 'label' => 'QMS'],
                    ['code' => 'PRH', 'label' => 'PQA'],
                    ['code' => 'PRH', 'label' => 'CMMI'],
                    ['code' => 'PRH', 'label' => 'Curriculum Based Learning'],
                    ['code' => 'PRH', 'label' => 'Competency Based Development'],
                ],
            ],
        ]);
        $subProgramItemCount = $subProgramCards->sum(fn ($group) => count($group['items']));
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
                <div class="dx-domain-grid">
                    @forelse ($coreDomains as $item)
                        <article class="dx-domain-card" role="button" tabindex="0" onclick="dxGoToSubProgram('{{ $item['key'] }}')" onkeydown="if(event.key === 'Enter' || event.key === ' '){ event.preventDefault(); dxGoToSubProgram('{{ $item['key'] }}'); }">
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
                    @forelse ($subProgramCards as $item)
                        <article class="dx-sub-card dx-sub-card-group" data-domain="{{ $item['domain'] }}" data-theme="{{ $item['code_theme'] }}">
                            <div class="dx-program-domain-group">{{ strtoupper($item['domain_label']) }}</div>
                            <h3 class="dx-program-title">{{ $item['title'] }}</h3>
                            <ul class="dx-program-list">
                                @foreach ($item['items'] as $program)
                                    <li class="dx-program-list-item">
                                        <span class="dx-program-code">{{ $program['code'] }}</span>
                                        <span class="dx-program-text">{{ $program['label'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </article>
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
                    <strong class="dx-metric-number" data-target="{{ $subProgramItemCount }}">0</strong>
                    <span>Sub-programs</span>
                </article>
                <article class="dx-metric-card">
                    <strong class="dx-metric-number" data-target="132">0</strong>
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
