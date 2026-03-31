@extends('layouts.app', ['title' => $title])

@section('content')
    @include('portal.partials.navigation')

    <main class="dx-program-page-shell">
        <section class="dx-program-page-hero">
            <div class="container">
                <div class="dx-program-page-hero-panel">
                    <a class="dx-program-page-back" href="{{ route('portal.home') }}#dost-dx">
                        <i class="bi bi-arrow-left"></i>
                        <span>Back to DOST DX</span>
                    </a>

                    <div class="dx-program-page-hero-grid">
                        <div class="dx-program-page-copy">
                            <div class="dx-program-page-kicker">{{ strtoupper($dxDomain['title']) }} SUB-PROGRAM</div>
                            <h1 class="dx-program-page-title">{{ $dxSubProgram['title'] }}</h1>
                            <div class="dx-program-page-count">{{ $dxTotalProjectCount }} projects mapped under this sub-program</div>
                        </div>
                        <div class="dx-program-page-summary">
                            <div class="dx-program-page-summary-label">Domain</div>
                            <div class="dx-program-page-summary-value">{{ $dxDomain['title'] }}</div>
                            <div class="dx-program-page-summary-copy">{{ $dxSubProgram['description'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="dx-program-page-content">
            <div class="container">
                <div class="dx-program-page-toolbar">
                    <form class="dx-program-page-controls" method="GET" action="{{ route('portal.dx.program.show', ['domainSlug' => $dxSubProgram['domain'], 'subProgramSlug' => $dxSubProgram['slug']]) }}">
                        <div class="dx-program-page-search-wrap">
                            <i class="bi bi-search"></i>
                            <input
                                class="form-control dx-program-page-search"
                                type="search"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Search {{ strtolower($dxSubProgram['title']) }} projects..."
                                data-dx-project-search>
                        </div>
                    </form>

                    <div class="dx-program-page-status">
                        <strong data-dx-project-count>{{ $dxProjectCount }}</strong> projects available
                    </div>
                </div>

                <div class="dx-program-page-results">
                    @foreach ($dxProjects as $project)
                        <a
                            class="dx-program-page-item"
                            id="project-{{ $project['slug'] }}"
                            href="#project-{{ $project['slug'] }}"
                            data-dx-project-item
                            data-project-code="{{ strtolower($project['code']) }}"
                            data-project-title="{{ strtolower($project['title']) }}"
                            @if (! $project['matches_search']) hidden @endif>
                            <div class="dx-program-page-item-main">
                                <div class="dx-program-page-item-top">
                                    <span class="dx-program-page-code">{{ $project['code'] }}</span>
                                    <span class="badge-soft">{{ $dxDomain['title'] }}</span>
                                </div>
                                <h2>{{ $project['title'] }}</h2>
                                <p>{{ $dxSubProgram['title'] }} project under the {{ $dxDomain['title'] }} domain.</p>
                            </div>
                            <div class="dx-program-page-item-action">
                                <span>Project Detail</span>
                                <i class="bi bi-arrow-up-right"></i>
                            </div>
                        </a>
                    @endforeach

                    <div class="dx-program-page-empty" data-dx-project-empty @if ($dxProjectCount > 0) hidden @endif>
                        <div class="dx-program-page-empty-icon"><i class="bi bi-diagram-3"></i></div>
                        <h3>No projects found</h3>
                        <p>No projects matched this sub-program yet.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('portal.partials.footer')
@endsection
