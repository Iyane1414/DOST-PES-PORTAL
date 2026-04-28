
@php
    $activeSection = $activeSection ?? 'workspace';
    $sidebarTabIcons = [
        'issuances' => 'bi-briefcase',
        'materials' => 'bi-collection-play',
        'news' => 'bi-newspaper',
        'dx' => 'bi-cpu',
        'roadmap' => 'bi-signpost-split',
        'messages' => 'bi-chat-left-text',
        'ai' => 'bi-robot',
        'gates-projects' => 'bi-folder',
        'gates-issuances' => 'bi-file-earmark-text',
        'gates-news' => 'bi-megaphone',
    ];
@endphp

<aside class="admin-sidebar admin-sidebar-enhanced">
    <div class="admin-brand">
        <div class="admin-brand-mark">
            <img src="{{ asset('images/dostlogo.png') }}" alt="DOST logo" class="admin-brand-logo">
        </div>
        <div>
            <div class="admin-brand-title">DOST Admin</div>
            <div class="admin-brand-copy">Planning and Evaluation Service</div>
        </div>
    </div>

    <div class="admin-nav-group">
        <div class="admin-nav-label">Overview</div>
        <div class="nav flex-column nav-pills gap-2">
            <a href="{{ route('admin.dashboard') }}" class="nav-link admin-nav-link @if ($activeSection === 'overview') active @endif">
                <span class="admin-nav-link-main">
                    <span class="admin-nav-link-icon">
                        <i class="bi bi-grid"></i>
                    </span>
                    <span>Dashboard Home</span>
                </span>
                <i class="bi bi-chevron-right admin-nav-link-arrow"></i>
            </a>
        </div>
    </div>

    <div class="admin-nav-group">
        <div class="admin-nav-label">PES Workspace</div>
        <div class="nav flex-column nav-pills gap-2">
            @foreach (['issuances' => 'Issuances', 'materials' => 'Materials', 'news' => 'PES News', 'dx' => 'DOST DX', 'roadmap' => 'DX Roadmap', 'messages' => 'Messages'] as $tab => $label)
                <a href="{{ route('admin.workspace', ['tab' => $tab]) }}" class="nav-link admin-nav-link @if (($activeTab ?? null) === $tab) active @endif">
                    <span class="admin-nav-link-main">
                        <span class="admin-nav-link-icon">
                            <i class="bi {{ $sidebarTabIcons[$tab] ?? 'bi-circle' }}"></i>
                        </span>
                        <span>{{ $label }}</span>
                    </span>
                    <i class="bi bi-chevron-right admin-nav-link-arrow"></i>
                </a>
            @endforeach
        </div>
    </div>

    <div class="admin-nav-group">
        <div class="admin-nav-label">GATES Project 1</div>
        <div class="nav flex-column nav-pills gap-2">
            @foreach (['gates-projects' => 'Projects', 'gates-issuances' => 'Issuances', 'gates-news' => 'Project 1 News'] as $tab => $label)
                <a href="{{ route('admin.workspace', ['tab' => $tab]) }}" class="nav-link admin-nav-link @if (($activeTab ?? null) === $tab) active @endif">
                    <span class="admin-nav-link-main">
                        <span class="admin-nav-link-icon">
                            <i class="bi {{ $sidebarTabIcons[$tab] ?? 'bi-circle' }}"></i>
                        </span>
                        <span>{{ $label }}</span>
                    </span>
                    <i class="bi bi-chevron-right admin-nav-link-arrow"></i>
                </a>
            @endforeach
        </div>
    </div>

    <div class="admin-nav-group mt-auto">
        <form method="POST" action="{{ route('admin.logout') }}" class="w-100">
            @csrf
            <button class="btn btn-outline-danger w-100 rounded-pill" type="submit">
                <i class="bi bi-power me-2"></i>Logout
            </button>
        </form>
    </div>
</aside>
