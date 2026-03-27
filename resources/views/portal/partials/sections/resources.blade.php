<section class="section-space resources-section" id="resources">
    <div class="container">
        <div class="resources-shell">
            <div class="section-header d-flex flex-column flex-xl-row justify-content-between align-items-xl-end gap-3">
                <div>
                    <h2 class="section-title split-title">PES<br><span class="split-title-accent">Resources</span></h2>
                    <p class="section-copy">Policies, annual reports, R&amp;D survey references, and presentation materials in one cleaner library.</p>
                </div>
                <div class="resources-spotlight">
                    <span class="badge-soft">Resource Hub</span>
                    <h3 class="h4 mb-2">Built for quick access</h3>
                    <p class="mb-0 text-secondary-soft">Use these placeholder records for now, then swap in actual files when the documents are ready for publishing.</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12 col-xl-5">
                    <div class="feature-card resources-feature-card h-100">
                        <div class="resources-feature-kicker">Featured Collection</div>
                        <h3 class="resources-feature-title">Document groups that make PES outputs easier to find.</h3>
                        <p class="resources-feature-copy">Organize public-facing references by document type so visitors can go directly to policy references, annual reporting, survey outputs, and presentation materials without guessing where to start.</p>
                        <div class="resources-feature-grid">
                            <div class="resources-feature-item" id="resources-policies">
                                <span>Policies</span>
                                <strong>Governance, planning, and compliance references</strong>
                            </div>
                            <div class="resources-feature-item" id="resources-annual-report">
                                <span>Annual Reports</span>
                                <strong>Institutional progress, accomplishments, and highlights</strong>
                            </div>
                            <div class="resources-feature-item" id="resources-rd-survey">
                                <span>R&amp;D Survey</span>
                                <strong>Research and development snapshots and survey outputs</strong>
                            </div>
                            <div class="resources-feature-item" id="resources-presentations">
                                <span>Presentations</span>
                                <strong>Decks for planning, evaluation, and strategic sessions</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-7">
                    <div class="row g-4">
                        @foreach ($resourceHighlights as $resource)
                            <div class="col-md-6">
                                <article class="resource-card resources-library-card h-100">
                                    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                                        <div class="resources-icon-chip">
                                            <i class="bi {{ $resource['icon'] }}"></i>
                                        </div>
                                        <span class="badge-soft resources-badge">{{ $resource['type'] }}</span>
                                    </div>
                                    <h3 class="resources-card-title">{{ $resource['title'] }}</h3>
                                    <p class="resources-card-copy">{{ $resource['summary'] }}</p>
                                    <div class="resources-card-meta">{{ $resource['division'] }} <span>&bull;</span> {{ $resource['date'] }}</div>
                                    <div class="d-flex align-items-center justify-content-between gap-3">
                                        <a class="resources-card-link" href="{{ $resource['url'] }}">Open Resource</a>
                                        <button class="btn btn-sm btn-outline-dark rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#resourceModal{{ $loop->index }}">Quick View</button>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach ($resourceHighlights as $resource)
        <div class="modal fade" id="resourceModal{{ $loop->index }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content issuance-modal">
                    <div class="modal-header border-0 pb-0">
                        <div>
                            <span class="badge-soft">{{ $resource['type'] }}</span>
                            <h3 class="h2 mt-3 mb-0">{{ $resource['title'] }}</h3>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <div class="issuance-modal-card">
                            <p class="mb-3">{{ $resource['summary'] }}</p>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="issuance-meta">
                                        <span>Office</span>
                                        <strong>{{ $resource['division'] }}</strong>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="issuance-meta">
                                        <span>Release</span>
                                        <strong>{{ $resource['date'] }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <a class="btn btn-accent rounded-pill px-4" href="{{ $resource['url'] }}">Open Resource</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</section>
