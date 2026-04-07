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
        'gates' => [
            'label' => 'DOST GATES',
            'section_title' => 'Add GATES Project',
            'section_copy' => 'Manage the DOST GATES brief overview and the project files shown on the public portal.',
            'icon' => 'bi-grid-1x2',
        ],
        'news' => [
            'label' => 'PES News',
            'section_title' => 'Add News Story',
            'section_copy' => 'Manage PES in Action stories with thumbnails, summaries, and optional external article links.',
            'icon' => 'bi-newspaper',
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
        'messages' => [
            'label' => 'Messages',
            'section_title' => 'Messages Inbox',
            'section_copy' => 'Review incoming portal concerns, browse sender details, and keep communication monitoring in one themed workspace.',
            'icon' => 'bi-chat-left-text',
        ],
        'ai' => [
            'label' => 'AI Agent',
            'section_title' => 'Manage AI Assistant',
            'section_copy' => 'Control the assistant prompt, topic boundaries, and refusal behavior from the admin workspace.',
            'icon' => 'bi-robot',
        ],
    ];

    $activeMeta = $tabMeta[$activeTab] ?? $tabMeta['issuances'];
    $isEditingIssuance = isset($selectedIssuance) && $selectedIssuance;
    $isEditingMaterial = isset($selectedMaterial) && $selectedMaterial;
    $isEditingGates = isset($selectedGatesProject) && $selectedGatesProject;
    $isEditingNews = isset($selectedNews) && $selectedNews;
    $isEditingDx = isset($selectedDxItem) && $selectedDxItem;
@endphp

