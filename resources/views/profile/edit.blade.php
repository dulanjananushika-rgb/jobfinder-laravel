@extends('layouts.app')
@section('title', 'Profile & Account Settings - JobFinder')

@section('content')
<div class="section-head">
    <div>
        <h1>Account Settings</h1>
        <p class="muted" style="margin:0">Update your personal information, company profile, and password security.</p>
    </div>
</div>

<div class="grid grid-2" style="align-items: start;">
    <section class="card" style="padding: 28px;">
        <h2 style="font-size: 20px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Personal & Company Profile</h2>
        <form class="form" method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <label>
                <span>Full Name <span style="color:#ef4444">*</span></span>
                <input name="name" value="{{ old('name', $user->name) }}" required>
            </label>

            <label>
                <span>Email Address <span style="color:#ef4444">*</span></span>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
            </label>

            <label>
                <span>Contact Phone</span>
                <input name="phone" value="{{ old('phone', $user->phone) }}" placeholder="0771234567">
            </label>

            @if($user->isEmployer())
                <label>
                    <span>Company Name</span>
                    <input name="company_name" value="{{ old('company_name', $user->company_name) }}">
                </label>

                <label>
                    <span>Website URL</span>
                    <input type="url" name="website" value="{{ old('website', $user->website) }}" placeholder="https://example.com">
                </label>
            @endif

            <label>
                <span>Address / Location</span>
                <textarea name="address" style="min-height: 80px;">{{ old('address', $user->address) }}</textarea>
            </label>

            <label>
                <span>Professional Bio / Description</span>
                <textarea name="bio" style="min-height: 100px;">{{ old('bio', $user->bio) }}</textarea>
            </label>

            <label>
                <span>Profile Photo / Company Logo</span>
                <input type="file" name="profile_picture" accept="image/*">
            </label>

            <button class="btn" style="margin-top: 8px;" type="submit">Save Changes</button>
        </form>
    </section>

    <section class="card" style="padding: 28px;">
        <h2 style="font-size: 20px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Security & Password</h2>
        <form class="form" method="post" action="{{ route('profile.password') }}">
            @csrf
            @method('PUT')

            <label>
                <span>Current Password <span style="color:#ef4444">*</span></span>
                <input type="password" name="current_password" placeholder="••••••••" required>
            </label>

            <label>
                <span>New Password <span style="color:#ef4444">*</span></span>
                <input type="password" name="password" placeholder="Min. 8 characters" required>
            </label>

            <label>
                <span>Confirm New Password <span style="color:#ef4444">*</span></span>
                <input type="password" name="password_confirmation" placeholder="Repeat new password" required>
            </label>

            <button class="btn secondary" style="margin-top: 8px;" type="submit">Update Password</button>
        </form>
    </section>
</div>
@endsection
