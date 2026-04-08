@php
    $portalHomeUrl = route('portal.home');
@endphp

<nav class="navbar navbar-expand-lg fixed-top portal-navbar" id="portal-navbar">
    <div class="container-fluid portal-navbar-shell">
        <a class="navbar-brand portal-brand" href="{{ route('portal.home') }}">
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
                <li class="nav-item"><a class="nav-link" href="{{ $portalHomeUrl }}#top">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $portalHomeUrl }}#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $portalHomeUrl }}#issuances">Issuances</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $portalHomeUrl }}#materials">Resources</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $portalHomeUrl }}#dost-dx">DOST DX</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $portalHomeUrl }}#dost-gates">DOST GATES</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $portalHomeUrl }}#contact">Contact</a></li>
            </ul>
            <div class="portal-navbar-actions">
                <button class="btn btn-theme-toggle" type="button" id="theme-toggle"><i class="bi bi-moon-stars-fill"></i></button>
                <a class="btn btn-dark rounded-pill px-4 portal-admin-btn" href="{{ route('admin.dashboard') }}">Admin</a>
            </div>
        </div>
    </div>
</nav>
