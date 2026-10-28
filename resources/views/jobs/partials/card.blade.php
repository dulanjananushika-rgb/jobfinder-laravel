<article class="job-card" style="{{ $job->is_featured ? 'border-color: #f59e0b; box-shadow: 0 4px 14px rgba(245, 158, 11, 0.12);' : '' }}">
    <div class="job-card-head">
        <div class="company-logo">
            {{ strtoupper(substr($job->employer->company_name ?: $job->employer->name, 0, 1)) }}
        </div>
        <div style="display:flex; align-items:center; gap:6px;">
            @if($job->is_featured)
                <span class="badge amber" style="background:#fffbeb; color:#b45309; border-color:#fde68a; font-weight:700;">★ Featured</span>
            @endif
            @auth
                @if(auth()->user()->isJobSeeker())
                    @php $isSaved = auth()->user()->savedJobs()->where('job_id', $job->id)->exists(); @endphp
                    <form method="post" action="{{ route('jobs.save', $job) }}">
                        @csrf
                        <button class="btn ghost" style="font-size:17px; color: {{ $isSaved ? '#ef4444' : '#94a3b8' }};" title="{{ $isSaved ? 'Remove bookmark' : 'Bookmark job' }}">
                            {{ $isSaved ? '♥' : '♡' }}
                        </button>
                    </form>
                @else
                    <span class="muted" style="font-size:17px">♡</span>
                @endif
            @else
                <a href="{{ route('login') }}" class="muted" style="font-size:17px" title="Sign in to bookmark">♡</a>
            @endauth
        </div>
    </div>

    <div>
        <h3 class="job-title">
            <a href="{{ route('jobs.show', $job) }}">{{ $job->title }}</a>
        </h3>
        <div class="job-meta-row">
            <a href="{{ route('companies.show', $job->employer) }}" style="font-weight:600; color:var(--text-heading);">
                {{ $job->employer->company_name ?: $job->employer->name }}
            </a>
            <span>&bull;</span>
            <span>📍 {{ $job->location }}</span>
            <span>&bull;</span>
            <span>👥 {{ $job->applications_count ?? $job->applications()->count() }} applied</span>
        </div>
    </div>

    <div class="actions">
        <span class="badge emerald">{{ $job->main_category }}</span>
        <span class="badge blue">{{ $job->type }}</span>
        @if($job->sub_category)
            <span class="badge gray">{{ $job->sub_category }}</span>
        @endif
    </div>

    <p class="job-desc-snippet">{{ \Illuminate\Support\Str::limit($job->description, 115) }}</p>

    <div class="job-card-footer">
        <div class="job-salary-tag">{{ $job->salary ?: 'Negotiable' }}</div>
        <div class="muted" style="font-size: 12px;">{{ $job->created_at->diffForHumans() }}</div>
    </div>
</article>
