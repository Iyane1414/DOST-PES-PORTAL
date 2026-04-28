@extends('layouts.app', ['title' => $title])

@section('body_class', 'portal-page portal-page-gates')
@section('page_theme', 'gates')

@section('content')
    <main class="gates-page-shell repository-page-shell">
        <section class="gates-page-hero repository-page-hero">
            <div class="container">
                <a class="gates-page-back repository-page-back" href="{{ route('portal.home') }}#dost-gates">
                    <i class="bi bi-arrow-left"></i>
                    <span>Back to GATES Project 1</span>
                </a>

                <div class="gates-page-kicker repository-page-kicker">DOST GATES Project 1</div>
                <h1 class="gates-page-title repository-page-title">{{ $gatesCollection['label'] }}</h1>
                <div class="gates-page-count repository-page-count">{{ $totalGatesProjectsCount }} items found</div>
                <p class="gates-page-description repository-page-description">{{ $gatesCollection['description'] }}</p>
            </div>
        </section>

        <section class="gates-page-content repository-page-content">
            <div class="container">
                <div class="repository-page-toolbar-card">
                    <form class="gates-page-controls repository-page-controls" method="GET" action="{{ route('portal.gates.show', ['collectionSlug' => $gatesCollection['slug']]) }}" data-gates-search-form>
                        <div class="gates-page-search-wrap repository-page-search-wrap">
                            <i class="bi bi-search"></i>
                            <input class="form-control gates-page-search repository-page-search" type="search" name="search" value="{{ $search }}" placeholder="Search {{ strtolower($gatesCollection['label']) }}..." data-gates-search-input>
                        </div>

                        <div class="gates-page-year-wrap repository-page-year-wrap">
                            <label for="gates-year">Filter by year:</label>
                            <select id="gates-year" class="form-select gates-page-year repository-page-year" name="year" onchange="this.form.submit()">
                                <option value="All">All Years</option>
                                @foreach ($availableYears as $availableYear)
                                    <option value="{{ $availableYear }}" @selected($year === $availableYear)>{{ $availableYear }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    <div class="gates-page-status repository-page-status">
                        <strong data-gates-search-count>{{ $gatesProjectsCount }}</strong> items available
                    </div>
                </div>

                <div class="gates-page-results repository-page-results">
                    @forelse ($gatesProjects as $project)
                        @php
                            $extension = strtoupper(pathinfo(parse_url($project->url ?? '', PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'FILE');
                            $isVideo = $project->type === 'Video Presentation';
                        @endphp
                        <article class="gates-page-item repository-page-item" data-gates-search-row="{{ strtolower($project->title . ' ' . ($project->code ?? '') . ' ' . ($project->description ?? '')) }}">
                            <div class="gates-page-item-main repository-page-item-main">
                                <div class="gates-page-item-top repository-page-item-top">
                                    <span class="badge-soft">{{ $project->type }}</span>
                                    @if (!$isVideo)
                                        <span class="gates-page-item-ext repository-page-item-ext">{{ $extension }}</span>
                                    @else
                                        <span class="gates-page-item-ext repository-page-item-ext">VIDEO</span>
                                    @endif
                                </div>
                                <h2>{{ $project->title }}</h2>
                                <p>{{ $project->description }}</p>
                                @if ($project->code)
                                    <div class="gates-page-item-meta repository-page-item-meta">
                                        <span><i class="bi bi-hash me-2"></i>{{ $project->code }}</span>
                                        @if ($project->date)
                                            <span><i class="bi bi-calendar3 me-2"></i>{{ optional($project->date)->format('F d, Y') }}</span>
                                        @endif
                                    </div>
                                @elseif ($project->date)
                                    <div class="gates-page-item-meta repository-page-item-meta">
                                        <span><i class="bi bi-calendar3 me-2"></i>{{ optional($project->date)->format('F d, Y') }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="gates-page-item-actions repository-page-item-actions">
                                <a class="btn btn-outline-dark rounded-pill px-4" href="{{ $project->url ?: '#' }}" target="_blank" rel="noreferrer">Open File</a>
                                <a class="btn btn-accent rounded-pill px-4" href="{{ $project->url ?: '#' }}" download rel="noreferrer">Download</a>
                            </div>
                        </article>
                    @empty
                        <div class="gates-page-empty repository-page-empty">
                            <div class="gates-page-empty-icon repository-page-empty-icon"><i class="bi bi-file-earmark"></i></div>
                            <h3>No items found</h3>
                            <p>{{ $gatesCollection['page_copy'] }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </main>

    @include('portal.partials.footer')
@endsection
