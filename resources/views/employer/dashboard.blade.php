@extends('layouts.app')
@section('title', 'Employer Analytics & Dashboard - JobFinder')

@section('content')
<div class="section-head">
    <div>
        <h1>Employer Console & Analytics</h1>
        <p class="muted" style="margin:0">Track your recruitment funnel, views traction, upcoming interviews, and candidate pipeline.</p>
    </div>
    @if(auth()->user()->isVerifiedEmployer())
        <a class="btn btn-sm" href="{{ route('employer.jobs.create') }}">+ Post New Vacancy</a>
    @endif
</div>

@unless(auth()->user()->isVerifiedEmployer())
    <div class="alert" style="background:#fffbeb; color:#b45309; border-color:#fde68a;">
        <span>⏳</span>
        <span>
            <strong>Verification In Progress:</strong> Your employer organization profile is currently being reviewed by the JobFinder admin team.
        </span>
    </div>
@endunless

<!-- Metric Tiles -->
<div class="grid grid-4" style="margin-bottom: 28px;">
    <div class="stat-card">
        <div class="stat-icon-wrap">💼</div>
        <div>
            <div class="stat-value">{{ $activeJobs }}</div>
            <div class="stat-label">Active Vacancies</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrap" style="background:#eff6ff; color:#2563eb;">👁️</div>
        <div>
            <div class="stat-value">{{ number_format($totalViews) }}</div>
            <div class="stat-label">Total Job Views</div>
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
        <div class="stat-icon-wrap" style="background:#fffbeb; color:#b45309;">📅</div>
        <div>
            <div class="stat-value">{{ $upcomingInterviews->count() }}</div>
            <div class="stat-label">Upcoming Interviews</div>
        </div>
    </div>
</div>

<div class="grid grid-2" style="margin-bottom: 32px; align-items: start;">
    <!-- Pipeline Status Breakdown -->
    <div class="card" style="padding: 24px;">
        <h3 style="margin-bottom: 16px;">Recruitment Funnel Breakdown</h3>
        <div style="display:flex; flex-direction:column; gap:10px;">
            @php
                $stages = [
                    'pending' => ['label' => 'New / Pending Review', 'color' => '#f59e0b', 'bg' => '#fffbeb'],
                    'reviewed' => ['label' => 'Under Review', 'color' => '#3b82f6', 'bg' => '#eff6ff'],
                    'shortlisted' => ['label' => 'Shortlisted Candidates', 'color' => '#8b5cf6', 'bg' => '#faf5ff'],
                    'interview' => ['label' => 'Interview Scheduled', 'color' => '#d97706', 'bg' => '#fef3c7'],
                    'hired' => ['label' => 'Successfully Hired 🎉', 'color' => '#059669', 'bg' => '#ecfdf5'],
                    'rejected' => ['label' => 'Declined / Not Selected', 'color' => '#ef4444', 'bg' => '#fff1f2'],
                ];
            @endphp
            @foreach($stages as $key => $meta)
                @php 
                    $count = $statusBreakdown[$key] ?? 0;
                    $percent = $applicationsCount > 0 ? round(($count / $applicationsCount) * 100) : 0;
                @endphp
                <div>
                    <div style="display:flex; justify-content:space-between; font-size:13px; font-weight:600; margin-bottom:4px;">
                        <span>{{ $meta['label'] }}</span>
                        <span>{{ $count }} ({{ $percent }}%)</span>
                    </div>
                    <div style="height:8px; background:#f1f5f9; border-radius:999px; overflow:hidden;">
                        <div style="width:{{ $percent }}%; height:100%; background:{{ $meta['color'] }}; border-radius:999px;"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Scheduled Interviews -->
    <div class="card" style="padding: 24px;">
        <h3 style="margin-bottom: 16px;">Upcoming Candidate Interviews</h3>
        <div style="display:flex; flex-direction:column; gap:12px;">
            @forelse($upcomingInterviews as $interview)
                <div style="padding:12px; background:#f8fafc; border:1px solid var(--border-light); border-radius:var(--radius-sm); display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <div style="font-weight:700; color:var(--text-heading); font-size:14px;">
                            {{ $interview->candidate->name }}
                        </div>
                        <div class="muted" style="font-size:12px;">
                            Role: {{ $interview->application->job->title }} &bull; {{ $interview->scheduled_at->format('M d, Y @ h:i A') }}
                        </div>
                    </div>
                    <a class="btn secondary btn-sm" href="{{ route('applications.messages', $interview->application) }}">
                        Open Thread →
                    </a>
                </div>
            @empty
                <div style="text-align:center; padding:32px 0;" class="muted">
                    No interviews scheduled on your calendar.
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Job Listings Table -->
<div class="section-head">
    <div>
        <h2>Active Job Postings</h2>
        <p class="muted" style="margin:0">Overview of recent postings and candidate traction.</p>
    </div>
    <a class="btn secondary btn-sm" href="{{ route('employer.jobs.index') }}">Manage All Vacancies →</a>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Status</th>
                    <th>Views</th>
                    <th>Applications</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($jobs as $job)
                <tr>
                    <td>
                        <a href="{{ route('jobs.show', $job) }}" style="font-weight:700; color:var(--text-heading);">
                            {{ $job->title }}
                        </a>
                        <div class="muted" style="font-size:12px;">📍 {{ $job->location }} &bull; {{ $job->type }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $job->status === 'active' ? 'green' : 'peach' }}">{{ ucfirst($job->status) }}</span>
                        @if($job->is_featured)
                            <span class="badge amber" style="margin-left:4px;">★ Featured</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-weight:700;">{{ number_format($job->views_count) }}</span>
                    </td>
                    <td>
                        <span style="font-weight:700; color:var(--primary);">{{ $job->applications_count }}</span>
                        <span class="muted" style="font-size:12px;"> candidates</span>
                    </td>
                    <td>
                        <div class="actions">
                            <a class="btn secondary btn-sm" href="{{ route('employer.jobs.edit', $job) }}">Edit</a>
                            <a class="btn ghost btn-sm" href="{{ route('employer.applications.index') }}">Review</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 36px;" class="muted">No jobs posted yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
