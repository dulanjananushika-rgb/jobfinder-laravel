@extends('layouts.app')
@section('title', 'System Admin Console - JobFinder')

@section('content')
<div class="section-head">
    <div>
        <h1>System Administration</h1>
        <p class="muted" style="margin:0">Platform-wide overview, moderation controls, and user verifications.</p>
    </div>
</div>

<div class="grid grid-4" style="margin-bottom: 32px;">
    <a class="stat-card" href="{{ route('admin.users') }}">
        <div class="stat-icon-wrap" style="background:#eff6ff; color:#2563eb;">👥</div>
        <div>
            <div class="stat-value">{{ number_format($usersCount) }}</div>
            <div class="stat-label">Registered Users</div>
        </div>
    </a>

    <a class="stat-card" href="{{ route('admin.jobs') }}">
        <div class="stat-icon-wrap" style="background:#ecfdf5; color:#059669;">💼</div>
        <div>
            <div class="stat-value">{{ number_format($jobsCount) }}</div>
            <div class="stat-label">Platform Jobs</div>
        </div>
    </a>

    <a class="stat-card" href="{{ route('admin.applications') }}">
        <div class="stat-icon-wrap" style="background:#faf5ff; color:#7e22ce;">📄</div>
        <div>
            <div class="stat-value">{{ number_format($applicationsCount) }}</div>
            <div class="stat-label">Applications</div>
        </div>
    </a>

    <a class="stat-card" href="{{ route('admin.messages') }}">
        <div class="stat-icon-wrap" style="background:{{ $messagesCount > 0 ? '#fff1f2' : '#f8fafc' }}; color:{{ $messagesCount > 0 ? '#e11d48' : '#64748b' }};">
            ✉️
        </div>
        <div>
            <div class="stat-value">{{ number_format($messagesCount) }}</div>
            <div class="stat-label">New Inquiries</div>
        </div>
    </a>
</div>

<div class="grid grid-2">
    <section class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h2 style="font-size:18px;">Recent Registered Users</h2>
            <a class="btn ghost btn-sm" href="{{ route('admin.users') }}">Manage Users →</a>
        </div>
        <div style="display:flex; flex-direction:column; gap:10px;">
            @foreach($recentUsers as $user)
                <div style="padding:10px 14px; background:#f8fafc; border-radius:10px; border:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <a href="{{ route('admin.users.show', $user) }}" style="font-weight:700; color:var(--text-primary);">
                            {{ $user->name }}
                        </a>
                        <div class="muted" style="font-size:12px;">{{ $user->email }} &bull; {{ ucfirst(str_replace('_', ' ', $user->role)) }}</div>
                    </div>
                    <span class="badge {{ $user->status === 'active' ? 'green' : 'red' }}">{{ ucfirst($user->status) }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h2 style="font-size:18px;">Recent Job Postings</h2>
            <a class="btn ghost btn-sm" href="{{ route('admin.jobs') }}">Moderate Jobs →</a>
        </div>
        <div style="display:flex; flex-direction:column; gap:10px;">
            @foreach($recentJobs as $job)
                <div style="padding:10px 14px; background:#f8fafc; border-radius:10px; border:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <a href="{{ route('jobs.show', $job) }}" style="font-weight:700; color:var(--text-primary);">
                            {{ $job->title }}
                        </a>
                        <div class="muted" style="font-size:12px;">{{ $job->employer->company_name ?: $job->employer->name }}</div>
                    </div>
                    <span class="badge {{ $job->status === 'active' ? 'green' : ($job->status === 'pending' ? 'peach' : 'red') }}">
                        {{ ucfirst($job->status) }}
                    </span>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
