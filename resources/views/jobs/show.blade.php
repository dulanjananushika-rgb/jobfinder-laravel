@extends('layouts.app')
@section('title', $job->title . ' - ' . ($job->employer->company_name ?: $job->employer->name))

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('jobs.index') }}" class="btn ghost btn-sm" style="padding-left:0;">← Back to All Vacancies</a>
</div>

<div class="grid grid-2" style="grid-template-columns: 1.8fr 1.2fr; align-items: start;">
    <section class="card" style="padding: 28px;">
        <div class="actions" style="margin-bottom: 14px;">
            <span class="badge emerald">{{ $job->main_category }}</span>
            <span class="badge blue">{{ $job->type }}</span>
            @if($job->sub_category)
                <span class="badge gray">{{ $job->sub_category }}</span>
            @endif
            @if($job->status === 'active')
                <span class="badge green">● Active Opening</span>
            @endif
        </div>

        <h1 style="font-size: 26px; margin-bottom: 6px;">{{ $job->title }}</h1>
        <div class="muted" style="display:flex; align-items:center; gap:8px; font-size:14px; margin-bottom:20px;">
            <strong>{{ $job->employer->company_name ?: $job->employer->name }}</strong>
            <span>&bull;</span>
            <span>📍 {{ $job->location }}</span>
            <span>&bull;</span>
            <span>Posted {{ $job->created_at->diffForHumans() }}</span>
        </div>

        <div class="grid grid-3" style="margin-bottom: 24px;">
            <div style="background:#f8fafc; border:1px solid var(--border-light); border-radius:var(--radius-sm); padding:14px;">
                <div class="muted" style="font-size:11.5px; font-weight:600; text-transform:uppercase;">Compensation</div>
                <div style="font-family:var(--font-heading); font-size:15px; font-weight:700; color:var(--primary); margin-top:3px;">
                    {{ $job->formatted_salary }}
                </div>
            </div>

            <div style="background:#f8fafc; border:1px solid var(--border-light); border-radius:var(--radius-sm); padding:14px;">
                <div class="muted" style="font-size:11.5px; font-weight:600; text-transform:uppercase;">Application Deadline</div>
                <div style="font-family:var(--font-heading); font-size:15px; font-weight:700; color:var(--text-heading); margin-top:3px;">
                    {{ optional($job->deadline)->format('M d, Y') ?: 'Until Filled' }}
                </div>
            </div>

            <div style="background:#f8fafc; border:1px solid var(--border-light); border-radius:var(--radius-sm); padding:14px;">
                <div class="muted" style="font-size:11.5px; font-weight:600; text-transform:uppercase;">Applications</div>
                <div style="font-family:var(--font-heading); font-size:15px; font-weight:700; color:var(--text-heading); margin-top:3px;">
                    {{ $job->applications()->count() }} Candidates
                </div>
            </div>
        </div>

        <h2 style="font-size:18px; border-bottom:1px solid var(--border-light); padding-bottom:8px; margin-bottom:12px;">Position Summary</h2>
        <div class="job-richtext" style="line-height:1.7; margin-bottom:24px; font-size:14px;">
            {!! \Illuminate\Support\Str::markdown($job->description, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
        </div>

        <h2 style="font-size:18px; border-bottom:1px solid var(--border-light); padding-bottom:8px; margin-bottom:12px;">Key Responsibilities</h2>
        <div class="job-richtext" style="line-height:1.7; margin-bottom:24px; font-size:14px;">
            {!! \Illuminate\Support\Str::markdown($job->responsibilities ?: 'Responsibilities will be discussed during initial interview.', ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
        </div>

        <h2 style="font-size:18px; border-bottom:1px solid var(--border-light); padding-bottom:8px; margin-bottom:12px;">Qualifications & Requirements</h2>
        <div class="job-richtext" style="line-height:1.7; font-size:14px;">
            {!! \Illuminate\Support\Str::markdown($job->requirements, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
        </div>
    </section>

    <aside class="grid" style="gap: 18px;">
        <!-- Application Actions Panel -->
        <section class="card" style="padding: 22px;">
            <h3 style="margin-bottom: 12px;">Candidate Application</h3>
            @auth
                @if(auth()->user()->isJobSeeker())
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        @if(! $job->isOpenForApplications())
                            <div class="badge amber" style="justify-content:center; padding:8px;">
                                ⚠️ Applications for this job are currently closed
                            </div>
                        @elseif($hasApplied)
                            <div class="badge emerald" style="justify-content:center; padding:10px; font-size:13.5px;">
                                ✓ You have applied for this vacancy
                            </div>
                            <a class="btn secondary btn-sm" href="{{ route('seeker.applications') }}">View Application Status</a>
                        @else
                            <a class="btn" style="width:100%" href="{{ route('applications.create', $job) }}">
                                Apply with Resume →
                            </a>
                        @endif

                        <form method="post" action="{{ route('jobs.save', $job) }}" style="width:100%">
                            @csrf
                            <button class="btn secondary btn-sm" style="width:100%" type="submit">
                                {{ $isSaved ? '♥ Remove from Saved' : '♡ Save Vacancy' }}
                            </button>
                        </form>
                    </div>
                @elseif(auth()->user()->isEmployer() && auth()->id() === $job->employer_id)
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <a class="btn" href="{{ route('employer.jobs.edit', $job) }}">Edit Vacancy</a>
                        <a class="btn secondary" href="{{ route('employer.applications.index') }}">View Candidate Resumes</a>
                    </div>
                @elseif(auth()->user()->isAdmin())
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <span class="badge blue">Admin Review Mode</span>
                        <a class="btn secondary" href="{{ route('admin.jobs') }}">Moderate in Admin Console</a>
                    </div>
                @endif
            @else
                <div style="display:flex; flex-direction:column; gap:10px;">
                    <a class="btn" style="width:100%" href="{{ route('login') }}">Sign In to Apply</a>
                    <a class="btn secondary" style="width:100%" href="{{ route('register') }}">Create Free Account</a>
                </div>
            @endauth
        </section>

        <!-- Company Details Card -->
        <section class="card" style="padding: 22px;">
            <h3 style="margin-bottom: 12px;">Hiring Organization</h3>
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:14px;">
                <div class="company-logo" style="width:44px; height:44px; font-size:18px;">
                    {{ strtoupper(substr($job->employer->company_name ?: $job->employer->name, 0, 1)) }}
                </div>
                <div>
                    <h4 style="font-size:15px; font-weight:700;">{{ $job->employer->company_name ?: $job->employer->name }}</h4>
                    @if($job->employer->isVerifiedEmployer())
                        <span class="badge green" style="font-size:11px; margin-top:2px;">✓ Verified Employer</span>
                    @endif
                </div>
            </div>

            <p class="muted" style="font-size:13px; margin-bottom:14px;">
                {!! nl2br(e($job->employer->bio ?: 'Verified employer registered on JobFinder Sri Lanka.')) !!}
            </p>

            @if($job->employer->website)
                <p style="margin-bottom:10px;">
                    <a class="btn secondary btn-sm" href="{{ $job->employer->website }}" target="_blank">Company Website ↗</a>
                </p>
            @endif

            <div style="font-size:12.5px; color:var(--text-muted); border-top:1px solid var(--border-light); padding-top:10px;">
                <div><strong>Inquiries:</strong> {{ $job->employer->email }}</div>
                @if($job->employer->phone)
                    <div style="margin-top:3px;"><strong>Phone:</strong> {{ $job->employer->phone }}</div>
                @endif
            </div>
        </section>
    </aside>
</div>
@endsection
