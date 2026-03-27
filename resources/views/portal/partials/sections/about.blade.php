@php
    $aboutDivisions = $divisions->take(4)->values();
@endphp

<section class="about-section" id="about">
    <div class="about-grid"></div>
    <div class="about-glow"></div>

    <section class="about-intro-panel">
        <div class="container about-panel-container">
            <div class="about-hero-layout">
                <div class="about-hero-copy">
                    <div class="about-kicker-wrap">
                        <span class="about-line"></span>
                        <span class="about-kicker">Department of Science and Technology</span>
                    </div>
                    <h2 class="about-title">About<br><span>DOST<br>PES</span></h2>
                    <p class="about-copy">The Department of Science and Technology - Planning and Evaluation Services (DOST PES) is dedicated to providing efficient and effective services related to planning, programs, and project monitoring and development.</p>
                </div>

                <div class="about-scroll-cue" aria-hidden="true">
                    <span>Scroll</span>
                    <div class="about-scroll-arrow-stack">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="about-horizontal-shell" id="about-horizontal-shell">
        <div class="about-horizontal-stage" id="about-horizontal-stage">
            <div class="about-horizontal-track" id="about-horizontal-track">
                <section class="about-panel about-panel-mandate" id="mandate" data-about-panel="mandate">
                    <div class="container about-panel-container">
                        <div class="about-card about-card-mandate">
                            <div class="about-card-copy">
                                <div class="about-panel-number">01</div>
                                <div class="about-chip-row">
                                    <span class="about-chip">Legal Basis</span>
                                    <span class="about-chip">Executive Order 128</span>
                                </div>
                                <h2 class="about-panel-title">Our<br><span>Mandate</span></h2>
                                <p class="about-panel-body">Established under <strong>EO 128</strong>, the Planning and Evaluation Services (PES) shall be responsible for providing the Authority with efficient and effective services relating to <strong>planning, programs and project monitoring and development</strong>.</p>

                                <div class="about-feature-grid">
                                    <article class="about-feature-card">
                                        <div class="about-feature-icon">
                                            <i class="bi bi-activity"></i>
                                        </div>
                                        <h3>Strategic Planning</h3>
                                        <p>Formulating the DOST roadmap for the future.</p>
                                    </article>
                                    <article class="about-feature-card">
                                        <div class="about-feature-icon">
                                            <i class="bi bi-shield-check"></i>
                                        </div>
                                        <h3>Impact Evaluation</h3>
                                        <p>Measuring the success of S&amp;T initiatives.</p>
                                    </article>
                                </div>
                            </div>

                            <div class="about-card-showcase">
                                <div class="about-showcase-badge">PES</div>
                                <div class="about-showcase-wordmark">DOST</div>
                                <div class="about-showcase-copy">Planning &amp;<br>Evaluation <span>Service</span></div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="about-panel about-panel-organization" id="organizational-structure" data-about-panel="organization">
                    <div class="container about-panel-container">
                        <div class="about-card about-card-organization">
                            <div class="about-org-copy">
                                <div class="about-panel-number">02</div>
                                <span class="about-chip">Structure</span>
                                <h2 class="about-panel-title">Our<br><span>Organization</span></h2>
                                <p class="about-panel-body">PES is composed of four divisions working in concert to fulfill its mandate.</p>
                            </div>

                            <div class="about-org-list">
                                @forelse ($aboutDivisions as $division)
                                    <button
                                        class="about-org-item"
                                        type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#divisionModal{{ $division->id }}"
                                    >
                                        <span class="about-org-index">{{ $loop->iteration }}</span>
                                        <span class="about-org-text">
                                            <span class="about-org-name">{{ $division->name }}</span>
                                            <span class="about-org-abbr">{{ $division->abbr }}</span>
                                        </span>
                                        <i class="bi bi-arrow-up-right"></i>
                                    </button>
                                @empty
                                    <div class="about-org-empty">Division records will appear here once they are added in the admin dashboard.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <div class="container about-mobile-scroll">
        <a class="about-scroll" href="#pes-action">
            <span>Continue to PES in Action</span>
            <span class="about-scroll-arrows"><i class="bi bi-chevron-down"></i><i class="bi bi-chevron-down"></i></span>
        </a>
    </div>
</section>

@foreach ($aboutDivisions as $division)
    <div class="modal fade" id="divisionModal{{ $division->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content issuance-modal about-division-modal">
                <div class="modal-body p-4 p-lg-5">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <div class="about-division-modal-eyebrow">{{ $division->abbr }}</div>
                            <h3 class="h2 mb-1">{{ $division->name }}</h3>
                        </div>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="issuance-modal-card">
                        <div class="about-division-modal-copy">
                            {{ $division->description }}
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button class="btn btn-outline-secondary rounded-pill px-4" type="button" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
