@extends('layouts.app', ['title' => 'DOST Admin Workspace'])

@section('body_class', 'admin-dashboard-page')

@php
    $tabMeta = [
        'issuances' => [
            'label' => 'Issuances',
            'section_title' => 'Add New Issuance',
            'section_copy' => 'Publish official memos, circulars, notices, and orders for the PES portal.',
            'icon' => 'bi-briefcase',
        ],
        'materials' => [
            'label' => 'Materials',
            'section_title' => 'Add New Material',
            'section_copy' => 'Manage videos, infographics, presentations, and supporting resources.',
            'icon' => 'bi-collection-play',
        ],
        'divisions' => [
            'label' => 'Divisions',
            'section_title' => 'Add New Division',
            'section_copy' => 'Keep division profiles and leadership information aligned with the portal.',
            'icon' => 'bi-diagram-3',
        ],
        'dx' => [
            'label' => 'DOST DX',
            'section_title' => 'Add DX Content',
            'section_copy' => 'Update DOST DX domains, sub-programs, and transformation content.',
            'icon' => 'bi-cpu',
        ],
        'categories' => [
            'label' => 'Categories',
            'section_title' => 'Add Issuance Category',
            'section_copy' => 'Maintain content taxonomies for better publication and search structure.',
            'icon' => 'bi-tags',
        ],
    ];

    $activeMeta = $tabMeta[$activeTab] ?? $tabMeta['issuances'];
@endphp

