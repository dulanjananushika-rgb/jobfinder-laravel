@extends('layouts.app')
@section('title', 'Verify Your Email - JobFinder')

@section('content')
<div style="max-width: 500px; margin: 40px auto;">
    <div class="card" style="padding: 32px; text-align: center;">
        <div class="brand-icon-box" style="margin: 0 auto 12px; width:44px; height:44px; font-size:20px;">✉️</div>
        <h1 style="font-size: 22px; margin-bottom: 8px;">Verify Your Email Address</h1>
        <p class="muted" style="font-size: 14px; line-height: 1.6; margin-bottom: 24px;">
            Thanks for joining JobFinder! Before getting started, please check your inbox and verify your email address by clicking on the link we just sent to:
            <strong style="color:var(--text); display:block; margin-top:6px;">{{ auth()->user()->email }}</strong>
        </p>

        @if (session('status') == 'verification-link-sent' || session('status'))
            <div class="alert success" style="margin-bottom: 20px; font-size:13.5px;">
                {{ session('status') == 'verification-link-sent' ? 'A new verification link has been sent to your email.' : session('status') }}
            </div>
        @endif

        <div style="display:flex; flex-direction:column; gap:12px; max-width:320px; margin:0 auto;">
            <form method="post" action="{{ route('verification.send') }}">
                @csrf
                <button class="btn" style="width:100%; justify-content:center;" type="submit">Resend Verification Email</button>
            </form>

            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button class="btn secondary" style="width:100%; justify-content:center;" type="submit">Sign Out</button>
            </form>
        </div>
    </div>
</div>
@endsection
