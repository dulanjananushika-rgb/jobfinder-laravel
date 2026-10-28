@extends('layouts.app')
@section('title', 'Reset Password - JobFinder')

@section('content')
<div style="max-width: 440px; margin: 40px auto;">
    <div class="card" style="padding: 32px;">
        <div style="text-align: center; margin-bottom: 24px;">
            <div class="brand-icon-box" style="margin: 0 auto 10px; width:40px; height:40px; font-size:18px;">🔒</div>
            <h1 style="font-size: 22px; margin-bottom: 4px;">Set New Password</h1>
            <p class="muted" style="font-size: 13.5px;">Choose a strong password with at least 8 characters, numbers, and symbols.</p>
        </div>

        <form class="form" method="post" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <label>
                <span>Email Address</span>
                <input type="email" name="email" value="{{ old('email', $email) }}" required autofocus>
            </label>

            <label>
                <span>New Password</span>
                <input type="password" name="password" placeholder="••••••••" required>
            </label>

            <label>
                <span>Confirm New Password</span>
                <input type="password" name="password_confirmation" placeholder="••••••••" required>
            </label>

            <button class="btn" style="width:100%; margin-top: 10px;" type="submit">Reset Password</button>
        </form>
    </div>
</div>
@endsection
