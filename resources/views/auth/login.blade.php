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
