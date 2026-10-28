@extends('layouts.app')
@section('title', 'Create Account - JobFinder')

@section('content')
<div style="max-width: 640px; margin: 28px auto;">
    <div class="card" style="padding: 32px;">
        <div style="text-align: center; margin-bottom: 24px;">
            <div class="brand-icon-box" style="margin: 0 auto 10px; width:40px; height:40px; font-size:18px;">💼</div>
            <h1 style="font-size: 24px; margin-bottom: 4px;">Create an Account</h1>
            <p class="muted" style="font-size: 13.5px;">Join Sri Lanka's verified employment network</p>
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
                    <span>Account Category <span style="color:#ef4444">*</span></span>
                    <select name="role" required>
                        <option value="job_seeker" @selected(old('role') === 'job_seeker')>Job Seeker (Candidate)</option>
                        <option value="employer" @selected(old('role') === 'employer')>Employer (Company)</option>
                    </select>
                </label>
            </div>

            <label>
                <span>Company Name <span class="muted" style="font-weight:normal;">(Required for Employers)</span></span>
                <input name="company_name" value="{{ old('company_name') }}" placeholder="e.g. Ceylon Commercial Group (Pvt) Ltd">
            </label>

            <div class="grid grid-2">
                <label>
                    <span>Password <span style="color:#ef4444">*</span></span>
                    <input type="password" name="password" placeholder="At least 8 characters" required>
                </label>

                <label>
                    <span>Confirm Password <span style="color:#ef4444">*</span></span>
                    <input type="password" name="password_confirmation" placeholder="Confirm password" required>
                </label>
            </div>

            <button class="btn" style="width:100%; margin-top:6px;" type="submit">Create Account</button>
        </form>

        <div style="text-align: center; margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--border-light); font-size: 13px;" class="muted">
            Already registered?
            <a href="{{ route('login') }}" style="color:var(--primary); font-weight:600;">Sign In</a>
        </div>
    </div>
</div>
@endsection
