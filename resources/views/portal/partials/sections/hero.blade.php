@php
    $heroCards = [
        [
            'id' => 'pes',
            'title' => 'PES Planning and Evaluation Service',
            'eyebrow' => 'Core Office',
            'logo' => 'images/dostlogo.png',
            'logo_label' => '',
            'art' => 'chart',
            'description' => 'Planning and Evaluation Service (PES) leads strategic foresight, planning, monitoring, and evaluation efforts that strengthen decision-making across the DOST system. It helps align programs, projects, and policies with institutional priorities through data-driven insights and performance review.',
        ],
        [
            'id' => 'dx',
            'title' => 'DOST DX',
            'eyebrow' => 'Digital Transformation',
            'logo' => 'images/dostdx.png',
            'logo_label' => '',
            'art' => 'cube',
            'description' => 'DOST DX is the digital transformation initiative of the DOST system. It serves as a blueprint for modernizing government services through interoperable platforms, stronger data governance, and citizen-centered digital delivery across the Department.',
        ],
        [
            'id' => 'gates',
            'title' => 'GATES Project 1',
            'eyebrow' => 'Geospatial Analytics',
            'logo' => 'images/GATES LOGO.png',
            'logo_label' => '',
            'art' => 'people',
            'description' => 'GATES Project 1 advances geospatial analytics, AI-enabled insights, and integrated data systems for DOST. It supports mapping, analytics, and decision tools that help agencies work from unified datasets and more actionable intelligence.',
        ],
    ];
@endphp

<section class="hero-section" id="top" data-scroll-scene="hero">
    <div class="hero-orb orb-left"></div>
    <div class="hero-orb orb-right"></div>
    <div class="hero-grid-overlay" aria-hidden="true"></div>
    <div class="hero-streak" aria-hidden="true"></div>
    <div class="hero-pointer-glow" id="hero-pointer-glow" aria-hidden="true"></div>
    <div class="hero-cloud hero-cloud-one" aria-hidden="true"></div>
    <div class="hero-cloud hero-cloud-two" aria-hidden="true"></div>
    <div class="hero-cloud hero-cloud-three" aria-hidden="true"></div>
    <div class="hero-cloud hero-cloud-four" aria-hidden="true"></div>
    <div class="hero-glow-beam hero-glow-beam-one" aria-hidden="true"></div>
    <div class="hero-glow-beam hero-glow-beam-two" aria-hidden="true"></div>
    <div class="hero-visual" aria-hidden="true">
        <img class="hero-building hero-building-light" src="{{ asset('images/halfbglight.png') }}" alt="">
        <img class="hero-building hero-building-dark" src="{{ asset('images/nightmode_bg.png') }}" alt="">
    </div>

    <div class="container text-start position-relative">
        <div class="hero-layout">
            <div class="hero-content-shell">
                <span class="eyebrow hero-pill">Department of Science and Technology</span>
                <h1 class="display-1 fw-black hero-title">Planning &amp;<br><span class="text-muted-hero">Evaluation<br>Service</span></h1>
                <p class="hero-copy">The strategic backbone of the organization, driving forward-looking planning, advancing DOST’s digital transformation, and ensuring the effective monitoring and evaluation of programs and projects</p>
                <div class="d-flex flex-column flex-sm-row justify-content-start gap-3 hero-actions">
                    <a class="btn btn-accent btn-lg rounded-pill px-4 hero-cta" href="#mandate" data-magnetic data-ripple>Explore Mandate <i class="bi bi-arrow-right-short" aria-hidden="true"></i></a>
                    <a class="btn btn-dark btn-lg rounded-pill px-4 hero-cta hero-cta-secondary" href="#dost-dx" data-magnetic data-ripple>DOST DX Initiatives <i class="bi bi-arrow-right-short" aria-hidden="true"></i></a>
                </div>
            </div>

        </div>

        <div class="hero-card-row" aria-label="Featured portal areas">
            @foreach ($heroCards as $card)
                <button
                    class="hero-feature-card"
                    type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#heroInfoModal-{{ $card['id'] }}"
                >
                    <span class="hero-feature-eyebrow">{{ $card['eyebrow'] }}</span>
                    <span class="hero-feature-menu" aria-hidden="true"><i class="bi bi-three-dots"></i></span>
                    <span class="hero-feature-logo-row">
                        <span class="hero-feature-logo-wrap">
                            <img src="{{ asset($card['logo']) }}" alt="{{ $card['title'] }} logo" class="hero-feature-logo hero-feature-logo-{{ $card['id'] }}">
                        </span>
                        @if ($card['logo_label'] !== '')
                            <span class="hero-feature-logo-label">{{ $card['logo_label'] }}</span>
                        @endif
                    </span>
                    <span class="hero-feature-accent" aria-hidden="true"></span>
                    <span class="hero-feature-title">{{ $card['title'] }}</span>
                    <span class="hero-feature-link">View description <i class="bi bi-arrow-up-right"></i></span>
                    <span class="hero-feature-art-wrap hero-feature-art-{{ $card['art'] }}" aria-hidden="true">
                        @if ($card['art'] === 'chart')
                            <span class="hero-art-chart">
                                <span class="hero-art-chart-line"></span>
                                <span class="hero-art-chart-dot dot-one"></span>
                                <span class="hero-art-chart-dot dot-two"></span>
                                <span class="hero-art-chart-dot dot-three"></span>
                                <span class="hero-art-chart-dot dot-four"></span>
                                <span class="hero-art-chart-bar bar-one"></span>
                                <span class="hero-art-chart-bar bar-two"></span>
                                <span class="hero-art-chart-bar bar-three"></span>
                                <span class="hero-art-chart-bar bar-four"></span>
                                <span class="hero-art-base"></span>
                            </span>
                        @elseif ($card['art'] === 'cube')
                            <span class="hero-art-cubes">
                                <span class="hero-art-cube cube-a"></span>
                                <span class="hero-art-cube cube-b"></span>
                                <span class="hero-art-cube cube-c"></span>
                                <span class="hero-art-cube cube-d"></span>
                                <span class="hero-art-cube cube-e"></span>
                                <span class="hero-art-cube cube-f"></span>
                            </span>
                        @elseif ($card['art'] === 'people')
                            <span class="hero-art-people">
                                <span class="hero-art-person person-left"></span>
                                <span class="hero-art-person person-center"></span>
                                <span class="hero-art-person person-right"></span>
                            </span>
                        @endif
                    </span>
                </button>
            @endforeach
        </div>
    </div>
</section>

@foreach ($heroCards as $card)
    <div class="modal fade" id="heroInfoModal-{{ $card['id'] }}" tabindex="-1" aria-labelledby="heroInfoModalLabel-{{ $card['id'] }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content hero-info-modal">
                <div class="modal-body">
                    <button class="btn-close hero-info-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="hero-info-head">
                        <span class="hero-info-pill">{{ $card['eyebrow'] }}</span>
                        <img src="{{ asset($card['logo']) }}" alt="{{ $card['title'] }} logo" class="hero-info-logo">
                    </div>
                    <h3 class="hero-info-title" id="heroInfoModalLabel-{{ $card['id'] }}">{{ $card['title'] }}</h3>
                    <p class="hero-info-copy mb-0">{{ $card['description'] }}</p>
                </div>
            </div>
        </div>
    </div>
@endforeach
