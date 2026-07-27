@extends('layouts.app')
@section('title', $job->title)
@section('content')
<div class="grid grid-2">
    <section class="card">
        <div class="actions"><span class="badge blue">{{ ucfirst($job->status) }}</span><span class="badge green">{{ $job->type }}</span><span class="badge peach">{{ $job->main_category }}</span></div>
        <h1>{{ $job->title }}</h1>
        <p class="muted">{{ $job->employer->company_name ?: $job->employer->name }} · {{ $job->location }}</p>
        <div class="grid grid-3">
            <div class="card"><strong>Salary</strong><p class="muted">{{ $job->salary ?: 'Not specified' }}</p></div>
            <div class="card"><strong>Deadline</strong><p class="muted">{{ optional($job->deadline)->format('M d, Y') ?: 'Open until filled' }}</p></div>
            <div class="card"><strong>Applications</strong><p class="muted">{{ $job->applications()->count() }}</p></div>
        </div>
        <h2>Description</h2><p>{!! nl2br(e($job->description)) !!}</p>
        <h2>Responsibilities</h2><p>{!! nl2br(e($job->responsibilities ?: 'Responsibilities will be discussed by the employer.')) !!}</p>
        <h2>Requirements</h2><p>{!! nl2br(e($job->requirements)) !!}</p>
    </section>
    <aside class="grid">
        <section class="card">
            <h2>Actions</h2>
            @auth
                @if(auth()->user()->isJobSeeker())
                    <div class="actions">
                        @if(! $job->isOpenForApplications())
                            <span class="badge peach">Not open for applications</span>
                        @elseif($hasApplied)
                            <span class="badge green">Already applied</span>
                        @else
                            <a class="btn" href="{{ route('applications.create', $job) }}">Apply Now</a>
                        @endif
                        <form method="post" action="{{ route('jobs.save', $job) }}">@csrf <button class="btn secondary">{{ $isSaved ? 'Unsave' : 'Save' }}</button></form>
                    </div>
                @elseif(auth()->user()->isEmployer() && auth()->id() === $job->employer_id)
                    <a class="btn" href="{{ route('employer.jobs.edit', $job) }}">Edit Job</a>
                @endif
            @else
                <a class="btn" href="{{ route('login') }}">Login to Apply</a>
            @endauth
        </section>
        <section class="card">
            <h2>Company</h2>
            <div class="actions"><div class="company-logo">{{ strtoupper(substr($job->employer->company_name ?: $job->employer->name,0,1)) }}</div><strong>{{ $job->employer->company_name ?: $job->employer->name }}</strong></div>
            <p class="muted">{!! nl2br(e($job->employer->bio ?: 'No company description provided.')) !!}</p>
            @if($job->employer->website)<p><a class="btn secondary" href="{{ $job->employer->website }}" target="_blank">Visit Website</a></p>@endif
            <p><strong>Contact:</strong> {{ $job->employer->email }} {{ $job->employer->phone ? '· '.$job->employer->phone : '' }}</p>
        </section>
    </aside>
</div>
@endsection
