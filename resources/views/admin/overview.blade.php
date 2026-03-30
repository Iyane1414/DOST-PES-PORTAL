@extends('layouts.app', ['title' => 'DOST Admin Dashboard'])

@section('body_class', 'admin-dashboard-page')

@php
    $tabMeta = [
        'issuances' => ['icon' => 'bi-briefcase'],
        'materials' => ['icon' => 'bi-collection-play'],
        'divisions' => ['icon' => 'bi-diagram-3'],
        'dx' => ['icon' => 'bi-cpu'],
        'categories' => ['icon' => 'bi-tags'],
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
                    <div class="admin-stat-icon icon-gold"><i class="bi bi-chat-left-text"></i></div>
                    <div class="admin-stat-body">
                        <div class="admin-stat-label">Contact Messages</div>
                        <div class="admin-stat-value">{{ $stats['messages'] }}</div>
                        <div class="admin-stat-meta">Inbound concerns</div>
                    </div>
                </div>
                </div>

                <div class="row g-4">
                <div class="col-12">
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

                    <div class="admin-card admin-side-card admin-overview-card mt-4">
                        <h2 class="h5 fw-bold mb-3">Latest Contact Messages</h2>
                        <div class="admin-side-list">
                            @forelse ($messages as $message)
                                <div class="admin-side-item">
                                    <div class="admin-side-item-title">{{ $message->subject }}</div>
                                    <div class="admin-side-item-meta">{{ $message->name }} • {{ $message->email }}</div>
                                    <div class="admin-side-item-copy">{{ \Illuminate\Support\Str::limit($message->message, 120) }}</div>
                                </div>
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
