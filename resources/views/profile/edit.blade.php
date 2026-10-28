@extends('layouts.app')
@section('title', 'Profile & CV Builder - JobFinder')

@section('content')
<div class="section-head">
    <div>
        <h1>Profile & Professional Settings</h1>
        <p class="muted" style="margin:0">Complete your professional credentials for 1-Click job applications and verified employer branding.</p>
    </div>
</div>

<div class="grid grid-2" style="align-items: start;">
    <section class="card" style="padding: 28px;">
        <h2 style="font-size: 18px; margin-bottom: 16px; border-bottom: 1px solid var(--border-light); padding-bottom: 10px;">
            {{ auth()->user()->isEmployer() ? '🏢 Company Profile & Showcase' : '📄 Candidate Profile & Digital CV' }}
        </h2>

        <form class="form" method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-2">
                <label>
                    <span>Full Name <span style="color:#ef4444">*</span></span>
                    <input name="name" value="{{ old('name', $user->name) }}" required>
                </label>

                <label>
                    <span>Email Address <span style="color:#ef4444">*</span></span>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                </label>
            </div>

            <label>
                <span>Contact Phone</span>
                <input name="phone" value="{{ old('phone', $user->phone) }}" placeholder="0771234567">
            </label>

            @if($user->isEmployer())
                <div class="grid grid-2">
                    <label>
                        <span>Company Legal Name</span>
                        <input name="company_name" value="{{ old('company_name', $user->company_name) }}">
                    </label>

                    <label>
                        <span>Organization Team Size</span>
                        <select name="company_size">
                            <option value="">Select size</option>
                            <option value="1-10" @selected(old('company_size', $user->company_size) === '1-10')>1 - 10 Employees</option>
                            <option value="11-50" @selected(old('company_size', $user->company_size) === '11-50')>11 - 50 Employees</option>
                            <option value="51-200" @selected(old('company_size', $user->company_size) === '51-200')>51 - 200 Employees</option>
                            <option value="200+" @selected(old('company_size', $user->company_size) === '200+')>200+ Enterprise</option>
                        </select>
                    </label>
                </div>

                <label>
                    <span>Company Culture & Perks (Comma-separated)</span>
                    <input name="company_perks" value="{{ old('company_perks', $user->company_perks) }}" placeholder="e.g. Remote Flexibility, Health Insurance, Annual Bonus, Tech Gear">
                </label>
            @endif

            @if($user->isJobSeeker())
                <label>
                    <span>Professional Headline</span>
                    <input name="headline" value="{{ old('headline', $user->headline) }}" placeholder="e.g. Senior Laravel & Cloud Solutions Architect">
                </label>

                <label>
                    <span>Key Skills & Proficiencies (Comma-separated)</span>
                    <input name="skills" value="{{ old('skills', $user->skills) }}" placeholder="e.g. Laravel, PHP, MySQL, REST APIs, Git, React, Docker">
                </label>

                <label>
                    <span>Work Experience & Career History</span>
                    <textarea name="experience" placeholder="Detail past roles, companies, achievements, and responsibilities..." style="min-height: 100px;">{{ old('experience', $user->experience) }}</textarea>
                </label>

                <label>
                    <span>Education, Degrees & Certifications</span>
                    <textarea name="education" placeholder="Universities, degrees, diplomas, online certifications..." style="min-height: 80px;">{{ old('education', $user->education) }}</textarea>
                </label>

                <div class="grid grid-2">
                    <label>
                        <span>GitHub URL</span>
                        <input type="url" name="github_url" value="{{ old('github_url', $user->github_url) }}" placeholder="https://github.com/username">
                    </label>
                    <label>
                        <span>LinkedIn Profile</span>
                        <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $user->linkedin_url) }}" placeholder="https://linkedin.com/in/username">
                    </label>
                </div>
            @endif

            <label>
                <span>Portfolio / Personal Website URL</span>
                <input type="url" name="portfolio_url" value="{{ old('portfolio_url', $user->portfolio_url) }}" placeholder="https://yourportfolio.com">
            </label>

            <label>
                <span>Physical Address / District</span>
                <input name="address" value="{{ old('address', $user->address) }}" placeholder="Colombo 03, Sri Lanka">
            </label>

            <label>
                <span>About / Executive Bio</span>
                <textarea name="bio" style="min-height: 90px;">{{ old('bio', $user->bio) }}</textarea>
            </label>

            <label>
                <span>Profile Photo / Organization Logo</span>
                <input type="file" name="profile_picture" accept="image/*">
            </label>

            <button class="btn" style="margin-top: 6px;" type="submit">Save Profile & CV Details →</button>
        </form>
    </section>

    <!-- Security & Password -->
    <section class="card" style="padding: 28px;">
        <h2 style="font-size: 18px; margin-bottom: 16px; border-bottom: 1px solid var(--border-light); padding-bottom: 10px;">Security Credentials</h2>
        <form class="form" method="post" action="{{ route('profile.password') }}">
            @csrf
            @method('PUT')

            <label>
                <span>Current Password <span style="color:#ef4444">*</span></span>
                <input type="password" name="current_password" placeholder="••••••••" required>
            </label>

            <label>
                <span>New Password <span style="color:#ef4444">*</span></span>
                <input type="password" name="password" placeholder="At least 8 characters" required>
            </label>

            <label>
                <span>Confirm New Password <span style="color:#ef4444">*</span></span>
                <input type="password" name="password_confirmation" placeholder="Repeat new password" required>
            </label>

            <button class="btn secondary" style="margin-top: 6px;" type="submit">Update Password</button>
        </form>
    </section>
</div>
@endsection
