<section class="section-space subscribe-section">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6"><h2 class="section-title text-white">Stay proactive with PES.</h2><p class="text-white-50 mb-0">Subscribe to receive updates about new issuances, materials, and DOST DX developments.</p></div>
            <div class="col-lg-6">
                @if (session('subscription_status')) <div class="alert alert-light">{{ session('subscription_status') }}</div> @endif
                <form method="POST" action="{{ route('portal.subscribe') }}" class="subscription-form">@csrf<input class="form-control form-control-lg border-0" type="email" name="email" placeholder="Enter your email" required><button class="btn btn-light btn-lg text-accent fw-bold" type="submit">Subscribe</button></form>
            </div>
        </div>
    </div>
</section>
