@extends('layouts.app', ['title' => $title])

@section('content')
    @include('portal.partials.navigation')

    <main class="materials-page-shell">
        <section class="materials-page-hero">
            <div class="container">
                <a class="materials-page-back" href="{{ route('portal.home') }}#materials">
                    <i class="bi bi-arrow-left"></i>
                    <span>Back to PES Materials</span>
                </a>

                <div class="materials-page-kicker">DOST PES Resources</div>
                <h1 class="materials-page-title">{{ $resourceCollection['label'] }}</h1>
                <div class="materials-page-count">{{ $totalMaterialsCount }} files found</div>
            </div>
        </section>

        <section class="materials-page-content">
            <div class="container">
                <form class="materials-page-controls" method="GET" action="{{ route('portal.resources.show', ['collectionSlug' => $resourceCollection['slug']]) }}">
                    <div class="materials-page-search-wrap">
                        <i class="bi bi-search"></i>
                        <input class="form-control materials-page-search" type="search" name="search" value="{{ $search }}" placeholder="Search {{ strtolower($resourceCollection['label']) }}...">
                    </div>

                    <div class="materials-page-year-wrap">
                        <label for="materials-year">Filter by year:</label>
                        <select id="materials-year" class="form-select materials-page-year" name="year" onchange="this.form.submit()">
                            <option value="All">All Years</option>
                            @foreach ($availableYears as $availableYear)
                                <option value="{{ $availableYear }}" @selected($year === $availableYear)>{{ $availableYear }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <div class="materials-page-status">
                    <strong>{{ $materialsCount }}</strong> files available
                </div>

                <div class="materials-page-results">
                    @forelse ($materials as $material)
                        @php
                            $extension = strtoupper(pathinfo(parse_url($material->url ?? '', PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'FILE');
                        @endphp
                        <article class="materials-page-file">
                            <div class="materials-page-file-main">
                                <div class="materials-page-file-top">
                                    <span class="badge-soft">{{ $material->type }}</span>
                                    <span class="materials-page-file-ext">{{ $extension }}</span>
                                </div>
                                <h2>{{ $material->title }}</h2>
                                <p>Published by {{ $material->division }} for PES reference and resource browsing.</p>
                                <div class="materials-page-file-meta">
                                    <span><i class="bi bi-building me-2"></i>{{ $material->division }}</span>
                                    <span><i class="bi bi-calendar3 me-2"></i>{{ optional($material->date)->format('F d, Y') }}</span>
                                </div>
                            </div>
                            <div class="materials-page-file-actions">
                                <a class="btn btn-outline-dark rounded-pill px-4" href="{{ $material->url ?: '#' }}" target="_blank" rel="noreferrer">Preview</a>
                                <a class="btn btn-accent rounded-pill px-4" href="{{ $material->url ?: '#' }}" target="_blank" rel="noreferrer">Open File</a>
                            </div>
                        </article>
                    @empty
                        <div class="materials-page-empty">
                            <div class="materials-page-empty-icon"><i class="bi bi-file-earmark"></i></div>
                            <h3>No files yet</h3>
                            <p>{{ $resourceCollection['page_copy'] }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </main>

    @include('portal.partials.footer')
@endsection
