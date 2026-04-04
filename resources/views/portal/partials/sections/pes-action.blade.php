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
            @forelse ($pesInActionItems as $item)
                @php
                    $thumbnailSrc = $item->thumbnail_path
                        ? ((str_starts_with($item->thumbnail_path, '/') || str_starts_with($item->thumbnail_path, 'http'))
                            ? $item->thumbnail_path
                            : asset($item->thumbnail_path))
                        : null;
                @endphp
                <article class="pes-action-news-card">
                    @if ($item->link_url)
                        <a
                            class="pes-action-news-trigger"
                            href="{{ $item->link_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                    @else
                        <button
                            class="pes-action-news-trigger"
                            type="button"
                            data-bs-toggle="modal"
                            data-bs-target="#pesActionModal{{ $item->id }}"
                        >
                    @endif
                        <div class="pes-action-news-media accent-{{ $item->accent }}">
                            @if ($thumbnailSrc)
                                <img src="{{ $thumbnailSrc }}" alt="{{ $item->image_alt ?: $item->title }}" class="pes-action-news-image">
                            @else
                                <div class="pes-action-news-placeholder">
                                    <span>{{ $item->image_alt ?: $item->title }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="pes-action-news-body">
                            <div class="pes-action-news-eyebrow">{{ $item->eyebrow }}</div>
                            <h3 class="pes-action-news-title">{{ $item->title }}</h3>
                            <div class="pes-action-news-date">{{ optional($item->date)->format('F d, Y') }}</div>
                            <p class="pes-action-news-summary">{{ $item->summary }}</p>
                            <span class="pes-action-news-link">
                                {{ $item->link_url ? 'Open Article' : 'Read Full' }}
                                <i class="bi bi-chevron-right"></i>
                            </span>
                        </div>
                    @if ($item->link_url)
                        </a>
                    @else
                        </button>
                    @endif
                </article>
            @empty
                <div class="pes-action-empty-state">
                    <strong>No PES news yet.</strong>
                    <span>Stories published from the admin portal will appear here.</span>
                </div>
            @endforelse
        </div>
    </div>
</section>

@foreach ($pesInActionItems as $item)
    @php
        $thumbnailSrc = $item->thumbnail_path
            ? ((str_starts_with($item->thumbnail_path, '/') || str_starts_with($item->thumbnail_path, 'http'))
                ? $item->thumbnail_path
                : asset($item->thumbnail_path))
            : null;
    @endphp
    <div class="modal fade" id="pesActionModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content pes-action-modal">
                <div class="modal-body p-0">
                    <button class="btn-close pes-action-modal-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>

                    <div class="pes-action-modal-media accent-{{ $item->accent }}">
                        @if ($thumbnailSrc)
                            <img src="{{ $thumbnailSrc }}" alt="{{ $item->image_alt ?: $item->title }}" class="pes-action-modal-image">
                        @else
                            <div class="pes-action-modal-placeholder">
                                <span>{{ $item->image_alt ?: $item->title }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="pes-action-modal-content">
                        <div class="pes-action-modal-eyebrow">{{ $item->eyebrow }}</div>
                        <h3 class="pes-action-modal-title">{{ $item->title }}</h3>
                        <div class="pes-action-modal-date">{{ optional($item->date)->format('F d, Y') }}</div>
                        <p class="pes-action-modal-copy">{{ $item->content }}</p>
                        @if ($item->link_url)
                            <div class="mt-4">
                                <a class="btn btn-accent rounded-pill px-4" href="{{ $item->link_url }}" target="_blank" rel="noopener noreferrer">Open Full Article</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
