@extends('layouts.app')
@section('title', 'Admin Jobs')
@section('content')
<h1>Manage Jobs</h1>
<div class="card">
<table><thead><tr><th>Job</th><th>Employer</th><th>Status</th><th>Approval</th><th>Applications</th><th>Actions</th></tr></thead><tbody>
@foreach($jobs as $job)
<tr>
    <td><a href="{{ route('jobs.show', $job) }}">{{ $job->title }}</a></td>
    <td>{{ $job->employer->company_name ?: $job->employer->name }}<br>
        @if($job->employer->isVerifiedEmployer())<span class="badge green">Verified employer</span>@else<span class="badge peach">Unverified employer</span>@endif
    </td>
    <td>
        <form class="form" method="post" action="{{ route('admin.jobs.update', $job) }}">
            @csrf @method('PUT')
            <select name="status">
                @foreach(['active','inactive','pending','rejected'] as $status)
                    <option value="{{ $status }}" @selected($job->status === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <input name="rejection_reason" placeholder="Reason if rejected" value="{{ $job->rejection_reason }}">
            <button class="btn secondary">Save</button>
        </form>
    </td>
    <td>
        @if($job->approved_at)
            <span class="badge green">Approved</span><br><span class="muted">{{ $job->approved_at->format('M d, Y') }}</span>
        @elseif($job->status === 'rejected')
            <span class="badge peach">Rejected</span>
        @else
            <span class="badge">Needs review</span>
        @endif
    </td>
    <td>{{ $job->applications_count }}</td>
    <td><form method="post" action="{{ route('admin.jobs.delete', $job) }}">@csrf @method('DELETE') <button class="btn danger">Delete</button></form></td>
</tr>
@endforeach
</tbody></table>
</div>
{{ $jobs->links() }}
@endsection
