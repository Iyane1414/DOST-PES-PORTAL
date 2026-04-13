@extends('layouts.app', ['title' => $title])

@section('body_class', 'portal-page portal-page-dx')
@section('page_theme', 'dx')

@section('content')
    <main class="dx-program-page-shell repository-page-shell">
        <section class="dx-program-page-hero repository-page-hero">
            <div class="container">
                <div class="dx-program-page-hero-panel">
                    <a class="dx-program-page-back repository-page-back" href="{{ route('portal.home') }}#dost-dx">
                        <i class="bi bi-arrow-left"></i>
                        <span>Back to DOST DX</span>
                    </a>

                    <div class="dx-program-page-hero-grid">
                        <div class="dx-program-page-copy">
                            <div class="dx-program-page-kicker repository-page-kicker">{{ strtoupper($dxDomain['title']) }} SUB-PROGRAM</div>
                            <h1 class="dx-program-page-title repository-page-title">{{ $dxSubProgram['title'] }}</h1>
                            <div class="dx-program-page-count repository-page-count">{{ $dxTotalProjectCount }} projects mapped under this sub-program</div>
                        </div>
                        <div class="dx-program-page-summary repository-page-summary-card">
                            <div class="dx-program-page-summary-label">Domain</div>
                            <div class="dx-program-page-summary-value">{{ $dxDomain['title'] }}</div>
                            <div class="dx-program-page-summary-copy">{{ $dxSubProgram['description'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="dx-program-page-content repository-page-content">
            <div class="container">
                <div class="dx-program-page-toolbar repository-page-toolbar-card">
                    <form class="dx-program-page-controls repository-page-controls" method="GET" action="{{ route('portal.dx.program.show', ['domainSlug' => $dxSubProgram['domain'], 'subProgramSlug' => $dxSubProgram['slug']]) }}">
                        <div class="dx-program-page-search-wrap repository-page-search-wrap">
                            <i class="bi bi-search"></i>
                            <input
                                class="form-control dx-program-page-search repository-page-search"
                                type="search"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Search {{ strtolower($dxSubProgram['title']) }} projects..."
                                data-dx-project-search>
                        </div>
                    </form>

                    <div class="dx-program-page-status repository-page-status">
                        <strong data-dx-project-count>{{ $dxProjectCount }}</strong> projects available
                    </div>
                </div>

                <div class="dx-program-page-results repository-page-results">
                    @foreach ($dxProjects as $project)
                        <article
                            class="dx-program-page-item repository-page-item"
                            id="project-{{ $project['slug'] }}"
                            data-dx-project-item
                            data-project-title="{{ strtolower($project['title']) }}"
                            @if (! $project['matches_search']) hidden @endif>
                            <div class="dx-program-page-item-main repository-page-item-main">
                                <div class="dx-program-page-item-top repository-page-item-top">
                                    <span class="badge-soft">{{ $dxDomain['title'] }}</span>
                                </div>
                                <h2>{{ $project['title'] }}</h2>
                                <p>{{ $project['description'] ?: ($dxSubProgram['title'].' project under the '.$dxDomain['title'].' domain.') }}</p>
                            </div>
                            <div class="dx-program-page-item-action repository-page-item-actions">
                                @if (! empty($project['file_url']))
                                    <a class="btn btn-outline-dark rounded-pill px-4" href="{{ $project['file_url'] }}" target="_blank" rel="noreferrer">Open File</a>
                                    <a class="btn btn-accent rounded-pill px-4" href="{{ $project['file_url'] }}" target="_blank" rel="noreferrer" download>Download</a>
                                @else
                                    <span>Project Detail</span>
                                    <i class="bi bi-arrow-up-right"></i>
                                @endif
                            </div>
                        </article>
                    @endforeach

                    <div class="dx-program-page-empty repository-page-empty" data-dx-project-empty @if ($dxProjectCount > 0) hidden @endif>
                        <div class="dx-program-page-empty-icon repository-page-empty-icon"><i class="bi bi-diagram-3"></i></div>
                        <h3>No projects found</h3>
                        <p>No projects matched this sub-program yet.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('portal.partials.footer')
@endsection
