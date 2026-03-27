@extends('layouts.app', ['title' => 'Admin Login'])

@section('body_class', 'admin-auth-page')

@section('content')
    <div class="admin-auth-shell">
        <div class="admin-auth-aurora aurora-one"></div>
        <div class="admin-auth-aurora aurora-two"></div>
        <div class="admin-auth-mesh"></div>

        <div class="admin-auth-card admin-auth-card-enhanced">
            <div class="admin-auth-badge">
                <span class="admin-auth-badge-icon">
                    <img src="{{ asset('images/dostlogo.png') }}" alt="DOST logo" class="admin-auth-badge-logo">
                </span>
                <span>Administrative Portal</span>
            </div>

            <div class="admin-auth-card-glow glow-one"></div>
            <div class="admin-auth-card-glow glow-two"></div>
            <div class="admin-auth-card-mesh"></div>

            <h1 class="admin-auth-title">Admin Access</h1>
            <p class="admin-auth-copy">Enter the administrative password to manage the PES portal through a secure government-tech workspace.</p>

            @if ($errors->any())
                <div class="alert alert-danger admin-auth-alert">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('admin.login.store') }}" class="admin-auth-form">
                @csrf
                <div class="admin-auth-field">
                    <span class="admin-auth-field-icon"><i class="bi bi-key"></i></span>
                    <input class="form-control form-control-lg admin-auth-input" type="password" name="password" placeholder="Enter password" required>
                </div>
                <button class="btn admin-auth-submit btn-lg w-100 rounded-4" type="submit">Enter Admin Portal</button>
            </form>

            <div class="admin-auth-footer">
                <div class="admin-auth-footer-line"></div>
                <a class="admin-auth-back" href="{{ route('portal.home') }}">Back to Public Portal</a>
            </div>
        </div>
    </div>
@endsection
