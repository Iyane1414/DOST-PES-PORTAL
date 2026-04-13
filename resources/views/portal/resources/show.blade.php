@extends('layouts.app', ['title' => $title])

@section('body_class', 'portal-page portal-page-resources')
@section('page_theme', 'pes')

@section('content')
    <main class="materials-page-shell repository-page-shell">
        <section class="materials-page-hero repository-page-hero">
            <div class="container">
                <a class="materials-page-back repository-page-back" href="{{ route('portal.home') }}#materials">
                    <i class="bi bi-arrow-left"></i>
                    <span>Back to PES Materials</span>
                </a>

                <div class="materials-page-kicker repository-page-kicker">DOST PES Resources</div>
                <h1 class="materials-page-title repository-page-title">{{ $resourceCollection['label'] }}</h1>
                <div class="materials-page-count repository-page-count">{{ $totalMaterialsCount }} files found</div>
            </div>
        </section>

        <section class="materials-page-content repository-page-content">
            <div class="container">
                <div class="repository-page-toolbar-card">
                    <form class="materials-page-controls repository-page-controls" method="GET" action="{{ route('portal.resources.show', ['collectionSlug' => $resourceCollection['slug']]) }}">
                        <div class="materials-page-search-wrap repository-page-search-wrap">
                            <i class="bi bi-search"></i>
                            <input class="form-control materials-page-search repository-page-search" type="search" name="search" value="{{ $search }}" placeholder="Search {{ strtolower($resourceCollection['label']) }}...">
                        </div>

                        <div class="materials-page-year-wrap repository-page-year-wrap">
                            <label for="materials-year">Filter by year:</label>
                            <select id="materials-year" class="form-select materials-page-year repository-page-year" name="year" onchange="this.form.submit()">
                                <option value="All">All Years</option>
                                @foreach ($availableYears as $availableYear)
                                    <option value="{{ $availableYear }}" @selected($year === $availableYear)>{{ $availableYear }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    <div class="materials-page-status repository-page-status">
                        <strong>{{ $materialsCount }}</strong> files available
                    </div>
                </div>

                <div class="materials-page-results repository-page-results">
                    @forelse ($materials as $material)
                        @php
                            $extension = strtoupper(pathinfo(parse_url($material->url ?? '', PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'FILE');
                        @endphp
                        <article class="materials-page-file repository-page-item">
                            <div class="materials-page-file-main repository-page-item-main">
                                <div class="materials-page-file-top repository-page-item-top">
                                    <span class="badge-soft">{{ $material->type }}</span>
                                    <span class="materials-page-file-ext repository-page-item-ext">{{ $extension }}</span>
                                </div>
                                <h2>{{ $material->title }}</h2>
                                <p>Published by {{ $material->division }} for PES reference and resource browsing.</p>
                                <div class="materials-page-file-meta repository-page-item-meta">
                                    <span><i class="bi bi-building me-2"></i>{{ $material->division }}</span>
                                    <span><i class="bi bi-calendar3 me-2"></i>{{ optional($material->date)->format('F d, Y') }}</span>
                                </div>
                            </div>
                            <div class="materials-page-file-actions repository-page-item-actions">
                                <a class="btn btn-outline-dark rounded-pill px-4" href="{{ $material->url ?: '#' }}" target="_blank" rel="noreferrer">Open File</a>
                                <a class="btn btn-accent rounded-pill px-4" href="{{ $material->url ?: '#' }}" download rel="noreferrer">Download</a>
                            </div>
                        </article>
                    @empty
                        <div class="materials-page-empty repository-page-empty">
                            <div class="materials-page-empty-icon repository-page-empty-icon"><i class="bi bi-file-earmark"></i></div>
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
