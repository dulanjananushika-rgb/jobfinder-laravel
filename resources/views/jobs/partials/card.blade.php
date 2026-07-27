<article class="card job-card">
    <div class="job-card-head">
        <div class="company-logo">{{ strtoupper(substr($job->employer->company_name ?: $job->employer->name, 0, 1)) }}</div>
        @auth
            @if(auth()->user()->isJobSeeker())
                <form method="post" action="{{ route('jobs.save', $job) }}">@csrf <button class="btn ghost" title="Save job">♡</button></form>
            @else
                <span class="muted">♡</span>
            @endif
        @else
            <span class="muted">♡</span>
        @endauth
    </div>
    <div>
        <h3><a href="{{ route('jobs.show', $job) }}">{{ $job->title }}</a></h3>
        <p class="muted" style="margin:0">{{ $job->employer->company_name ?: $job->employer->name }} · {{ $job->location }} · {{ $job->applications_count ?? $job->applications()->count() }} applications</p>
    </div>
    <div class="actions">
        <span class="badge">{{ $job->main_category }}</span>
        <span class="badge green">{{ $job->type }}</span>
        @if($job->sub_category)<span class="badge peach">{{ $job->sub_category }}</span>@endif
    </div>
    <p>{{ \Illuminate\Support\Str::limit($job->description, 105) }}</p>
    <div class="actions price">
        <span>{{ $job->salary ?: 'Negotiable' }}</span>
        <span class="muted">Posted {{ $job->created_at->diffForHumans() }}</span>
    </div>
</article>