@section('content')
    <div class="admin-shell admin-shell-enhanced">
        @include('admin.partials.sidebar', ['activeSection' => 'workspace'])

        <main class="admin-main admin-main-enhanced">
            <div class="admin-dashboard-top admin-workspace-top">
                <div class="admin-workspace-hero-copy">
                    <div class="admin-kicker">Publishing Workspace</div>
                    <h1 class="admin-dashboard-title admin-workspace-title">{{ $activeMeta['label'] }}</h1>
                    <p class="text-secondary-soft mb-0">{{ $activeMeta['section_copy'] }}</p>
                </div>
            </div>

            @if (session('status'))
                <div class="alert alert-success admin-status-alert">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger admin-status-alert">
                    <strong>Upload failed.</strong>
                    <div class="mt-2">{{ $errors->first() }}</div>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-12">
                    @if ($activeTab === 'issuances')
                        <div class="admin-issuance-workspace-grid">
                            <div class="admin-card admin-workspace-card admin-issuance-panel admin-issuance-library-panel">
                                <div class="admin-section-head admin-issuance-panel-head">
                                    <div class="admin-section-icon"><i class="bi {{ $activeMeta['icon'] }}"></i></div>
                                    <div>
                                        <div class="admin-kicker mb-2">Publishing Desk</div>
                                        <h2 class="h3 fw-bold mb-1">{{ $isEditingIssuance ? 'Edit Issuance' : $activeMeta['section_title'] }}</h2>
                                        <p class="text-secondary-soft mb-0">{{ $activeMeta['section_copy'] }}</p>
                                    </div>
                                </div>

                                <form method="POST" action="{{ $isEditingIssuance ? route('admin.issuances.update', $selectedIssuance) : route('admin.issuances.store') }}" class="row g-3" enctype="multipart/form-data">@csrf
                                    @if ($isEditingIssuance)
                                        @method('PUT')
                                    @endif
                                    <div class="col-md-6">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Issuance Title</label>
                                            <input class="form-control" type="text" name="title" value="{{ old('title', $selectedIssuance->title ?? '') }}" placeholder="Enter issuance title" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Category</label>
                                            <select class="form-select" name="category" required>@foreach ($categories as $category)<option value="{{ $category->name }}" @selected(old('category', $selectedIssuance->category ?? '') === $category->name)>{{ $category->name }}</option>@endforeach</select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Date Issued</label>
                                            <input class="form-control" type="date" name="date" value="{{ old('date', isset($selectedIssuance) && $selectedIssuance?->date ? $selectedIssuance->date->format('Y-m-d') : '') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Division</label>
                                            <input class="form-control" type="text" name="division" value="{{ old('division', $selectedIssuance->division ?? '') }}" placeholder="Enter division name" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="admin-issuance-field admin-issuance-file-field">
                                            <label class="form-label">Attachment{{ $isEditingIssuance ? ' (optional replacement)' : '' }}</label>
                                            <input class="form-control" type="file" name="document" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" {{ $isEditingIssuance ? '' : 'required' }}>
                                        </div>
                                    </div>
                                    <div class="col-12 admin-issuance-form-actions">
                                        <button class="btn btn-accent rounded-pill px-4" type="submit">{{ $isEditingIssuance ? 'Update Issuance' : 'Publish Issuance' }}</button>
                                        @if ($isEditingIssuance)
                                            <a class="btn btn-outline-secondary rounded-pill px-4" href="{{ route('admin.workspace', ['tab' => 'issuances']) }}">Cancel Edit</a>
                                        @endif
                                    </div>
                                </form>
                            </div>

                            <div class="admin-card admin-table-shell admin-issuance-panel admin-issuance-form-panel">
                                <div class="admin-section-head admin-section-head-sm admin-issuance-panel-head">
                                    <div>
                                        <div class="admin-kicker mb-2">Record Center</div>
                                        <h2 class="h4 fw-bold mb-1">{{ $activeMeta['label'] }} Library</h2>
                                        <p class="text-secondary-soft mb-0">Review and maintain currently published records.</p>
                                    </div>
                                </div>

                                <form method="GET" action="{{ route('admin.workspace', ['tab' => 'issuances']) }}" class="admin-issuance-library-toolbar" data-admin-issuance-library-form>
                                    <div class="admin-issuance-search-wrap">
                                        <i class="bi bi-search"></i>
                                        <input class="form-control" type="search" name="issuance_search" value="{{ $issuanceSearch ?? '' }}" placeholder="Search issuance title, category, or division..." data-admin-issuance-library-search>
                                    </div>
                                    <button class="btn admin-public-btn rounded-pill px-4" type="submit" data-admin-issuance-library-apply>Search</button>
                                </form>

                                @include('admin.partials.table-issuances')
                            </div>
                        </div>
                    @elseif ($activeTab === 'materials')
                        <div class="admin-issuance-workspace-grid">
                            <div class="admin-card admin-workspace-card admin-issuance-panel admin-issuance-library-panel">
                                <div class="admin-section-head admin-issuance-panel-head">
                                    <div class="admin-section-icon"><i class="bi {{ $activeMeta['icon'] }}"></i></div>
                                    <div>
                                        <div class="admin-kicker mb-2">Publishing Desk</div>
                                        <h2 class="h3 fw-bold mb-1">{{ $isEditingMaterial ? 'Edit Material' : $activeMeta['section_title'] }}</h2>
                                        <p class="text-secondary-soft mb-0">{{ $activeMeta['section_copy'] }}</p>
                                    </div>
                                </div>

                                <form method="POST" action="{{ $isEditingMaterial ? route('admin.materials.update', $selectedMaterial) : route('admin.materials.store') }}" class="row g-3" enctype="multipart/form-data">@csrf
                                    @if ($isEditingMaterial)
                                        @method('PUT')
                                    @endif
                                    <div class="col-md-6">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Material Title</label>
                                            <input class="form-control" type="text" name="title" value="{{ old('title', $selectedMaterial->title ?? '') }}" placeholder="Enter material title" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Material Type</label>
                                            <select class="form-select" name="type" required>
                                                <option value="Policy" @selected(old('type', $selectedMaterial->type ?? '') === 'Policy')>Policy</option>
                                                <option value="Annual Report" @selected(old('type', $selectedMaterial->type ?? '') === 'Annual Report')>Annual Report</option>
                                                <option value="R&D Survey" @selected(old('type', $selectedMaterial->type ?? '') === 'R&D Survey')>R&amp;D Survey</option>
                                                <option value="Presentation" @selected(old('type', $selectedMaterial->type ?? '') === 'Presentation')>Presentation</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Date Published</label>
                                            <input class="form-control" type="date" name="date" value="{{ old('date', isset($selectedMaterial) && $selectedMaterial?->date ? $selectedMaterial->date->format('Y-m-d') : '') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Division</label>
                                            <input class="form-control" type="text" name="division" value="{{ old('division', $selectedMaterial->division ?? '') }}" placeholder="Enter division name" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="admin-issuance-field admin-issuance-file-field">
                                            <label class="form-label">Attachment{{ $isEditingMaterial ? ' (optional replacement)' : '' }}</label>
                                            <input class="form-control" type="file" name="document" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.mp4,.mov,.jpg,.jpeg,.png" {{ $isEditingMaterial ? '' : 'required' }}>
                                        </div>
                                    </div>
                                    <div class="col-12 admin-issuance-form-actions">
                                        <button class="btn btn-accent rounded-pill px-4" type="submit">{{ $isEditingMaterial ? 'Update Material' : 'Save Material' }}</button>
                                        @if ($isEditingMaterial)
                                            <a class="btn btn-outline-secondary rounded-pill px-4" href="{{ route('admin.workspace', ['tab' => 'materials']) }}">Cancel Edit</a>
                                        @endif
                                    </div>
                                </form>
                            </div>

                            <div class="admin-card admin-table-shell admin-issuance-panel admin-issuance-form-panel">
                                <div class="admin-section-head admin-section-head-sm admin-issuance-panel-head">
                                    <div>
                                        <div class="admin-kicker mb-2">Record Center</div>
                                        <h2 class="h4 fw-bold mb-1">{{ $activeMeta['label'] }} Library</h2>
                                        <p class="text-secondary-soft mb-0">Review and maintain currently published records.</p>
                                    </div>
                                </div>

                                <form method="GET" action="{{ route('admin.workspace', ['tab' => 'materials']) }}" class="admin-issuance-library-toolbar" data-material-library-form>
                                    <div class="admin-issuance-search-wrap">
                                        <i class="bi bi-search"></i>
                                        <input class="form-control" type="search" name="material_search" value="{{ $materialSearch ?? '' }}" placeholder="Search material title, type, or division..." data-material-library-search>
                                    </div>
                                    <button class="btn admin-public-btn rounded-pill px-4" type="submit" data-material-library-apply>Search</button>
                                </form>

                                @include('admin.partials.table-materials')
                            </div>
                        </div>
                    @elseif ($activeTab === 'gates')
                        <div class="admin-issuance-workspace-grid">
                            <div class="admin-card admin-workspace-card admin-issuance-panel admin-issuance-library-panel">
                                <div class="admin-section-head admin-issuance-panel-head">
                                    <div class="admin-section-icon"><i class="bi {{ $activeMeta['icon'] }}"></i></div>
                                    <div>
                                        <div class="admin-kicker mb-2">Program Desk</div>
                                        <h2 class="h3 fw-bold mb-1">{{ $isEditingGates ? 'Edit GATES Project' : $activeMeta['section_title'] }}</h2>
                                        <p class="text-secondary-soft mb-0">{{ $activeMeta['section_copy'] }}</p>
                                    </div>
                                </div>

                                <form method="POST" action="{{ $isEditingGates ? route('admin.gates.update', $selectedGatesProject) : route('admin.gates.store') }}" class="row g-3" enctype="multipart/form-data">@csrf
                                    @if ($isEditingGates)
                                        @method('PUT')
                                    @endif
                                    <div class="col-md-8">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Project Title</label>
                                            <input class="form-control" type="text" name="title" value="{{ old('title', $selectedGatesProject->title ?? '') }}" placeholder="Enter GATES project title" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Project Code</label>
                                            <input class="form-control" type="text" name="code" value="{{ old('code', $selectedGatesProject->code ?? 'GATES') }}" placeholder="GATES">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Content Type</label>
                                            <select class="form-select" name="type" required>
                                                @php
                                                    $selectedGatesType = strtolower(str_replace(' ', '_', old('type', $selectedGatesProject->type ?? 'project')));
                                                @endphp
                                                <option value="project" @selected($selectedGatesType === 'project')>Project</option>
                                                <option value="video_presentation" @selected($selectedGatesType === 'video_presentation')>Video Presentation</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Date</label>
                                            <input class="form-control" type="date" name="date" value="{{ old('date', isset($selectedGatesProject) && $selectedGatesProject?->date ? $selectedGatesProject->date->format('Y-m-d') : '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Sort Order</label>
                                            <input class="form-control" type="number" name="sort_order" min="0" value="{{ old('sort_order', $selectedGatesProject->sort_order ?? 0) }}">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Brief Description</label>
                                            <textarea class="form-control" name="description" rows="4" placeholder="Write a short public-facing summary for this GATES project." required>{{ old('description', $selectedGatesProject->description ?? '') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="admin-issuance-field admin-issuance-file-field">
                                            <label class="form-label">Project Attachment{{ $isEditingGates ? ' (optional replacement)' : '' }}</label>
                                            <input class="form-control" type="file" name="document" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.mp4,.mov,.jpg,.jpeg,.png" {{ $isEditingGates ? '' : 'required' }}>
                                        </div>
                                    </div>
                                    <div class="col-12 admin-issuance-form-actions">
                                        <button class="btn btn-accent rounded-pill px-4" type="submit">{{ $isEditingGates ? 'Update GATES Project' : 'Save GATES Project' }}</button>
                                        @if ($isEditingGates)
                                            <a class="btn btn-outline-secondary rounded-pill px-4" href="{{ route('admin.workspace', ['tab' => 'gates']) }}">Cancel Edit</a>
                                        @endif
                                    </div>
                                </form>
                            </div>

                            <div class="admin-card admin-table-shell admin-issuance-panel admin-issuance-form-panel">
                                <div class="admin-section-head admin-section-head-sm admin-issuance-panel-head">
                                    <div>
                                        <div class="admin-kicker mb-2">Record Center</div>
                                        <h2 class="h4 fw-bold mb-1">DOST GATES Library</h2>
                                        <p class="text-secondary-soft mb-0">Review and maintain the public-facing GATES projects and reference files.</p>
                                    </div>
                                </div>

                                <form method="GET" action="{{ route('admin.workspace', ['tab' => 'gates']) }}" class="admin-issuance-library-toolbar" data-gates-library-form>
                                    <div class="admin-issuance-search-wrap">
                                        <i class="bi bi-search"></i>
                                        <input class="form-control" type="search" name="material_search" value="{{ $materialSearch ?? '' }}" placeholder="Search GATES title, code, or description..." data-gates-library-search>
                                    </div>
                                    <button class="btn admin-public-btn rounded-pill px-4" type="submit" data-gates-library-apply>Search</button>
                                </form>

                                @include('admin.partials.table-gates')
                            </div>
                        </div>
                    @elseif ($activeTab === 'news')
                        <div class="admin-issuance-workspace-grid">
                            <div class="admin-card admin-workspace-card admin-issuance-panel admin-issuance-library-panel">
                                <div class="admin-section-head admin-issuance-panel-head">
                                    <div class="admin-section-icon"><i class="bi {{ $activeMeta['icon'] }}"></i></div>
                                    <div>
                                        <div class="admin-kicker mb-2">Publishing Desk</div>
                                        <h2 class="h3 fw-bold mb-1">{{ $isEditingNews ? 'Edit News Story' : $activeMeta['section_title'] }}</h2>
                                        <p class="text-secondary-soft mb-0">{{ $activeMeta['section_copy'] }}</p>
                                    </div>
                                </div>

                                <form method="POST" action="{{ $isEditingNews ? route('admin.news.update', $selectedNews) : route('admin.news.store') }}" class="row g-3" enctype="multipart/form-data">@csrf
                                    @if ($isEditingNews)
                                        @method('PUT')
                                    @endif
                                    <div class="col-md-4">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Story Label</label>
                                            <input class="form-control" type="text" name="eyebrow" value="{{ old('eyebrow', $selectedNews->eyebrow ?? 'Update') }}" placeholder="Featured, Event, Update..." required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Published Date</label>
                                            <input class="form-control" type="date" name="date" value="{{ old('date', isset($selectedNews) && $selectedNews?->date ? $selectedNews->date->format('Y-m-d') : '') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Accent Style</label>
                                            <select class="form-select" name="accent" required>
                                                @foreach (['cyan' => 'Cyan', 'blue' => 'Blue', 'gold' => 'Gold', 'mint' => 'Mint', 'violet' => 'Violet', 'slate' => 'Slate'] as $accentValue => $accentLabel)
                                                    <option value="{{ $accentValue }}" @selected(old('accent', $selectedNews->accent ?? 'cyan') === $accentValue)>{{ $accentLabel }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Headline</label>
                                            <input class="form-control" type="text" name="title" value="{{ old('title', $selectedNews->title ?? '') }}" placeholder="Enter PES news headline" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Card Summary</label>
                                            <textarea class="form-control" name="summary" rows="3" placeholder="Short summary shown on the homepage card" required>{{ old('summary', $selectedNews->summary ?? '') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Full Story</label>
                                            <textarea class="form-control" name="content" rows="6" placeholder="Full story content for the modal view" required>{{ old('content', $selectedNews->content ?? '') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Article Link (optional)</label>
                                            <input class="form-control" type="url" name="link_url" value="{{ old('link_url', $selectedNews->link_url ?? '') }}" placeholder="https://example.com/news-story">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Thumbnail Alt Text</label>
                                            <input class="form-control" type="text" name="image_alt" value="{{ old('image_alt', $selectedNews->image_alt ?? '') }}" placeholder="Short image description">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="admin-issuance-field admin-issuance-file-field">
                                            <label class="form-label">Thumbnail{{ $isEditingNews ? ' (optional replacement)' : '' }}</label>
                                            <input class="form-control" type="file" name="thumbnail" accept=".jpg,.jpeg,.png,.webp">
                                        </div>
                                    </div>
                                    <div class="col-12 admin-issuance-form-actions">
                                        <button class="btn btn-accent rounded-pill px-4" type="submit">{{ $isEditingNews ? 'Update Story' : 'Publish Story' }}</button>
                                        @if ($isEditingNews)
                                            <a class="btn btn-outline-secondary rounded-pill px-4" href="{{ route('admin.workspace', ['tab' => 'news']) }}">Cancel Edit</a>
                                        @endif
                                    </div>
                                </form>
                            </div>

                            <div class="admin-card admin-table-shell admin-issuance-panel admin-issuance-form-panel">
                                <div class="admin-section-head admin-section-head-sm admin-issuance-panel-head">
                                    <div>
                                        <div class="admin-kicker mb-2">Record Center</div>
                                        <h2 class="h4 fw-bold mb-1">{{ $activeMeta['label'] }} Library</h2>
                                        <p class="text-secondary-soft mb-0">Review and maintain the stories visible in the PES in Action section.</p>
                                    </div>
                                </div>

                                <form method="GET" action="{{ route('admin.workspace', ['tab' => 'news']) }}" class="admin-issuance-library-toolbar" data-news-library-form>
                                    <div class="admin-issuance-search-wrap">
                                        <i class="bi bi-search"></i>
                                        <input class="form-control" type="search" name="news_search" value="{{ $newsSearch ?? '' }}" placeholder="Search story title, label, or summary..." data-news-library-search>
                                    </div>
                                    <button class="btn admin-public-btn rounded-pill px-4" type="submit" data-news-library-apply>Search</button>
                                </form>

                                @include('admin.partials.table-news')
                            </div>
                        </div>
                    @elseif ($activeTab === 'dx')
                        @php
                            $dxCodeOptions = $dxItems->where('category', 'project')->pluck('code')->filter()->push('ETC')->unique()->sort()->values();
                            $dxProjectPrograms = $dxPrograms->values();
                        @endphp
                        <div class="admin-issuance-workspace-grid">
                            <div class="admin-card admin-workspace-card admin-issuance-panel admin-issuance-library-panel">
                                <div class="admin-section-head admin-issuance-panel-head">
                                    <div class="admin-section-icon"><i class="bi {{ $activeMeta['icon'] }}"></i></div>
                                    <div>
                                        <div class="admin-kicker mb-2">Publishing Desk</div>
                                        <h2 class="h3 fw-bold mb-1">{{ $isEditingDx ? 'Edit DX Content' : 'Add DX Content' }}</h2>
                                        <p class="text-secondary-soft mb-0">Update DOST DX sub-programs and project records in a cleaner structured workflow.</p>
                                    </div>
                                </div>

                                <form method="POST" action="{{ $isEditingDx ? route('admin.dx-items.update', $selectedDxItem) : route('admin.dx-items.store') }}" class="row g-3" enctype="multipart/form-data">@csrf
                                    @if ($isEditingDx)
                                        @method('PUT')
                                    @endif
                                    <input type="hidden" name="category" value="project">
                                    <div class="col-md-4">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Core Domain</label>
                                            <select class="form-select" name="domain_key" required>
                                                <option value="people" @selected(old('domain_key', $selectedDxItem->domain_key ?? 'people') === 'people')>People</option>
                                                <option value="process" @selected(old('domain_key', $selectedDxItem->domain_key ?? '') === 'process')>Process</option>
                                                <option value="technology" @selected(old('domain_key', $selectedDxItem->domain_key ?? '') === 'technology')>Technology</option>
                                                <option value="other" @selected(old('domain_key', $selectedDxItem->domain_key ?? '') === 'other')>Others</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Sort Order</label>
                                            <input class="form-control" type="number" name="sort_order" min="0" value="{{ old('sort_order', $selectedDxItem->sort_order ?? 0) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Sub-program</label>
                                            <select class="form-select" name="parent_id" required>
                                                @foreach ($dxProjectPrograms as $program)
                                                    <option value="{{ $program->id }}" @selected((string) old('parent_id', $selectedDxItem->parent_id ?? '') === (string) $program->id)>{{ $program->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Project Code</label>
                                            <select class="form-select" name="code">
                                                <option value="">Select project code</option>
                                                @foreach ($dxCodeOptions as $code)
                                                    <option value="{{ $code }}" @selected(old('code', $selectedDxItem->code ?? '') === $code)>{{ $code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Title</label>
                                            <input class="form-control" type="text" name="title" value="{{ old('title', $selectedDxItem->title ?? '') }}" placeholder="Title" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" name="description" rows="4" placeholder="Description" required>{{ old('description', $selectedDxItem->description ?? '') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="admin-issuance-field admin-issuance-file-field">
                                            <label class="form-label">Project Attachment{{ $isEditingDx ? ' (optional replacement)' : '' }}</label>
                                            <input class="form-control" type="file" name="document" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.mp4,.mov,.jpg,.jpeg,.png">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="admin-issuance-field admin-issuance-file-field">
                                            <label class="form-label">Domain Image{{ $isEditingDx ? ' (optional replacement)' : '' }}</label>
                                            <input class="form-control" type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
                                        </div>
                                    </div>
                                    <div class="col-12 admin-issuance-form-actions">
                                        <button class="btn btn-accent rounded-pill px-4" type="submit">{{ $isEditingDx ? 'Update DX Content' : 'Save DX Content' }}</button>
                                        @if ($isEditingDx)
                                            <a class="btn btn-outline-secondary rounded-pill px-4" href="{{ route('admin.workspace', ['tab' => 'dx']) }}">Cancel Edit</a>
                                        @endif
                                    </div>
                                </form>
                            </div>

                            <div class="admin-card admin-table-shell admin-issuance-panel admin-issuance-form-panel">
                                <div class="admin-section-head admin-section-head-sm admin-issuance-panel-head">
                                    <div>
                                        <div class="admin-kicker mb-2">Record Center</div>
                                        <h2 class="h4 fw-bold mb-1">DOST DX Library</h2>
                                        <p class="text-secondary-soft mb-0">Review and maintain currently published DOST DX records.</p>
                                    </div>
                                </div>

                                <form method="GET" action="{{ route('admin.workspace', ['tab' => 'dx']) }}" class="admin-issuance-library-toolbar" data-dx-library-form>
                                    <div class="admin-issuance-search-wrap">
                                        <i class="bi bi-search"></i>
                                        <input class="form-control" type="search" name="dx_search" value="{{ $dxSearch ?? '' }}" placeholder="Search project title, code, or sub-program..." data-dx-library-search>
                                    </div>
                                    <button class="btn admin-public-btn rounded-pill px-4" type="submit" data-dx-library-apply>Search</button>
                                </form>

                                @include('admin.partials.table-dx')
                            </div>
                        </div>
                    @elseif ($activeTab === 'messages')
                        <div class="admin-issuance-workspace-grid">
                            <div class="admin-card admin-table-shell admin-issuance-panel admin-issuance-form-panel admin-message-inbox-panel">
                                <div class="admin-section-head admin-section-head-sm admin-issuance-panel-head">
                                    <div>
                                        <div class="admin-kicker mb-2">Inbox Monitor</div>
                                        <h2 class="h4 fw-bold mb-1">Messages Inbox</h2>
                                        <p class="text-secondary-soft mb-0">Review sender details, open full message records, and reply directly from the admin workspace.</p>
                                    </div>
                                </div>

                                <div class="admin-message-inbox-summary">
                                    <div class="admin-message-inbox-summary-copy">
                                        Total {{ $workspaceMessages->count() }} captured {{ \Illuminate\Support\Str::plural('message', $workspaceMessages->count()) }} from the public portal.
                                    </div>
                                </div>

                                <form method="GET" action="{{ route('admin.workspace', ['tab' => 'messages']) }}" class="admin-message-toolbar">
                                    <div class="admin-message-toolbar-main">
                                        <div class="admin-message-search-wrap">
                                            <i class="bi bi-search"></i>
                                            <input class="form-control" type="search" name="message_search" value="{{ $messageSearch }}" placeholder="Search sender, email, subject, or message...">
                                        </div>
                                        <div class="admin-message-sort-wrap">
                                            <select class="form-select" name="message_sort">
                                                <option value="newest" @selected($messageSort === 'newest')>Newest</option>
                                                <option value="oldest" @selected($messageSort === 'oldest')>Oldest</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="admin-message-toolbar-side">
                                        <button class="btn admin-public-btn rounded-pill px-4" type="submit">Apply</button>
                                    </div>
                                </form>

                                <div class="admin-message-inbox-section">
                                    <div class="admin-kicker mb-2">Inbox Status</div>
                                    @include('admin.partials.table-messages')
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="admin-card admin-workspace-card mb-4">
                            <div class="admin-section-head">
                                    <div class="admin-section-icon"><i class="bi {{ $activeMeta['icon'] }}"></i></div>
                                    <div>
                                        <h2 class="h3 fw-bold mb-1">{{ $activeTab === 'dx' && $isEditingDx ? 'Edit DX Content' : $activeMeta['section_title'] }}</h2>
                                        <p class="text-secondary-soft mb-0">{{ $activeMeta['section_copy'] }}</p>
                                    </div>
                                </div>

                            @if ($activeTab === 'divisions')
                                <form method="POST" action="{{ route('admin.divisions.store') }}" class="row g-3">@csrf
                                    <div class="col-md-6"><input class="form-control" type="text" name="name" placeholder="Division Name" required></div>
                                    <div class="col-md-6"><input class="form-control" type="text" name="head" placeholder="Head of Division"></div>
                                    <div class="col-12"><textarea class="form-control" name="description" rows="4" placeholder="Description" required></textarea></div>
                                    <div class="col-12"><button class="btn btn-accent rounded-pill px-4" type="submit">Save Division</button></div>
                                </form>
                            @endif

                            @if ($activeTab === 'categories')
                                <form method="POST" action="{{ route('admin.categories.store') }}" class="row g-3">@csrf
                                    <div class="col-md-8"><input class="form-control" type="text" name="name" placeholder="Category Name" required></div>
                                    <div class="col-md-4"><button class="btn btn-accent rounded-pill px-4 w-100" type="submit">Add Category</button></div>
                                </form>
                            @endif

                            @if ($activeTab === 'ai')
                                <form method="POST" action="{{ route('admin.ai-settings.store') }}" class="row g-3">@csrf
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">System Prompt</label>
                                        <textarea class="form-control" name="system_prompt" rows="6" required>{{ old('system_prompt', $aiSetting?->system_prompt ?? 'You are the PES AI Assistant for the DOST Planning and Evaluation Service. Answer only with PES-related information found in the provided portal context. Be concise, factual, and helpful. Use citation-style references from the supplied source list when possible.') }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Scope Prompt</label>
                                        <textarea class="form-control" name="scope_prompt" rows="4" required>{{ old('scope_prompt', $aiSetting?->scope_prompt ?? 'Only answer questions about PES mandates, divisions, issuances, materials, contact details, DOST DX, and information clearly present in the portal database context. If a question is outside PES scope, refuse briefly.') }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Refusal Message</label>
                                        <input class="form-control" type="text" name="refusal_message" value="{{ old('refusal_message', $aiSetting?->refusal_message ?? 'I can only help with PES-related information available in this portal, such as mandates, divisions, issuances, materials, contact details, and DOST DX content.') }}" required>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-accent rounded-pill px-4" type="submit">Save AI Settings</button>
                                    </div>
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

                            @if ($activeTab === 'divisions')
                                @include('admin.partials.table-divisions')
                            @endif

                            @if ($activeTab === 'categories')
                                @include('admin.partials.table-categories')
                            @endif

                            @if ($activeTab === 'ai')
                                <div class="admin-side-list">
                                    <div class="admin-side-item">
                                        <div class="admin-side-item-title">How This Works</div>
                                        <div class="admin-side-item-copy">The assistant uses your OpenAI key, your admin-managed prompts, and portal records from issuances, materials, divisions, DOST DX, and contact details. It now prefers PES-only answers and adds source-style citations based on matched records.</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
@endsection
