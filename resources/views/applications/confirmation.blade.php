@extends('layouts.app')
@section('title', 'Application Submitted')
@section('content')
<div class="card" style="max-width:760px;margin:auto;text-align:center">
    <span class="badge green">Application submitted</span>
    <h1>Thanks for applying</h1>
    <p>You applied for <strong>{{ $application->job->title }}</strong> at <strong>{{ $application->job->employer->company_name ?: $application->job->employer->name }}</strong>.</p>
    <div class="grid grid-3">
        <div class="card"><strong>Submitted</strong><p class="muted">{{ $application->created_at->format('M d, Y') }}</p></div>
        <div class="card"><strong>Status</strong><p class="muted">{{ ucfirst($application->status) }}</p></div>
        <div class="card"><strong>Location</strong><p class="muted">{{ $application->job->location }}</p></div>
    </div>
    <br>
    <div class="actions" style="justify-content:center">
        <a class="btn" href="{{ route('seeker.applications') }}">My Applications</a>
        <a class="btn secondary" href="{{ route('jobs.show', $application->job) }}">View Job</a>
        <a class="btn secondary" href="{{ route('jobs.index') }}">Browse More Jobs</a>
    </div>
</div>
@endsection
