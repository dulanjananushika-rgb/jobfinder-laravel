@extends('layouts.app')
@section('title', 'Sign In - JobFinder')

@section('content')
<div style="max-width: 460px; margin: 40px auto;">
    <div class="card" style="padding: 36px;">
        <div style="text-align: center; margin-bottom: 24px;">
            <div class="brand-mark" style="margin: 0 auto 12px; width:44px; height:44px; font-size:18px;">JF</div>
            <h1 style="font-size: 24px; margin-bottom: 6px;">Welcome Back</h1>
            <p class="muted" style="font-size: 14px;">Sign in to your JobFinder account</p>
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

            <div style="display:flex; justify-content:space-between; align-items:center; font-size:13.5px; margin: 4px 0 8px;">
                <label style="display:flex; gap:8px; align-items:center; font-weight:normal; cursor:pointer;">
                    <input style="width:16px; height:16px; min-height:auto;" type="checkbox" name="remember" value="1">
                    <span>Remember me</span>
                </label>
            </div>

            <button class="btn" style="width:100%;" type="submit">Sign In →</button>
        </form>

        <div style="text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-color); font-size: 13.5px;" class="muted">
            Don't have an account yet?
            <a href="{{ route('register') }}" style="color:var(--brand-primary); font-weight:700;">Create Account</a>
        </div>
    </div>
</div>
@endsection
