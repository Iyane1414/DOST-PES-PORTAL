<section class="materials-stop-shell" id="materials-stop-shell">
    <div class="materials-stop-stage" id="materials-stop-stage">
        <section class="section-space materials-directory-section" id="materials" data-scroll-scene="materials">
            <div class="container">
                <div class="materials-home-shell">
                    <div class="section-header position-relative">
                        <div class="materials-header-shell">
                            <div class="materials-header-copy">
                                <span class="materials-kicker">Knowledge Library</span>
                                <h2 class="section-title materials-title split-title">PES <span class="split-title-accent">Materials</span></h2>
                                <p class="section-copy materials-copy">Policies, reports, surveys, and presentations organized in one refined browsing space for PES references.</p>
                            </div>
                        </div>
                    </div>

                    <div class="materials-home-grid">
                        @foreach ($resourceCollections as $collection)
                            <a
                                id="{{ $collection['anchor'] }}"
                                class="materials-home-card"
                                href="{{ route('portal.resources.show', ['collectionSlug' => $collection['slug']]) }}"
                            >
                                <div class="materials-home-art materials-home-art-{{ $collection['artwork'] }}">
                                    <div class="materials-home-illustration">
                                        <div class="materials-home-orb orb-one"></div>
                                        <div class="materials-home-orb orb-two"></div>
                                        <div class="materials-home-orb orb-three"></div>
                                        <div class="materials-home-device">
                                            <i class="bi {{ $collection['icon'] }}"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="materials-home-body">
                                    <div class="materials-home-meta">
                                        <div class="materials-home-icon">
                                            <i class="bi {{ $collection['icon'] }}"></i>
                                        </div>
                                        <div class="materials-home-count">{{ str_pad((string) $collection['count'], 2, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                    <div class="materials-home-eyebrow">{{ $collection['eyebrow'] }}</div>
                                    <h3>{{ $collection['label'] }}</h3>
                                    <p>{{ $collection['description'] }}</p>
                                    <span class="materials-home-link">View Resources <i class="bi bi-arrow-right"></i></span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </div>
</section>
