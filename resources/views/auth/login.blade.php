@extends('layouts.app')
@section('title', 'Sign In - JobFinder')

@section('content')
<div style="max-width: 440px; margin: 36px auto;">
    <div class="card" style="padding: 32px;">
        <div style="text-align: center; margin-bottom: 24px;">
            <div class="brand-icon-box" style="margin: 0 auto 10px; width:40px; height:40px; font-size:18px;">💼</div>
            <h1 style="font-size: 22px; margin-bottom: 4px;">Welcome Back</h1>
            <p class="muted" style="font-size: 13.5px;">Sign in to your JobFinder account</p>
        </div>

        <!-- Social Login Buttons -->
        <div style="display:flex; flex-direction:column; gap:8px; margin-bottom: 20px;">
            <a class="btn secondary" href="{{ route('social.redirect', 'google') }}" style="justify-content:center; gap:10px;">
                <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.7l3.1-3.1C17.3 1.8 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.3l3.7 2.9C6.5 7.4 9 5 12 5z"/><path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.6h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.9z"/><path fill="#FBBC05" d="M5.6 14.8c-.2-.7-.4-1.5-.4-2.3s.2-1.6.4-2.3L1.9 7.3C.7 9.7 0 12.3 0 15.1s.7 5.4 1.9 7.8l3.7-2.9z"/><path fill="#34A853" d="M12 24c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2.4-6.4-5.2L1.9 17C3.7 20.7 7.5 24 12 24z"/></svg>
                <span>Continue with Google</span>
            </a>
            <a class="btn secondary" href="{{ route('social.redirect', 'linkedin') }}" style="justify-content:center; gap:10px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="#0A66C2"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                <span>Continue with LinkedIn</span>
            </a>
        </div>

        <div style="display:flex; align-items:center; gap:12px; margin-bottom: 20px;">
            <div style="flex:1; height:1px; background:var(--border-light);"></div>
            <span class="muted" style="font-size:12px;">or sign in with email</span>
            <div style="flex:1; height:1px; background:var(--border-light);"></div>
        </div>

        <form class="form" method="post" action="{{ route('login.store') }}">
            @csrf

            <label>
                <span>Email Address</span>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="name@company.com" required autofocus>
            </label>

            <label>
                <span>Password</span>
                <input type="password" name="password" placeholder="••••••••" required>
            </label>

            <div style="display:flex; justify-content:space-between; align-items:center; font-size:13px; margin: 4px 0 6px;">
                <label style="display:flex; gap:6px; align-items:center; font-weight:normal; cursor:pointer;">
                    <input style="width:15px; height:15px; min-height:auto;" type="checkbox" name="remember" value="1">
                    <span>Remember me</span>
                </label>
            </div>

            <button class="btn" style="width:100%;" type="submit">Sign In</button>
        </form>

        <div style="text-align: center; margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--border-light); font-size: 13px;" class="muted">
            New to JobFinder?
            <a href="{{ route('register') }}" style="color:var(--primary); font-weight:600;">Create Account</a>
        </div>
    </div>
</div>
@endsection
