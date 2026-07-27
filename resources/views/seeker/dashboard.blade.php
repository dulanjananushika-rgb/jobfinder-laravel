@extends('layouts.app')
@section('title', 'Job Seeker Dashboard')
@section('content')
<h1>Welcome, {{ auth()->user()->name }}</h1>
<div class="grid grid-2">
    <section class="card">
        <h2>Recent Applications</h2>
        @forelse($applications as $application)
            <p><strong>{{ $application->job->title }}</strong><br><span class="badge">{{ $application->status }}</span></p>
        @empty <p class="muted">No applications yet.</p> @endforelse
        <a class="btn secondary" href="{{ route('seeker.applications') }}">View All</a>
    </section>
    <section class="card">
        <h2>Saved Jobs</h2>
        @forelse($savedJobs as $job)
            <p><a href="{{ route('jobs.show', $job) }}">{{ $job->title }}</a><br><span class="muted">{{ $job->employer->company_name ?: $job->employer->name }}</span></p>
        @empty <p class="muted">No saved jobs yet.</p> @endforelse
        <a class="btn secondary" href="{{ route('seeker.saved-jobs') }}">View Saved Jobs</a>
    </section>
</div>
<br><h2>Recommended Jobs</h2>
<div class="grid grid-3">@foreach($recommendedJobs as $job) @include('jobs.partials.card', ['job' => $job]) @endforeach</div>
@endsection
