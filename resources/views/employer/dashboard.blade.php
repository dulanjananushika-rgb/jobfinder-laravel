@extends('layouts.app')
@section('title', 'Employer Dashboard - JobFinder')

@section('content')
<div class="section-head">
    <div>
        <h1>Employer Console 👋</h1>
        <p class="muted" style="margin:0">Manage company openings, monitor applicants, and track review status.</p>
    </div>
    @if(auth()->user()->isVerifiedEmployer())
        <a class="btn btn-sm" href="{{ route('employer.jobs.create') }}">+ Post New Job</a>
    @endif
</div>

@unless(auth()->user()->isVerifiedEmployer())
    <div class="alert" style="background:#fffbeb; color:#b45309; border-color:#fde68a;">
        <span>⏳</span>
        <span>
            <strong>Verification In Progress:</strong> Your employer profile is currently under review by the JobFinder admin team. Job posting will be enabled as soon as verification completes.
        </span>
    </div>
@endunless

<div class="grid grid-3" style="margin-bottom: 32px;">
    <div class="stat-card">
        <div class="stat-icon-wrap">💼</div>
        <div>
            <div class="stat-value">{{ $activeJobs }}</div>
            <div class="stat-label">Live Active Jobs</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrap" style="background:#faf5ff; color:#7e22ce;">👥</div>
        <div>
            <div class="stat-value">{{ $applicationsCount }}</div>
            <div class="stat-label">Candidate Applications</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrap" style="background:{{ auth()->user()->isVerifiedEmployer() ? '#ecfdf5' : '#fffbeb' }}; color:{{ auth()->user()->isVerifiedEmployer() ? '#059669' : '#b45309' }};">
            {{ auth()->user()->isVerifiedEmployer() ? '✓' : '⏳' }}
        </div>
        <div>
            <div class="stat-value" style="font-size:18px;">
                {{ auth()->user()->isVerifiedEmployer() ? 'Verified Employer' : 'Pending Verification' }}
            </div>
            <div class="stat-label">Trust Status</div>
        </div>
    </div>
</div>

<div class="section-head">
    <div>
        <h2>Your Job Listings</h2>
        <p class="muted" style="margin:0">Overview of recent postings and candidate traction.</p>
    </div>
    <div class="actions">
        <a class="btn secondary btn-sm" href="{{ route('employer.applications.index') }}">Review Received Applications</a>
        <a class="btn secondary btn-sm" href="{{ route('employer.jobs.index') }}">All Job Postings →</a>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Approval Status</th>
                    <th>Received Applications</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($jobs as $job)
                @php
                    $badgeClass = match($job->status) {
                        'active' => 'green',
                        'pending' => 'peach',
                        'rejected' => 'red',
                        default => 'blue',
                    };
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('jobs.show', $job) }}" style="font-weight:700; color:var(--text-primary);">
                            {{ $job->title }}
                        </a>
                        <div class="muted" style="font-size:12px;">📍 {{ $job->location }} &bull; {{ $job->type }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($job->status) }}</span>
                        @if($job->approved_at)
                            <div class="muted" style="font-size:11.5px; margin-top:2px;">Approved {{ $job->approved_at->format('M d, Y') }}</div>
                        @elseif($job->status === 'rejected' && $job->rejection_reason)
                            <div class="muted" style="font-size:11.5px; color:#ef4444; margin-top:2px;">{{ $job->rejection_reason }}</div>
                        @endif
                    </td>
                    <td>
                        <span style="font-weight:700; font-family:var(--font-heading);">{{ $job->applications_count }}</span>
                        <span class="muted" style="font-size:12.5px;"> candidates</span>
                    </td>
                    <td>
                        <div class="actions">
                            <a class="btn secondary btn-sm" href="{{ route('employer.jobs.edit', $job) }}">Edit</a>
                            <a class="btn ghost btn-sm" href="{{ route('employer.applications.index', ['status' => 'pending']) }}">Review</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 48px;">
                        <p class="muted">You haven't posted any jobs yet.</p>
                        @if(auth()->user()->isVerifiedEmployer())
                            <a class="btn btn-sm" style="margin-top:12px;" href="{{ route('employer.jobs.create') }}">Post Your First Job</a>
                        @endif
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
