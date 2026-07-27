@extends('layouts.app')
@section('title', 'Applications')
@section('content')
<h1>Applications</h1>
<form class="card actions" method="get">
    <select name="status"><option value="">All Statuses</option>@foreach(['pending','reviewed','shortlisted','interview','hired','rejected','withdrawn'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select>
    <button class="btn">Filter</button>
</form><br>
<div class="card">
<table><thead><tr><th>Applicant</th><th>Job</th><th>Contact</th><th>Status</th><th>Resume</th><th>Cover Letter</th><th>Update</th></tr></thead><tbody>
@forelse($applications as $application)
<tr>
    <td>{{ $application->jobSeeker->name }}</td>
    <td>{{ $application->job->title }}</td>
    <td>{{ $application->jobSeeker->email }}<br><span class="muted">{{ $application->jobSeeker->phone ?: 'No phone' }}</span></td>
    <td><span class="badge">{{ $application->status }}</span></td>
    <td><a href="{{ asset('storage/'.$application->resume_path) }}" target="_blank">Download</a></td>
    <td>{{ \Illuminate\Support\Str::limit($application->cover_letter, 110) }}</td>
    <td>
        @if($application->status === 'withdrawn')
            <span class="muted">{{ $application->withdraw_reason ?: 'Withdrawn by candidate' }}</span>
        @else
            <form class="actions" method="post" action="{{ route('employer.applications.status', $application) }}">
                @csrf @method('PUT')
                <select name="status">@foreach(['pending','reviewed','shortlisted','interview','hired','rejected'] as $status)<option value="{{ $status }}" @selected($application->status === $status)>{{ ucfirst($status) }}</option>@endforeach</select>
                <button class="btn secondary">Save</button>
            </form>
        @endif
    </td>
</tr>
@empty <tr><td colspan="7">No applications found.</td></tr> @endforelse
</tbody></table>
</div>
{{ $applications->links() }}
@endsection
