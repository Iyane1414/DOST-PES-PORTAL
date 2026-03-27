@php
    $portalHomeUrl = route('portal.home');
@endphp

<nav class="navbar navbar-expand-lg fixed-top portal-navbar" id="portal-navbar">
    <div class="container">
        <a class="navbar-brand portal-brand" href="{{ route('portal.home') }}">
            <img src="{{ asset('images/bagongpilipinas.png') }}" alt="Bagong Pilipinas logo" class="portal-brand-logo portal-brand-logo-side">
            <span class="portal-brand-logo-stack">
                <img src="{{ asset('images/dostlogoo.png') }}" alt="DOST logo" class="portal-brand-logo portal-brand-logo-main portal-brand-logo-default">
                <img src="{{ asset('images/lightlogo.png') }}" alt="DOST light logo" class="portal-brand-logo portal-brand-logo-main portal-brand-logo-light">
            </span>
        </a>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
                <li class="nav-item"><a class="nav-link" href="{{ $portalHomeUrl }}#top">Home</a></li>
                <li class="nav-item portal-nav-dropdown">
                    <button class="nav-link portal-nav-toggle" type="button" aria-expanded="false">
                        <span>About</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="portal-nav-menu">
                        <a class="portal-nav-menu-link" href="{{ $portalHomeUrl }}#mandate" data-about-panel="mandate">Mandate</a>
                        <a class="portal-nav-menu-link" href="{{ $portalHomeUrl }}#organizational-structure" data-about-panel="organization">Organizational Structure</a>
                        <a class="portal-nav-menu-link" href="{{ $portalHomeUrl }}#pes-action">PES in Action</a>
                    </div>
                </li>
                <li class="nav-item"><a class="nav-link" href="{{ $portalHomeUrl }}#issuances">Issuances</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $portalHomeUrl }}#materials">Resources</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $portalHomeUrl }}#dost-dx">DOST DX</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $portalHomeUrl }}#contact">Contact</a></li>
                <li class="nav-item"><button class="btn btn-theme-toggle" type="button" id="theme-toggle"><i class="bi bi-moon-stars-fill"></i></button></li>
                <li class="nav-item"><a class="btn btn-dark rounded-pill px-4" href="{{ route('admin.dashboard') }}">Admin</a></li>
            </ul>
        </div>
    </div>
</nav>
