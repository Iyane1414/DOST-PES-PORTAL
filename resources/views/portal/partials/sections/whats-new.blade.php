<section class="section-space" id="whats-new">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-end gap-3">
            <div>
                <h2 class="section-title split-title">What's<br><span class="split-title-accent">New</span></h2>
                <p class="section-copy">Stay updated with the latest from PES.</p>
            </div>
            @if ($latestItems->count() > 1)
                <button
                    class="btn whats-new-next-btn rounded-pill"
                    type="button"
                    data-bs-target="#whatsNewCarousel"
                    data-bs-slide="next"
                    aria-label="Next What's New item"
                >
                    <span>Next</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            @endif
        </div>
        <div id="whatsNewCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach ($latestItems as $item)
                    <div class="carousel-item @if($loop->first) active @endif">
                        <div class="feature-card feature-carousel-card">
                            <div>
                                <span class="badge-soft">{{ $item['label'] }}</span>
                                <h3 class="h2 mt-3 mb-2">{{ $item['title'] }}</h3>
                            </div>
                            <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center">
                                <span class="text-secondary-soft">{{ $item['date'] }}</span>
                                <a class="btn btn-accent rounded-pill px-4" href="{{ $item['url'] }}">View Details</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
