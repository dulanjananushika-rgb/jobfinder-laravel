@extends('layouts.app')
@section('title', 'Create Account - JobFinder')

@section('content')
<div style="max-width: 680px; margin: 30px auto;">
    <div class="card" style="padding: 36px;">
        <div style="text-align: center; margin-bottom: 24px;">
            <div class="brand-mark" style="margin: 0 auto 12px; width:44px; height:44px; font-size:18px;">JF</div>
            <h1 style="font-size: 26px; margin-bottom: 6px;">Create Your Account</h1>
            <p class="muted" style="font-size: 14px;">Join Sri Lanka's leading verified employment network</p>
        </div>

        <form class="form" method="post" action="{{ route('register.store') }}">
            @csrf

            <div class="grid grid-2">
                <label>
                    <span>Full Name <span style="color:#ef4444">*</span></span>
                    <input name="name" value="{{ old('name') }}" placeholder="John Doe" required autofocus>
                </label>

                <label>
                    <span>Email Address <span style="color:#ef4444">*</span></span>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="john@example.com" required>
                </label>

                <label>
                    <span>Mobile Phone (Sri Lanka) <span style="color:#ef4444">*</span></span>
                    <input name="phone" value="{{ old('phone') }}" placeholder="0771234567" required>
                </label>

                <label>
                    <span>I am registering as: <span style="color:#ef4444">*</span></span>
                    <select name="role" required>
                        <option value="job_seeker" @selected(old('role') === 'job_seeker')>Job Seeker (Candidate)</option>
                        <option value="employer" @selected(old('role') === 'employer')>Employer (Hiring Company)</option>
                    </select>
                </label>
            </div>

            <label>
                <span>Company Name <span class="muted" style="font-weight:normal;">(Required if Employer)</span></span>
                <input name="company_name" value="{{ old('company_name') }}" placeholder="Acme Technologies (Pvt) Ltd">
            </label>

            <div class="grid grid-2">
                <label>
                    <span>Password <span style="color:#ef4444">*</span></span>
                    <input type="password" name="password" placeholder="Min. 8 characters" required>
                </label>

                <label>
                    <span>Confirm Password <span style="color:#ef4444">*</span></span>
                    <input type="password" name="password_confirmation" placeholder="Repeat password" required>
                </label>
            </div>

            <button class="btn" style="width:100%; margin-top:8px;" type="submit">Complete Registration →</button>
        </form>

        <div style="text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-color); font-size: 13.5px;" class="muted">
            Already have an account?
            <a href="{{ route('login') }}" style="color:var(--brand-primary); font-weight:700;">Sign In</a>
        </div>
    </div>
</div>
@endsection
