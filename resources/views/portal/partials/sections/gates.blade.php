<section class=gates id="gates" data-scroll-scene="gates">

    <div class="container">
        <div class="gates-hero">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <div class=""gates-copy-wrap>
                        <span class="eyebrow text-accent">DOST PES Program</span>
                        <h2 class="gates-title">Geospatial Analytics<br><span class="gates-title-accent">&amp; Technology Solutions</span>
                        </h2>
                        <p class="gates-desc mb-0">
                            The Geospatial Analytics and Technology Solutiions (GATES) Program is one of the eight (8) transformative research and development initiatives of the Department of Science and Technology (DOST). It is designed to <strong>harness the full potential of the Department's geospatial data for the data-driven decision-making, research, and innovation.</strong>
                        </p>
                        <div class="gates-hero-action">
                            <button class="gates-btn-primary" type="button"
                                    data-bs-toggle="modal" data-bs-target="#gatesOverviewModal">
                                Learn More <i class="bi bi-chevron-right ms-1"></i>
                            </button>
                            <a class="gates-btn-outline" href="#gates-gallery">
                                View Gallery <i class="bi bi-images ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="logo-col col-lg-6">
                    <img src="{{ asset('images/gates_logo_nobg.png') }}" alt="GATES Program" class="gates-hero-img">
                </div>
            </div>
        </div>
        <section class="gates-gallery-section" id="gates-gallery">
            <div class="gates-gallery-header">
                <span class="gates-eyebrow">Program Materials</span>
                <h3 class="gates-section-title">
                    Photos, Videos<br>
                    <span class="gates-title-accent">&amp; Documents</span>
                </h3>
                <p class="gates-desc">
                    Browse all uploaded materials from the GATES Program.
                </p>
            </div>

            <div class="gates-tabbar">
                <button class="gates-tab is-active" data-gates-filter="all">All</button>
                <button class="gates-tab gates-tab-blue"   data-gates-filter="photo">Photos</button>
                <button class="gates-tab gates-tab-purple" data-gates-filter="video">Videos</button>
                <button class="gates-tab gates-tab-orange" data-gates-filter="pdf">PDF</button>
                <button class="gates-tab gates-tab-teal"   data-gates-filter="docx">Word</button>
                <button class="gates-tab gates-tab-orange" data-gates-filter="ppt">PowerPoint</button>
            </div>
        </section>
    </div>
    <div class="gates-lightbox" id="gatesLightbox" onclick="closeGatesLightbox(event)">
        <div class="gates-lightbox-inner">
            <button class="gates-lb-close" onclick="closeGatesLightboxDirect()">
                <i class="bi bi-x-lg"></i>
            </button>
            <div id="gatesLightboxMedia"></div>
            <div class="gates-lb-caption">
                <strong class="gates-lb-title" id="gatesLightboxTitle"></strong>
                <p class="gates-lb-desc" id="gatesLightboxDesc"></p>
            </div>
        </div>
    </div>
</section>
<div class="modal fade" id="gatesOverviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content gates-modal-content">
            <div class="modal-header gates-modal-header">
                <div>
                    <span class="gates-eyebrow" style="font-size:.65rem;">DOST PES Program</span>
                    <h3 class="gates-modal-title">What is GATES Project 1?</h3>
                    <div class="gates-modal-subtitle">Navigating the Ocean Data</div>
                </div>
                <button class="btn" type="button" data-bs-dismiss="modal" style="color:rgba(255,255,255,0.5);font-size:1.1rem;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body" style="padding:24px 28px;">
                <p style="font-size:.92rem; line-height:1.8; color:#000; margin:0;">
                    <strong>Project 1</strong>, the foundational phase of the GATES Program, lays the groundwork for transforming raw data into actionable insights. Led by DOST Central Office Planning and Evaluation Service (PES), it conducts geospatial data discovery, mapping, cleansing, and standardization, and develops the AI-ready data architecture to support advanced analytics.<br><br>
                    <span style="color: #1C0DFC;">OBJECTIVES<br></span>
                    To systematically collect, map, cleanse, and standardize the geospatial datasets across DOST, creating an AI-ready data architecture that enhances data-driven decision-making, fosters collaboration, and supports the integration of advanced technologies for national development.
                </p>
            </div>
            <div class="modal-footer gates-modal-footer">
                <button class="gates-btn-primary" type="button" data-bs-dismiss="modal"
                        onclick="document.getElementById('gates-gallery').scrollIntoView({behavior:'smooth'})">
                    View Gallery <i class="bi bi-images ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</div>