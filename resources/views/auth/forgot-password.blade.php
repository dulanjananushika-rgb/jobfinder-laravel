@extends('layouts.app')
@section('title', 'Forgot Password - JobFinder')

@section('content')
<div style="max-width: 440px; margin: 40px auto;">
    <div class="card" style="padding: 32px;">
        <div style="text-align: center; margin-bottom: 24px;">
            <div class="brand-icon-box" style="margin: 0 auto 10px; width:40px; height:40px; font-size:18px;">🔑</div>
            <h1 style="font-size: 22px; margin-bottom: 4px;">Forgot Password</h1>
            <p class="muted" style="font-size: 13.5px;">Enter your verified email address and we'll send you a secure password reset link.</p>
        </div>

        @if (session('status'))
            <div class="alert success" style="margin-bottom: 20px;">
                {{ session('status') }}
            </div>
        @endif

        <form class="form" method="post" action="{{ route('password.email') }}">
            @csrf

            <label>
                <span>Email Address</span>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="name@company.com" required autofocus>
            </label>

            <button class="btn" style="width:100%; margin-top: 10px;" type="submit">Send Reset Link</button>
        </form>

        <div style="text-align: center; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border-light); font-size: 13px;" class="muted">
            Remembered your password?
            <a href="{{ route('login') }}" style="color:var(--primary); font-weight:600;">Back to Sign In</a>
        </div>
    </div>
</div>
@endsection