@section('content')
    <div class="admin-shell admin-shell-enhanced">
        @include('admin.partials.sidebar', ['activeSection' => 'workspace'])

        <main class="admin-main admin-main-enhanced">
            <div class="admin-dashboard-top admin-workspace-top">
                <div>
                    <div class="admin-kicker">Publishing Workspace</div>
                    <h1 class="admin-dashboard-title admin-workspace-title">{{ $activeMeta['label'] }}</h1>
                    <p class="text-secondary-soft mb-0">{{ $activeMeta['section_copy'] }}</p>
                </div>
                <div class="admin-workspace-actions">
                    <a class="btn admin-public-btn rounded-pill px-4" href="{{ route('admin.dashboard') }}">Dashboard Home</a>
                    <a class="btn admin-public-btn rounded-pill px-4" href="{{ route('portal.home') }}" target="_blank">View Public Portal</a>
                </div>
            </div>

            @if (session('status'))
                <div class="alert alert-success admin-status-alert">{{ session('status') }}</div>
            @endif

            <div class="row g-4">
                <div class="col-12 col-xxl-8">
                    <div class="admin-card admin-workspace-card mb-4">
                        <div class="admin-section-head">
                            <div class="admin-section-icon"><i class="bi {{ $activeMeta['icon'] }}"></i></div>
                            <div>
                                <h2 class="h3 fw-bold mb-1">{{ $activeMeta['section_title'] }}</h2>
                                <p class="text-secondary-soft mb-0">{{ $activeMeta['section_copy'] }}</p>
                            </div>
                        </div>

                        @if ($activeTab === 'issuances')
                            <form method="POST" action="{{ route('admin.issuances.store') }}" class="row g-3" enctype="multipart/form-data">@csrf
                                <div class="col-md-6"><input class="form-control" type="text" name="title" placeholder="Title" required></div>
                                <div class="col-md-6"><select class="form-select" name="category" required>@foreach ($categories as $category)<option value="{{ $category->name }}">{{ $category->name }}</option>@endforeach</select></div>
                                <div class="col-md-6"><input class="form-control" type="date" name="date" required></div>
                                <div class="col-md-6"><input class="form-control" type="text" name="division" placeholder="Division" required></div>
                                <div class="col-12"><input class="form-control" type="file" name="document" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" required></div>
                                <div class="col-12"><button class="btn btn-accent rounded-pill px-4" type="submit">Publish Issuance</button></div>
                            </form>
                        @endif

                        @if ($activeTab === 'materials')
                            <form method="POST" action="{{ route('admin.materials.store') }}" class="row g-3">@csrf
                                <div class="col-md-6"><input class="form-control" type="text" name="title" placeholder="Title" required></div>
                                <div class="col-md-6"><input class="form-control" type="text" name="type" placeholder="Type" required></div>
                                <div class="col-md-6"><input class="form-control" type="date" name="date" required></div>
                                <div class="col-md-6"><input class="form-control" type="text" name="division" placeholder="Division" required></div>
                                <div class="col-12"><input class="form-control" type="url" name="url" placeholder="Resource URL"></div>
                                <div class="col-12"><button class="btn btn-accent rounded-pill px-4" type="submit">Save Material</button></div>
                            </form>
                        @endif

                        @if ($activeTab === 'divisions')
                            <form method="POST" action="{{ route('admin.divisions.store') }}" class="row g-3">@csrf
                                <div class="col-md-6"><input class="form-control" type="text" name="name" placeholder="Division Name" required></div>
                                <div class="col-md-6"><input class="form-control" type="text" name="head" placeholder="Head of Division"></div>
                                <div class="col-12"><textarea class="form-control" name="description" rows="4" placeholder="Description" required></textarea></div>
                                <div class="col-12"><button class="btn btn-accent rounded-pill px-4" type="submit">Save Division</button></div>
                            </form>
                        @endif

                        @if ($activeTab === 'dx')
                            <form method="POST" action="{{ route('admin.dx-items.store') }}" class="row g-3">@csrf
                                <div class="col-md-4"><select class="form-select" name="category" required><option value="domain">Domain</option><option value="program">Sub-Program</option></select></div>
                                <div class="col-md-8"><input class="form-control" type="text" name="title" placeholder="Title" required></div>
                                <div class="col-12"><textarea class="form-control" name="description" rows="4" placeholder="Description" required></textarea></div>
                                <div class="col-12"><button class="btn btn-accent rounded-pill px-4" type="submit">Save DX Content</button></div>
                            </form>
                        @endif

                        @if ($activeTab === 'categories')
                            <form method="POST" action="{{ route('admin.categories.store') }}" class="row g-3">@csrf
                                <div class="col-md-8"><input class="form-control" type="text" name="name" placeholder="Category Name" required></div>
                                <div class="col-md-4"><button class="btn btn-accent rounded-pill px-4 w-100" type="submit">Add Category</button></div>
                            </form>
                        @endif
                    </div>

                    <div class="admin-card admin-table-shell">
                        <div class="admin-section-head admin-section-head-sm">
                            <div>
                                <h2 class="h4 fw-bold mb-1">{{ $activeMeta['label'] }} Library</h2>
                                <p class="text-secondary-soft mb-0">Review and maintain currently published records.</p>
                            </div>
                        </div>

                        @if ($activeTab === 'issuances')
                            @include('admin.partials.table-issuances')
                        @endif

                        @if ($activeTab === 'materials')
                            @include('admin.partials.table-materials')
                        @endif

                        @if ($activeTab === 'divisions')
                            @include('admin.partials.table-divisions')
                        @endif

                        @if ($activeTab === 'dx')
                            @include('admin.partials.table-dx')
                        @endif

                        @if ($activeTab === 'categories')
                            @include('admin.partials.table-categories')
                        @endif
                    </div>
                </div>

                <div class="col-12 col-xxl-4">
                    <div class="admin-card admin-side-card mb-4">
                        <h2 class="h5 fw-bold mb-3">Quick Links</h2>
                        <div class="admin-side-list">
                            <a href="{{ route('admin.workspace', ['tab' => 'issuances']) }}" class="admin-side-item admin-side-link">
                                <div class="admin-side-item-title">Go To Issuances</div>
                                <div class="admin-side-item-meta">Publish and remove official issuances.</div>
                            </a>
                            <a href="{{ route('admin.workspace', ['tab' => 'materials']) }}" class="admin-side-item admin-side-link">
                                <div class="admin-side-item-title">Go To Materials</div>
                                <div class="admin-side-item-meta">Update resources, reports, and presentations.</div>
                            </a>
                            <a href="{{ route('admin.workspace', ['tab' => 'dx']) }}" class="admin-side-item admin-side-link">
                                <div class="admin-side-item-title">Go To DOST DX</div>
                                <div class="admin-side-item-meta">Maintain domains and digital transformation sub-programs.</div>
                            </a>
                        </div>
                    </div>

                    <div class="admin-card admin-side-card">
                        <h2 class="h5 fw-bold mb-3">DOST DX Snapshot</h2>
                        <div class="admin-side-list">
                            @forelse ($dxPrograms->take(5) as $program)
                                <div class="admin-side-item">
                                    <div class="admin-side-item-title">{{ $program->title }}</div>
                                    <div class="admin-side-item-meta">DOST DX sub-program</div>
                                    <div class="admin-side-item-copy">{{ \Illuminate\Support\Str::limit($program->description, 110) }}</div>
                                </div>
                            @empty
                                <div class="text-secondary-soft">No DOST DX programs yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
