<section class="section-space" id="whats-new">
    <div class="container">
        <div class="whats-new-shell">
            <div class="whats-new-orb whats-new-orb-left"></div>
            <div class="whats-new-orb whats-new-orb-right"></div>

            <div class="section-header whats-new-header">
                <div>
                    <div class="whats-new-kicker">Live Updates</div>
                    <h2 class="section-title split-title">What's <span class="split-title-accent">New</span></h2>
                    <p class="section-copy mb-0">Fresh issuances and resources moving across the portal in real time.</p>
                </div>
            </div>

            <div class="whats-new-marquee" aria-label="Latest portal updates">
                <div class="whats-new-track">
                    @foreach ($latestItems->concat($latestItems) as $item)
                        <a class="whats-new-card" href="{{ $item['url'] }}">
                            <span class="whats-new-card-type">{{ $item['label'] }}</span>
                            <h3>{{ $item['title'] }}</h3>
                            <div class="whats-new-card-meta">
                                <span>{{ $item['date'] }}</span>
                                <span class="whats-new-card-link">View <i class="bi bi-arrow-up-right"></i></span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
