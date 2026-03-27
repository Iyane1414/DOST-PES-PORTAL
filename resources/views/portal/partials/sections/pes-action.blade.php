<section class="section-space pes-action-section" id="pes-action" data-scroll-scene="news">
    <div class="container">
        <div class="pes-action-heading">
            <span class="pes-action-kicker">More News</span>
            <div class="pes-action-heading-copy">
                <h2 class="section-title split-title">PES <span class="split-title-accent">in Action</span></h2>
                <p class="section-copy">Stay updated with the latest stories, events, and service highlights from DOST PES.</p>
            </div>
        </div>

        <div class="pes-action-grid">
            @foreach ($pesInActionItems as $item)
                <article class="pes-action-news-card">
                    <button
                        class="pes-action-news-trigger"
                        type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#pesActionModal{{ $item['id'] }}"
                    >
                        <div class="pes-action-news-media accent-{{ $item['accent'] }}">
                            @if (! empty($item['image']))
                                <img src="{{ asset($item['image']) }}" alt="{{ $item['image_alt'] }}" class="pes-action-news-image">
                            @else
                                <div class="pes-action-news-placeholder">
                                    <span>{{ $item['image_alt'] }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="pes-action-news-body">
                            <div class="pes-action-news-eyebrow">{{ $item['eyebrow'] }}</div>
                            <h3 class="pes-action-news-title">{{ $item['title'] }}</h3>
                            <div class="pes-action-news-date">{{ $item['date'] }}</div>
                            <span class="pes-action-news-link">
                                Read Full
                                <i class="bi bi-chevron-right"></i>
                            </span>
                        </div>
                    </button>
                </article>
            @endforeach
        </div>
    </div>
</section>

@foreach ($pesInActionItems as $item)
    <div class="modal fade" id="pesActionModal{{ $item['id'] }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content pes-action-modal">
                <div class="modal-body p-0">
                    <button class="btn-close pes-action-modal-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>

                    <div class="pes-action-modal-media accent-{{ $item['accent'] }}">
                        @if (! empty($item['image']))
                            <img src="{{ asset($item['image']) }}" alt="{{ $item['image_alt'] }}" class="pes-action-modal-image">
                        @else
                            <div class="pes-action-modal-placeholder">
                                <span>{{ $item['image_alt'] }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="pes-action-modal-content">
                        <div class="pes-action-modal-eyebrow">{{ $item['eyebrow'] }}</div>
                        <h3 class="pes-action-modal-title">{{ $item['title'] }}</h3>
                        <div class="pes-action-modal-date">{{ $item['date'] }}</div>
                        <p class="pes-action-modal-copy">{{ $item['copy'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
