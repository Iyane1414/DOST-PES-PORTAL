<section class="section-space contact-section" id="contact">
    <div class="contact-grid"></div>
    <div class="contact-glow"></div>
    <div class="container position-relative">
        <div class="text-center section-header contact-header">
            <span class="contact-eyebrow">Reach Out To PES</span>
            <h2 class="section-title split-title">Get in<br><span class="split-title-accent">Touch</span></h2>
            <p class="section-copy contact-copy">Questions, coordination needs, or document concerns. We’re ready to help during office hours.</p>
        </div>
        <div class="row g-4 g-xl-5 align-items-stretch">
            <div class="col-lg-5">
                <div class="contact-panel contact-info-panel h-100">
                    <div class="contact-panel-top">
                        <span class="contact-panel-chip">PES Contact Desk</span>
                        <h3 class="contact-panel-title">Contact Information</h3>
                        <p class="contact-panel-copy">Reach the Planning and Evaluation Service through our official office channels below.</p>
                    </div>

                    <div class="contact-stack">
                        <div class="contact-item">
                            <div class="contact-item-icon"><i class="bi bi-geo-alt"></i></div>
                            <div class="contact-item-body">
                                <strong>Office Address</strong>
                                <span>DOST Complex, Gen Santos Ave., Bicutan, Taguig City, Philippines</span>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-item-icon"><i class="bi bi-telephone"></i></div>
                            <div class="contact-item-body">
                                <strong>Phone Number</strong>
                                <span>+63 (2) 8837-2071 to 82</span>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-item-icon"><i class="bi bi-envelope"></i></div>
                            <div class="contact-item-body">
                                <strong>Email Address</strong>
                                <span>pes@dost.gov.ph</span>
                            </div>
                        </div>

                        <div class="contact-item contact-item-hours">
                            <div class="contact-item-icon"><i class="bi bi-clock"></i></div>
                            <div class="contact-item-body">
                                <strong>Office Hours</strong>
                                <span>Monday to Thursday</span>
                                <span>8:00AM to 5:00PM</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="contact-panel contact-form-panel">
                    <div class="contact-form-head">
                        <div>
                            <span class="contact-panel-chip">Send A Message</span>
                            <h3 class="contact-panel-title">Let’s start the conversation</h3>
                        </div>
                        <p class="contact-panel-copy mb-0">Fill out the form and the PES team can get back to you through your provided email.</p>
                    </div>

                    @if (session('contact_status'))
                        <div class="alert alert-success contact-alert">{{ session('contact_status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('portal.contact') }}" class="row g-3 contact-form">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input class="form-control form-control-lg contact-input" type="text" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input class="form-control form-control-lg contact-input" type="email" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Subject</label>
                            <input class="form-control form-control-lg contact-input" type="text" name="subject" value="{{ old('subject') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Message</label>
                            <textarea class="form-control form-control-lg contact-input contact-textarea" rows="5" name="message" required>{{ old('message') }}</textarea>
                        </div>
                        <div class="col-12 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                            <p class="contact-form-note mb-0">Please provide complete contact details so we can respond properly.</p>
                            <button class="btn btn-accent btn-lg rounded-pill px-4 contact-submit-btn" type="submit">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
