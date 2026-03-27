<section class="section-space" id="contact">
    <div class="container">
        <div class="text-center section-header"><h2 class="section-title split-title">Get in<br><span class="split-title-accent">Touch</span></h2><p class="section-copy">Have questions? We're here to help.</p></div>
        <div class="row g-5">
            <div class="col-lg-5"><div class="mini-card h-100"><h3 class="h4 mb-4">Contact Information</h3><div class="contact-item"><div class="icon-chip"><i class="bi bi-geo-alt"></i></div><div><strong>Office Address</strong><br><span class="text-secondary-soft">DOST Complex, Gen. Santos Ave., Bicutan, Taguig City, Philippines</span></div></div><div class="contact-item"><div class="icon-chip"><i class="bi bi-telephone"></i></div><div><strong>Phone Number</strong><br><span class="text-secondary-soft">+63 (2) 8837-2071 to 82</span></div></div><div class="contact-item mb-0"><div class="icon-chip"><i class="bi bi-envelope"></i></div><div><strong>Email Address</strong><br><span class="text-secondary-soft">pes@dost.gov.ph</span></div></div></div></div>
            <div class="col-lg-7">
                <div class="feature-card">
                    @if (session('contact_status')) <div class="alert alert-success">{{ session('contact_status') }}</div> @endif
                    <form method="POST" action="{{ route('portal.contact') }}" class="row g-3">
                        @csrf
                        <div class="col-md-6"><label class="form-label">Name</label><input class="form-control form-control-lg" type="text" name="name" value="{{ old('name') }}" required></div>
                        <div class="col-md-6"><label class="form-label">Email</label><input class="form-control form-control-lg" type="email" name="email" value="{{ old('email') }}" required></div>
                        <div class="col-12"><label class="form-label">Subject</label><input class="form-control form-control-lg" type="text" name="subject" value="{{ old('subject') }}" required></div>
                        <div class="col-12"><label class="form-label">Message</label><textarea class="form-control form-control-lg" rows="5" name="message" required>{{ old('message') }}</textarea></div>
                        <div class="col-12"><button class="btn btn-accent btn-lg rounded-pill px-4" type="submit">Send Message</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
