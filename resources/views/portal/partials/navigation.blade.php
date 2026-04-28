@php
    $portalHomeUrl = route('portal.home');
@endphp

<nav class="navbar navbar-expand-lg fixed-top portal-navbar" id="portal-navbar">
    <div class="container-fluid portal-navbar-shell">
        <a class="navbar-brand portal-brand" href="{{ route('portal.home') }}" data-magnetic data-transition-label="DOST PES">
            <img src="{{ asset('images/bagongpilipinas.png') }}" alt="Bagong Pilipinas logo" class="portal-brand-logo portal-brand-logo-side">
            <span class="portal-brand-logo-stack">
                <img src="{{ asset('images/DOST LOGO light.png') }}" alt="DOST light mode logo" class="portal-brand-logo portal-brand-logo-main portal-brand-logo-default">
                <img src="{{ asset('images/DOST LOGO dark.png') }}" alt="DOST dark mode logo" class="portal-brand-logo portal-brand-logo-main portal-brand-logo-light">
            </span>
        </a>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse portal-navbar-collapse" id="mainNav">
            <ul class="navbar-nav align-items-lg-center portal-navbar-menu">
                <li class="nav-item"><a class="nav-link" href="{{ $portalHomeUrl }}#top" data-magnetic>Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $portalHomeUrl }}#about" data-magnetic>About</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $portalHomeUrl }}#issuances" data-magnetic>Issuances</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $portalHomeUrl }}#materials" data-magnetic>Resources</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $portalHomeUrl }}#dost-dx" data-magnetic>DOST DX</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $portalHomeUrl }}#dost-gates" data-magnetic>GATES Project 1</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $portalHomeUrl }}#contact" data-magnetic>Contact</a></li>
            </ul>
            <div class="portal-navbar-actions">
                <div class="dropdown portal-settings-dropdown">
                    <button
                        class="btn portal-settings-toggle"
                        type="button"
                        id="portal-settings-toggle"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="outside"
                        data-magnetic
                        aria-expanded="false"
                        aria-label="Open settings menu"
                    >
                        <i class="bi bi-gear-fill"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end portal-settings-menu" aria-labelledby="portal-settings-toggle">
                        <div class="portal-settings-item portal-settings-theme-row">
                            <div class="portal-settings-item-main">
                                <span class="portal-settings-item-icon"><i class="bi bi-moon-stars-fill" id="theme-toggle-icon"></i></span>
                                <div class="portal-settings-item-copy">
                                    <span class="portal-settings-item-label" id="theme-toggle-label">Dark Mode</span>
                                    <span class="portal-settings-item-meta">Switch portal appearance</span>
                                </div>
                            </div>
                            <button class="portal-theme-switch" type="button" id="theme-toggle" role="switch" aria-checked="false" aria-label="Toggle dark mode">
                                <span class="portal-theme-switch-thumb"></span>
                            </button>
                        </div>
                        <a class="dropdown-item portal-settings-item portal-settings-link" href="{{ route('admin.dashboard') }}">
                            <div class="portal-settings-item-main">
                                <span class="portal-settings-item-icon portal-settings-item-icon-logo">
                                    <span class="portal-admin-logo-stack">
                                        <img src="{{ asset('images/admin light.png') }}" alt="Admin logo for light mode" class="portal-admin-logo portal-admin-logo-default">
                                        <img src="{{ asset('images/admin dark.png') }}" alt="Admin logo for dark mode" class="portal-admin-logo portal-admin-logo-light">
                                    </span>
                                </span>
                                <div class="portal-settings-item-copy">
                                    <span class="portal-settings-item-label">Admin</span>
                                    <span class="portal-settings-item-meta">Open dashboard</span>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right portal-settings-item-arrow"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
