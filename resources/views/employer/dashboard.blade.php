@extends('layouts.app')
@section('title', 'Employer Dashboard')
@section('content')
<h1>Employer Dashboard</h1>
@unless(auth()->user()->isVerifiedEmployer())
    <div class="errors">
        Your employer account is waiting for admin verification. You can edit your profile, but job posting is locked until verification is complete.
    </div>
@endunless
<div class="grid grid-3">
    <div class="card"><h2>{{ $activeJobs }}</h2><p class="muted">Active Jobs</p></div>
    <div class="card"><h2>{{ $applicationsCount }}</h2><p class="muted">Applications</p></div>
    <div class="card">
        @if(auth()->user()->isVerifiedEmployer())
            <a class="btn" href="{{ route('employer.jobs.create') }}">Post Job</a>
        @else
            <span class="badge peach">Verification pending</span>
        @endif
    </div>
</div>
<br><h2>Your Jobs</h2>
<div class="card">
    <table><thead><tr><th>Title</th><th>Status</th><th>Applications</th><th></th></tr></thead><tbody>
    @foreach($jobs as $job)
        <tr>
            <td>{{ $job->title }}</td>
            <td>
                <span class="badge">{{ ucfirst($job->status) }}</span>
                @if($job->approved_at)
                    <br><span class="muted">Approved {{ $job->approved_at->format('M d, Y') }}</span>
                @elseif($job->status === 'rejected' && $job->rejection_reason)
                    <br><span class="muted">{{ $job->rejection_reason }}</span>
                @endif
            </td>
            <td>{{ $job->applications_count }}</td>
            <td><a href="{{ route('employer.jobs.edit', $job) }}">Edit</a></td>
        </tr>
    @endforeach
    </tbody></table>
</div>
@endsection
