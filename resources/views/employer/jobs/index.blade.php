@extends('layouts.app')
@section('title', 'Manage Jobs')
@section('content')
<div class="actions">
    <h1 style="margin-right:auto">Manage Jobs</h1>
    @if(auth()->user()->isVerifiedEmployer())
        <a class="btn" href="{{ route('employer.jobs.create') }}">Post Job</a>
    @else
        <span class="badge peach">Employer verification pending</span>
    @endif
</div>
<div class="card">
<table><thead><tr><th>Title</th><th>Status</th><th>Approval</th><th>Deadline</th><th>Applications</th><th>Actions</th></tr></thead><tbody>
@forelse($jobs as $job)
<tr>
    <td><a href="{{ route('jobs.show', $job) }}">{{ $job->title }}</a></td>
    <td><span class="badge">{{ ucfirst($job->status) }}</span></td>
    <td>
        @if($job->approved_at)
            <span class="badge green">Approved</span>
        @elseif($job->status === 'rejected')
            <span class="badge peach">Rejected</span>
            @if($job->rejection_reason)<br><span class="muted">{{ $job->rejection_reason }}</span>@endif
        @else
            <span class="badge">Admin review</span>
        @endif
    </td>
    <td>{{ optional($job->deadline)->format('M d, Y') ?: '-' }}</td>
    <td>{{ $job->applications_count }}</td>
    <td class="actions"><a href="{{ route('employer.jobs.edit', $job) }}">Edit</a><form method="post" action="{{ route('employer.jobs.destroy', $job) }}">@csrf @method('DELETE') <button class="btn danger">Delete</button></form></td>
</tr>
@empty <tr><td colspan="6">No jobs posted.</td></tr> @endforelse
</tbody></table>
</div>
{{ $jobs->links() }}
@endsection
