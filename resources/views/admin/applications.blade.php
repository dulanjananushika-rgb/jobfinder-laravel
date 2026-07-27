@extends('layouts.app')
@section('title', 'Admin Applications')
@section('content')
<h1>All Applications</h1>
<div class="card">
<table><thead><tr><th>Applicant</th><th>Job</th><th>Employer</th><th>Status</th><th>Resume</th><th>Cover Letter</th></tr></thead><tbody>
@forelse($applications as $application)
<tr>
    <td><a href="{{ route('admin.users.show', $application->jobSeeker) }}">{{ $application->jobSeeker->name }}</a><br><span class="muted">{{ $application->jobSeeker->email }}</span></td>
    <td><a href="{{ route('jobs.show', $application->job) }}">{{ $application->job->title }}</a></td>
    <td>{{ $application->job->employer->company_name ?: $application->job->employer->name }}</td>
    <td><span class="badge">{{ $application->status }}</span><br><span class="muted">{{ $application->created_at->format('M d, Y') }}</span></td>
    <td><a href="{{ asset('storage/'.$application->resume_path) }}" target="_blank">Download</a></td>
    <td>{{ \Illuminate\Support\Str::limit($application->cover_letter, 120) }}</td>
</tr>
@empty <tr><td colspan="6">No applications found.</td></tr> @endforelse
</tbody></table>
</div>
{{ $applications->links() }}
@endsection
