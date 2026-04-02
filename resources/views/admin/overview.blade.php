@extends('layouts.app', ['title' => 'DOST Admin Dashboard'])

@section('body_class', 'admin-dashboard-page')

@php
    $tabMeta = [
        'issuances' => ['icon' => 'bi-briefcase'],
        'materials' => ['icon' => 'bi-collection-play'],
        'divisions' => ['icon' => 'bi-diagram-3'],
        'dx' => ['icon' => 'bi-cpu'],
        'categories' => ['icon' => 'bi-tags'],
        'messages' => ['icon' => 'bi-chat-left-text'],
        'ai' => ['icon' => 'bi-robot'],
    ];
@endphp

@section('content')
    <div class="admin-shell admin-shell-enhanced">
        @include('admin.partials.sidebar', ['activeSection' => 'overview'])

        <main class="admin-main admin-main-enhanced">
            <div class="admin-dashboard-top admin-overview-hero">
                <div>
                    <div class="admin-kicker">Administrative Dashboard</div>
                    <h1 class="admin-dashboard-title">DOST PES Control Center</h1>
                    <p class="text-secondary-soft mb-0">A cleaner admin home for monitoring portal activity, jumping into content tasks, and checking the latest DOST DX updates.</p>
                </div>
                <a class="btn admin-public-btn rounded-pill px-4" href="{{ route('portal.home') }}" target="_blank">View Public Portal</a>
            </div>

            <div class="admin-overview-shell">
                <div class="admin-overview-shell-glow"></div>

                <div class="admin-stat-grid admin-overview-stat-grid">
                    <div class="admin-stat-card admin-overview-stat-card">
                        <div class="admin-stat-icon icon-blue"><i class="bi bi-briefcase"></i></div>
                        <div class="admin-stat-body">
                            <div class="admin-stat-label">Issuances</div>
                            <div class="admin-stat-value">{{ $stats['issuances'] }}</div>
                            <div class="admin-stat-meta">Published records</div>
                        </div>
                    </div>
                    <div class="admin-stat-card admin-overview-stat-card">
                        <div class="admin-stat-icon icon-green"><i class="bi bi-collection-play"></i></div>
                        <div class="admin-stat-body">
                            <div class="admin-stat-label">Materials</div>
                            <div class="admin-stat-value">{{ $stats['materials'] }}</div>
                            <div class="admin-stat-meta">Active resources</div>
                        </div>
                    </div>
                    <div class="admin-stat-card admin-overview-stat-card">
                        <div class="admin-stat-icon icon-cyan"><i class="bi bi-cpu"></i></div>
                        <div class="admin-stat-body">
                            <div class="admin-stat-label">DOST DX Projects</div>
                            <div class="admin-stat-value">{{ $stats['dx_programs'] }}</div>
                            <div class="admin-stat-meta">Tracked sub-programs</div>
                        </div>
                    </div>
                    <div class="admin-stat-card admin-overview-stat-card">
                        <div class="admin-stat-icon icon-gold"><i class="bi bi-eye"></i></div>
                        <div class="admin-stat-body">
                            <div class="admin-stat-label">Website Views</div>
                            <div class="admin-stat-value">{{ $stats['website_views'] }}</div>
                            <div class="admin-stat-meta">Recorded portal visits</div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-12 col-xxl-8">
                        @php
                            $pending = $projectAnalytics['pending'];
                            $done = $projectAnalytics['done'];
                            $new = $projectAnalytics['new'];
                            $circumference = 326.7256;
                            $pendingLength = ($pending['percent'] / 100) * $circumference;
                            $doneLength = ($done['percent'] / 100) * $circumference;
                            $newLength = ($new['percent'] / 100) * $circumference;
                        @endphp

                        <div class="admin-card admin-overview-card admin-project-widget mb-4" data-project-chart>
                            <div class="admin-project-widget-head">
                                <div>
                                    <h2 class="h4 fw-bold mb-1">Monthly Target</h2>
                                    <p class="text-secondary-soft mb-0">Hover each project status to inspect the current PES project mix.</p>
                                </div>
                            </div>

                            <div class="admin-project-widget-layout">
                                <div class="admin-project-widget-chart-shell">
                                    <div class="admin-project-tooltip" data-project-tooltip>
                                        <strong data-project-tooltip-value>{{ $pending['count'] }} Projects</strong>
                                        <span data-project-tooltip-label>Pending Projects</span>
                                    </div>

                                    <div class="admin-project-widget-chart">
                                        <svg viewBox="0 0 160 160" class="admin-project-chart-svg" aria-hidden="true">
                                            <circle class="admin-project-chart-track" cx="80" cy="80" r="52"></circle>
                                            <circle class="admin-project-chart-segment admin-project-chart-segment-pending" cx="80" cy="80" r="52"
                                                stroke="{{ $pending['color'] }}"
                                                stroke-dasharray="{{ $pendingLength }} {{ $circumference - $pendingLength }}"
                                                stroke-dashoffset="0"
                                                data-project-segment
                                                data-project-label="Pending Projects"
                                                data-project-value="{{ $pending['count'] }} Projects"
                                                data-project-percent="{{ $pending['percent'] }}%"></circle>
                                            <circle class="admin-project-chart-segment admin-project-chart-segment-done" cx="80" cy="80" r="52"
                                                stroke="{{ $done['color'] }}"
                                                stroke-dasharray="{{ $doneLength }} {{ $circumference - $doneLength }}"
                                                stroke-dashoffset="-{{ $pendingLength }}"
                                                data-project-segment
                                                data-project-label="Done Projects"
                                                data-project-value="{{ $done['count'] }} Projects"
                                                data-project-percent="{{ $done['percent'] }}%"></circle>
                                            <circle class="admin-project-chart-segment admin-project-chart-segment-new" cx="80" cy="80" r="52"
                                                stroke="{{ $new['color'] }}"
                                                stroke-dasharray="{{ $newLength }} {{ $circumference - $newLength }}"
                                                stroke-dashoffset="-{{ $pendingLength + $doneLength }}"
                                                data-project-segment
                                                data-project-label="New Projects"
                                                data-project-value="{{ $new['count'] }} Projects"
                                                data-project-percent="{{ $new['percent'] }}%"></circle>
                                        </svg>

                                        <div class="admin-project-chart-center" data-project-center>
                                            <strong data-project-center-value>{{ $pending['count'] }} Projects</strong>
                                            <span data-project-center-label>Pending Projects</span>
                                            <small data-project-center-percent>{{ $pending['percent'] }}%</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="admin-project-widget-legend">
                                    <button class="admin-project-legend-item is-active" type="button"
                                        style="--legend-color: {{ $pending['color'] }}"
                                        data-project-trigger
                                        data-project-label="Pending Projects"
                                        data-project-value="{{ $pending['count'] }} Projects"
                                        data-project-percent="{{ $pending['percent'] }}%">
                                        <span class="admin-project-dot" style="--dot-color: {{ $pending['color'] }}"></span>
                                        <span>
                                            <strong>Pending Projects</strong>
                                            <small>{{ $pending['percent'] }}%</small>
                                        </span>
                                    </button>
                                    <button class="admin-project-legend-item" type="button"
                                        style="--legend-color: {{ $done['color'] }}"
                                        data-project-trigger
                                        data-project-label="Done Projects"
                                        data-project-value="{{ $done['count'] }} Projects"
                                        data-project-percent="{{ $done['percent'] }}%">
                                        <span class="admin-project-dot" style="--dot-color: {{ $done['color'] }}"></span>
                                        <span>
                                            <strong>Done Projects</strong>
                                            <small>{{ $done['percent'] }}%</small>
                                        </span>
                                    </button>
                                    <button class="admin-project-legend-item" type="button"
                                        style="--legend-color: {{ $new['color'] }}"
                                        data-project-trigger
                                        data-project-label="New Projects"
                                        data-project-value="{{ $new['count'] }} Projects"
                                        data-project-percent="{{ $new['percent'] }}%">
                                        <span class="admin-project-dot" style="--dot-color: {{ $new['color'] }}"></span>
                                        <span>
                                            <strong>New Projects</strong>
                                            <small>{{ $new['percent'] }}%</small>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="admin-card admin-table-shell admin-overview-card">
                            <div class="admin-section-head admin-section-head-sm">
                                <div>
                                    <h2 class="h4 fw-bold mb-1">Recent Public Content</h2>
                                    <p class="text-secondary-soft mb-0">A quick look at the latest items already visible on the portal.</p>
                                </div>
                            </div>

                            <div class="admin-overview-stream">
                                <div class="admin-overview-stream-card">
                                    <h3 class="h5 fw-bold mb-3">Latest Issuances</h3>
                                    <div class="admin-side-list">
                                        @forelse ($recentIssuances as $issuance)
                                            <div class="admin-side-item">
                                                <div class="admin-side-item-title">{{ $issuance->title }}</div>
                                                <div class="admin-side-item-meta">{{ $issuance->category }} • {{ optional($issuance->date)->format('M d, Y') }}</div>
                                            </div>
                                        @empty
                                            <div class="text-secondary-soft">No issuances yet.</div>
                                        @endforelse
                                    </div>
                                </div>

                                <div class="admin-overview-stream-card">
                                    <h3 class="h5 fw-bold mb-3">Latest Materials</h3>
                                    <div class="admin-side-list">
                                        @forelse ($recentMaterials as $material)
                                            <div class="admin-side-item">
                                                <div class="admin-side-item-title">{{ $material->title }}</div>
                                                <div class="admin-side-item-meta">{{ $material->type }} • {{ optional($material->date)->format('M d, Y') }}</div>
                                            </div>
                                        @empty
                                            <div class="text-secondary-soft">No materials yet.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xxl-4">
                        <div class="admin-card admin-side-card admin-overview-card admin-contact-stack-card">
                            <div class="admin-contact-stack-head">
                                <div>
                                    <div class="admin-kicker mb-2">Inbox Monitor</div>
                                    <h2 class="h4 fw-bold mb-1">Latest Contact Messages</h2>
                                </div>
                                <a href="{{ route('admin.workspace', ['tab' => 'messages']) }}" class="admin-contact-stack-link">Open Inbox</a>
                            </div>
                            <div class="admin-side-list admin-contact-stack">
                                @forelse ($recentMessages as $message)
                                    <a href="{{ route('admin.messages.show', $message) }}" class="admin-side-item admin-side-link admin-message-preview-card">
                                        <div class="admin-message-preview-avatar">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($message->name, 0, 1)) }}</div>
                                        <div class="admin-message-preview-main">
                                            <div class="admin-message-preview-sender">{{ $message->name }}</div>
                                            <div class="admin-side-item-title">{{ $message->subject }}</div>
                                            <div class="admin-side-item-meta">{{ $message->email }}</div>
                                        </div>
                                        <div class="admin-message-preview-time">{{ optional($message->created_at)->diffForHumans() }}</div>
                                    </a>
                                @empty
                                    <div class="text-secondary-soft">No contact messages yet.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

