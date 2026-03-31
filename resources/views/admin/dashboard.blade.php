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

            <div class="row g-4">
                <div class="col-12">
                    @if ($activeTab === 'issuances')
                        <div class="admin-issuance-workspace-grid">
                            <div class="admin-card admin-workspace-card admin-issuance-panel admin-issuance-library-panel">
                                <div class="admin-section-head admin-issuance-panel-head">
                                    <div class="admin-section-icon"><i class="bi {{ $activeMeta['icon'] }}"></i></div>
                                    <div>
                                        <div class="admin-kicker mb-2">Publishing Desk</div>
                                        <h2 class="h3 fw-bold mb-1">{{ $activeMeta['section_title'] }}</h2>
                                        <p class="text-secondary-soft mb-0">{{ $activeMeta['section_copy'] }}</p>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('admin.issuances.store') }}" class="row g-3" enctype="multipart/form-data">@csrf
                                    <div class="col-md-6">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Issuance Title</label>
                                            <input class="form-control" type="text" name="title" placeholder="Enter issuance title" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Category</label>
                                            <select class="form-select" name="category" required>@foreach ($categories as $category)<option value="{{ $category->name }}">{{ $category->name }}</option>@endforeach</select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Date Issued</label>
                                            <input class="form-control" type="date" name="date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Division</label>
                                            <input class="form-control" type="text" name="division" placeholder="Enter division name" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="admin-issuance-field admin-issuance-file-field">
                                            <label class="form-label">Attachment</label>
                                            <input class="form-control" type="file" name="document" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" required>
                                        </div>
                                    </div>
                                    <div class="col-12 admin-issuance-form-actions">
                                        <button class="btn btn-accent rounded-pill px-4" type="submit">Publish Issuance</button>
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

                                <form method="GET" action="{{ route('admin.workspace', ['tab' => 'issuances']) }}" class="admin-issuance-library-toolbar">
                                    <div class="admin-issuance-search-wrap">
                                        <i class="bi bi-search"></i>
                                        <input class="form-control" type="search" name="issuance_search" value="{{ $issuanceSearch ?? '' }}" placeholder="Search issuance title, category, or division...">
                                    </div>
                                    <button class="btn admin-public-btn rounded-pill px-4" type="submit">Search</button>
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
                                        <h2 class="h3 fw-bold mb-1">{{ $activeMeta['section_title'] }}</h2>
                                        <p class="text-secondary-soft mb-0">{{ $activeMeta['section_copy'] }}</p>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('admin.materials.store') }}" class="row g-3" enctype="multipart/form-data">@csrf
                                    <div class="col-md-6">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Material Title</label>
                                            <input class="form-control" type="text" name="title" placeholder="Enter material title" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Material Type</label>
                                            <input class="form-control" type="text" name="type" placeholder="Enter material type" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Date Published</label>
                                            <input class="form-control" type="date" name="date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="admin-issuance-field">
                                            <label class="form-label">Division</label>
                                            <input class="form-control" type="text" name="division" placeholder="Enter division name" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="admin-issuance-field admin-issuance-file-field">
                                            <label class="form-label">Attachment</label>
                                            <input class="form-control" type="file" name="document" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.mp4,.mov,.jpg,.jpeg,.png" required>
                                        </div>
                                    </div>
                                    <div class="col-12 admin-issuance-form-actions">
                                        <button class="btn btn-accent rounded-pill px-4" type="submit">Save Material</button>
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

                                @include('admin.partials.table-materials')
                            </div>
                        </div>
                    @else
                        <div class="admin-card admin-workspace-card mb-4">
                            <div class="admin-section-head">
                                <div class="admin-section-icon"><i class="bi {{ $activeMeta['icon'] }}"></i></div>
                                <div>
                                    <h2 class="h3 fw-bold mb-1">{{ $activeMeta['section_title'] }}</h2>
                                    <p class="text-secondary-soft mb-0">{{ $activeMeta['section_copy'] }}</p>
                                </div>
                            </div>

                            @if ($activeTab === 'messages')
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
                                        <span class="admin-message-count">Showing {{ $workspaceMessages->count() }} {{ \Illuminate\Support\Str::plural('message', $workspaceMessages->count()) }}</span>
                                        <button class="btn btn-accent rounded-pill px-4" type="submit">Apply</button>
                                    </div>
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

                            @if ($activeTab === 'messages')
                                <div class="admin-message-intro-grid">
                                    <div class="admin-message-intro-card">
                                        <div class="admin-message-intro-label">Inbox Status</div>
                                        <strong>{{ $messages->count() }}</strong>
                                        <span>Total captured contact messages from the public portal.</span>
                                    </div>
                                    <div class="admin-message-intro-card">
                                        <div class="admin-message-intro-label">Latest Sender</div>
                                        <strong>{{ optional($messages->first())->name ?? 'No messages yet' }}</strong>
                                        <span>{{ optional($messages->first())->email ?? 'Waiting for new inbox activity.' }}</span>
                                    </div>
                                </div>
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

                            @if ($activeTab === 'dx')
                                @include('admin.partials.table-dx')
                            @endif

                            @if ($activeTab === 'categories')
                                @include('admin.partials.table-categories')
                            @endif

                            @if ($activeTab === 'messages')
                                @include('admin.partials.table-messages')
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
