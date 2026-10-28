@extends('layouts.app')
@section('title', 'Application Submitted Successfully')

@section('content')
<div style="max-width: 720px; margin: 40px auto;">
    <div class="card" style="padding: 48px 36px; text-align: center;">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: #ecfdf5; border: 2px solid #a7f3d0; color: #059669; font-size: 32px; display: grid; place-items: center; margin: 0 auto 20px;">
            ✓
        </div>

        <div class="badge green" style="margin-bottom: 12px;">Application Successfully Transmitted</div>
        <h1 style="font-size: 28px; margin-bottom: 8px;">Thanks for Applying!</h1>
        <p class="muted" style="font-size: 15px; max-width: 520px; margin: 0 auto 32px;">
            Your application for <strong>{{ $application->job->title }}</strong> has been forwarded directly to <strong>{{ $application->job->employer->company_name ?: $application->job->employer->name }}</strong>.
        </p>

        <div class="grid grid-3" style="margin-bottom: 36px;">
            <div style="background:#f8fafc; border:1px solid var(--border-color); border-radius:12px; padding:16px;">
                <div class="muted" style="font-size:12px; font-weight:600; text-transform:uppercase;">Application ID</div>
                <div style="font-family:var(--font-heading); font-size:16px; font-weight:700; margin-top:4px;">#APP-{{ $application->id }}</div>
            </div>

            <div style="background:#f8fafc; border:1px solid var(--border-color); border-radius:12px; padding:16px;">
                <div class="muted" style="font-size:12px; font-weight:600; text-transform:uppercase;">Current Stage</div>
                <div style="font-family:var(--font-heading); font-size:16px; font-weight:700; color:#b45309; margin-top:4px;">{{ ucfirst($application->status) }}</div>
            </div>

            <div style="background:#f8fafc; border:1px solid var(--border-color); border-radius:12px; padding:16px;">
                <div class="muted" style="font-size:12px; font-weight:600; text-transform:uppercase;">Location</div>
                <div style="font-family:var(--font-heading); font-size:16px; font-weight:700; margin-top:4px;">{{ $application->job->location }}</div>
            </div>
        </div>

        <div class="actions" style="justify-content: center; gap: 14px;">
            <a class="btn" href="{{ route('seeker.applications') }}">Track My Applications →</a>
            <a class="btn secondary" href="{{ route('applications.resume', $application) }}" target="_blank">📄 View Submitted Resume</a>
            <a class="btn secondary" href="{{ route('jobs.index') }}">Explore More Jobs</a>
        </div>
    </div>
</div>
@endsection
