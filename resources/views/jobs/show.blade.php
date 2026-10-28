@extends('layouts.app')
@section('title', $job->title . ' - ' . ($job->employer->company_name ?: $job->employer->name))

@section('content')
<div style="margin-bottom: 24px;">
    <a href="{{ route('jobs.index') }}" class="btn ghost btn-sm" style="padding-left:0;">← Back to All Jobs</a>
</div>

<div class="grid grid-2" style="grid-template-columns: 1.8fr 1.2fr; align-items: start;">
    <section class="card" style="padding: 32px;">
        <div class="actions" style="margin-bottom: 16px;">
            <span class="badge blue">{{ $job->main_category }}</span>
            <span class="badge green">{{ $job->type }}</span>
            @if($job->sub_category)
                <span class="badge peach">{{ $job->sub_category }}</span>
            @endif
            @if($job->status === 'active')
                <span class="badge active">● Live Vacancy</span>
            @endif
        </div>

        <h1 style="font-size: 32px; margin-bottom: 8px;">{{ $job->title }}</h1>
        <div class="muted" style="display:flex; align-items:center; gap:8px; font-size:15px; margin-bottom:24px;">
            <strong>{{ $job->employer->company_name ?: $job->employer->name }}</strong>
            <span>&bull;</span>
            <span>📍 {{ $job->location }}</span>
            <span>&bull;</span>
            <span>Posted {{ $job->created_at->diffForHumans() }}</span>
        </div>

        <div class="grid grid-3" style="margin-bottom: 28px;">
            <div style="background:#f8fafc; border:1px solid var(--border-color); border-radius:12px; padding:16px;">
                <div class="muted" style="font-size:12px; font-weight:600; text-transform:uppercase;">Compensation</div>
                <div style="font-family:var(--font-heading); font-size:16px; font-weight:800; color:var(--text-primary); margin-top:4px;">
                    {{ $job->salary ?: 'Negotiable' }}
                </div>
            </div>

            <div style="background:#f8fafc; border:1px solid var(--border-color); border-radius:12px; padding:16px;">
                <div class="muted" style="font-size:12px; font-weight:600; text-transform:uppercase;">Deadline</div>
                <div style="font-family:var(--font-heading); font-size:16px; font-weight:800; color:var(--text-primary); margin-top:4px;">
                    {{ optional($job->deadline)->format('M d, Y') ?: 'Until Filled' }}
                </div>
            </div>

            <div style="background:#f8fafc; border:1px solid var(--border-color); border-radius:12px; padding:16px;">
                <div class="muted" style="font-size:12px; font-weight:600; text-transform:uppercase;">Applicants</div>
                <div style="font-family:var(--font-heading); font-size:16px; font-weight:800; color:var(--text-primary); margin-top:4px;">
                    {{ $job->applications()->count() }} Candidates
                </div>
            </div>
        </div>

        <h2 style="font-size:20px; border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:14px;">Role Description</h2>
        <div style="line-height:1.7; margin-bottom:28px;">
            {!! nl2br(e($job->description)) !!}
        </div>

        <h2 style="font-size:20px; border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:14px;">Key Responsibilities</h2>
        <div style="line-height:1.7; margin-bottom:28px;">
            {!! nl2br(e($job->responsibilities ?: 'Key responsibilities will be briefed during initial interview screening.')) !!}
        </div>

        <h2 style="font-size:20px; border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:14px;">Candidate Requirements</h2>
        <div style="line-height:1.7;">
            {!! nl2br(e($job->requirements)) !!}
        </div>
    </section>

    <aside class="grid" style="gap: 20px;">
        <!-- Application Actions Panel -->
        <section class="card" style="padding: 24px;">
            <h3 style="margin-bottom: 14px;">Apply for this Position</h3>
            @auth
                @if(auth()->user()->isJobSeeker())
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        @if(! $job->isOpenForApplications())
                            <div class="badge peach" style="justify-content:center; padding:10px;">
                                ⚠️ Applications for this job are currently closed
                            </div>
                        @elseif($hasApplied)
                            <div class="badge green" style="justify-content:center; padding:12px; font-size:14px;">
                                ✓ You have applied for this job
                            </div>
                            <a class="btn secondary" href="{{ route('seeker.applications') }}">View Application Status</a>
                        @else
                            <a class="btn" style="width:100%" href="{{ route('applications.create', $job) }}">
                                🚀 Apply Now with Resume
                            </a>
                        @endif

                        <form method="post" action="{{ route('jobs.save', $job) }}" style="width:100%">
                            @csrf
                            <button class="btn secondary" style="width:100%" type="submit">
                                {{ $isSaved ? '♥ Remove from Saved' : '♡ Save for Later' }}
                            </button>
                        </form>
                    </div>
                @elseif(auth()->user()->isEmployer() && auth()->id() === $job->employer_id)
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        <a class="btn" href="{{ route('employer.jobs.edit', $job) }}">✏️ Edit Job Listing</a>
                        <a class="btn secondary" href="{{ route('employer.applications.index') }}">👥 View Received Resumes</a>
                    </div>
                @elseif(auth()->user()->isAdmin())
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        <span class="badge blue">Admin Preview Mode</span>
                        <a class="btn secondary" href="{{ route('admin.jobs') }}">Moderate in Admin Panel</a>
                    </div>
                @endif
            @else
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <a class="btn" style="width:100%" href="{{ route('login') }}">Sign In to Apply</a>
                    <a class="btn secondary" style="width:100%" href="{{ route('register') }}">Create Free Account</a>
                </div>
            @endauth
        </section>

        <!-- Company Card -->
        <section class="card" style="padding: 24px;">
            <h3 style="margin-bottom: 14px;">About the Employer</h3>
            <div style="display:flex; align-items:center; gap:14px; margin-bottom:16px;">
                <div class="company-logo" style="width:50px; height:50px; font-size:20px;">
                    {{ strtoupper(substr($job->employer->company_name ?: $job->employer->name, 0, 1)) }}
                </div>
                <div>
                    <h4 style="font-size:16px; font-weight:700;">{{ $job->employer->company_name ?: $job->employer->name }}</h4>
                    @if($job->employer->isVerifiedEmployer())
                        <span class="badge green" style="font-size:11px; margin-top:2px;">✓ Verified Employer</span>
                    @endif
                </div>
            </div>

            <p class="muted" style="font-size:13.5px; margin-bottom:16px;">
                {!! nl2br(e($job->employer->bio ?: 'Verified employer registered with JobFinder Sri Lanka.')) !!}
            </p>

            @if($job->employer->website)
                <p style="margin-bottom:12px;">
                    <a class="btn secondary btn-sm" href="{{ $job->employer->website }}" target="_blank">🌐 Official Website</a>
                </p>
            @endif

            <div style="font-size:13px; color:var(--text-muted); border-top:1px solid var(--border-color); padding-top:12px;">
                <div><strong>Inquiries:</strong> {{ $job->employer->email }}</div>
                @if($job->employer->phone)
                    <div style="margin-top:4px;"><strong>Contact:</strong> {{ $job->employer->phone }}</div>
                @endif
            </div>
        </section>
    </aside>
</div>
@endsection
