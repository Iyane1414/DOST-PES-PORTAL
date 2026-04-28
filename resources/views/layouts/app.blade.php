<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'DOST PES Portal' }}</title>
    <script>
        try {
            if (sessionStorage.getItem('portal-transition-pending') === '1') {
                document.documentElement.classList.add('portal-transition-boot');
                document.documentElement.dataset.portalTransitionTheme = sessionStorage.getItem('portal-transition-theme') || 'pes';
                document.documentElement.dataset.portalTransitionLabel = sessionStorage.getItem('portal-transition-label') || 'DOST PES';
            }
        } catch (error) {
            document.documentElement.classList.remove('portal-transition-boot');
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/portal.css') }}?v={{ filemtime(public_path('css/portal.css')) }}" rel="stylesheet">
</head>
<body class="@yield('body_class')" data-page-theme="@yield('page_theme', 'pes')" data-page-title="{{ $title ?? 'DOST PES Portal' }}">
    <div class="page-transition" data-page-transition aria-hidden="true">
        <div class="page-transition-curtain">
            <div class="page-transition-layer page-transition-layer-base"></div>
            <div class="page-transition-layer page-transition-layer-grid"></div>
            <div class="page-transition-layer page-transition-layer-glow"></div>
            <div class="page-transition-beam page-transition-beam-one"></div>
            <div class="page-transition-beam page-transition-beam-two"></div>
            <div class="page-transition-copy">
                <span class="page-transition-kicker">Navigating</span>
                <strong class="page-transition-title" data-page-transition-label></strong>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var transitionLabel = document.querySelector('[data-page-transition-label]');
            if (!transitionLabel) return;

            var bootLabel = document.documentElement.dataset.portalTransitionLabel;
            var fallbackLabel = document.body ? (document.body.dataset.pageTitle || 'DOST PES Portal') : 'DOST PES Portal';
            transitionLabel.textContent = (bootLabel && bootLabel.trim()) || fallbackLabel;
        }());
    </script>

    <div class="portal-page-content" data-page-content>
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/portal.js') }}?v={{ filemtime(public_path('js/portal.js')) }}"></script>
    @stack('scripts')
</body>
</html>
