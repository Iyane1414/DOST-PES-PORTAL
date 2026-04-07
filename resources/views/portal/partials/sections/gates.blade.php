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

                <div class="gates-projects-section">
                    <div class="gates-projects-shell" id="gates-projects">
                        <div class="gates-projects-head">
                            <div>
                                <span class="eyebrow">Project Library</span>
                                <h3 class="section-title split-title mb-2">Browse by <span class="split-title-accent">Category</span></h3>
                                <p class="section-copy mb-0">Explore GATES projects and video presentations organized by category.</p>
                            </div>
                        </div>

                        <div class="gates-collection-grid">
                            @php
                                $projectsCount = $gatesProjects->filter(fn($p) => strtolower($p->type ?? '') === 'project')->count();
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
    </div>
</section>
