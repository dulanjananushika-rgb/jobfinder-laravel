@extends('layouts.app')
@section('title', 'Candidate Dashboard - JobFinder')

@section('content')
<div class="section-head">
    <div>
        <h1>Welcome, {{ auth()->user()->name }} 👋</h1>
        <p class="muted" style="margin:0">Manage your active job applications, saved listings, and profile visibility.</p>
    </div>
    <a class="btn btn-sm" href="{{ route('jobs.index') }}">Explore Vacancies →</a>
</div>

<div class="grid grid-2" style="margin-bottom: 32px;">
    <section class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h2 style="font-size:18px;">Recent Applications</h2>
            <a class="btn ghost btn-sm" href="{{ route('seeker.applications') }}">View All →</a>
        </div>

        <div style="display:flex; flex-direction:column; gap:12px;">
            @forelse($applications as $application)
                @php
                    $badgeClass = match($application->status) {
                        'hired' => 'green',
                        'interview' => 'peach',
                        'shortlisted' => 'purple',
                        'reviewed' => 'blue',
                        'rejected', 'withdrawn' => 'red',
                        default => 'peach',
                    };
                @endphp
                <div style="padding:12px 14px; background:#f8fafc; border-radius:10px; border:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <a href="{{ route('jobs.show', $application->job) }}" style="font-weight:700; color:var(--text-primary);">
                            {{ $application->job->title }}
                        </a>
                        <div class="muted" style="font-size:12px;">{{ $application->job->employer->company_name ?: $application->job->employer->name }}</div>
                    </div>
                    <span class="badge {{ $badgeClass }}">{{ ucfirst($application->status) }}</span>
                </div>
            @empty
                <p class="muted" style="padding:20px 0; text-align:center;">No job applications submitted yet.</p>
            @endforelse
        </div>
    </section>

    <section class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h2 style="font-size:18px;">Bookmarked Jobs</h2>
            <a class="btn ghost btn-sm" href="{{ route('seeker.saved-jobs') }}">View All Saved →</a>
        </div>

        <div style="display:flex; flex-direction:column; gap:12px;">
            @forelse($savedJobs as $job)
                <div style="padding:12px 14px; background:#f8fafc; border-radius:10px; border:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <a href="{{ route('jobs.show', $job) }}" style="font-weight:700; color:var(--text-primary);">
                            {{ $job->title }}
                        </a>
                        <div class="muted" style="font-size:12px;">{{ $job->employer->company_name ?: $job->employer->name }} · {{ $job->location }}</div>
                    </div>
                    <a class="btn secondary btn-sm" href="{{ route('jobs.show', $job) }}">Apply</a>
                </div>
            @empty
                <p class="muted" style="padding:20px 0; text-align:center;">You haven't bookmarked any jobs yet.</p>
            @endforelse
        </div>
    </section>
</div>

<div class="section-head">
    <div>
        <h2>Recommended For You</h2>
        <p class="muted" style="margin:0">Active openings matching high-demand industries in Sri Lanka.</p>
    </div>
</div>

<div class="grid grid-3">
    @foreach($recommendedJobs as $job)
        @include('jobs.partials.card', ['job' => $job])
    @endforeach
</div>
@endsection
