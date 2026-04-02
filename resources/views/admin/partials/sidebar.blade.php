@php
    $activeSection = $activeSection ?? 'workspace';
    $sidebarTabIcons = [
        'issuances' => 'bi-briefcase',
        'materials' => 'bi-collection-play',
        'dx' => 'bi-cpu',
        'messages' => 'bi-chat-left-text',
        'ai' => 'bi-robot',
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
        <div class="admin-nav-label">Workspace</div>
        <div class="nav flex-column nav-pills gap-2">
            @foreach (['issuances' => 'Issuances', 'materials' => 'Materials', 'dx' => 'DOST DX', 'messages' => 'Messages', 'ai' => 'AI Agent'] as $tab => $label)
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

    <div class="admin-sidebar-note">
        <div class="admin-sidebar-note-title">Administration Guide</div>
        <p class="mb-0">Manage public-facing PES content, monitor inbound engagement, and keep the portal current.</p>
        <form method="POST" action="{{ route('admin.logout') }}" class="admin-sidebar-logout">
            @csrf
            <button class="btn btn-outline-danger w-100 rounded-pill" type="submit">
                <i class="bi bi-power me-2"></i>Logout
            </button>
        </form>
    </div>
</aside>
