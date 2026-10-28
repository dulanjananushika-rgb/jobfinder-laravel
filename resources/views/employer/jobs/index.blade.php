@extends('layouts.app')
@section('title', 'Manage Job Listings - Employer Portal')

@section('content')
<div class="section-head">
    <div>
        <h1>Manage Vacancies</h1>
        <p class="muted" style="margin:0">Track your published roles, admin approvals, and candidate response.</p>
    </div>
    @if(auth()->user()->isVerifiedEmployer())
        <a class="btn btn-sm" href="{{ route('employer.jobs.create') }}">+ Post New Job</a>
    @else
        <span class="badge peach">Verification Pending</span>
    @endif
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Status</th>
                    <th>Approval State</th>
                    <th>Application Deadline</th>
                    <th>Candidates</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($jobs as $job)
                @php
                    $badgeClass = match($job->status) {
                        'active' => 'green',
                        'pending' => 'peach',
                        'rejected' => 'red',
                        default => 'blue',
                    };
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('jobs.show', $job) }}" style="font-weight:700; color:var(--text-primary);">
                            {{ $job->title }}
                        </a>
                        <div class="muted" style="font-size:12px;">📍 {{ $job->location }} &bull; {{ $job->type }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($job->status) }}</span>
                    </td>
                    <td>
                        @if($job->approved_at)
                            <span class="badge green">✓ Approved</span>
                            <div class="muted" style="font-size:11px; margin-top:2px;">{{ $job->approved_at->format('M d, Y') }}</div>
                        @elseif($job->status === 'rejected')
                            <span class="badge red">✕ Rejected</span>
                            @if($job->rejection_reason)
                                <div class="muted" style="font-size:11px; color:#ef4444; margin-top:2px;">{{ $job->rejection_reason }}</div>
                            @endif
                        @else
                            <span class="badge peach">⏳ Under Review</span>
                        @endif
                    </td>
                    <td>
                        {{ optional($job->deadline)->format('M d, Y') ?: 'Open until filled' }}
                    </td>
                    <td>
                        <a href="{{ route('employer.applications.index') }}" style="font-weight:700; color:var(--brand-primary);">
                            {{ $job->applications_count }}
                        </a>
                        <span class="muted" style="font-size:12px;"> applicants</span>
                    </td>
                    <td>
                        <div class="actions">
                            <a class="btn secondary btn-sm" href="{{ route('employer.jobs.edit', $job) }}">Edit</a>
                            <form method="post" action="{{ route('employer.jobs.destroy', $job) }}" onsubmit="return confirm('Delete this job permanently?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn danger btn-sm" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 48px;">
                        <p class="muted">You haven't posted any jobs yet.</p>
                        @if(auth()->user()->isVerifiedEmployer())
                            <a class="btn btn-sm" style="margin-top:12px;" href="{{ route('employer.jobs.create') }}">Post Your First Job</a>
                        @endif
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 24px;">
    {{ $jobs->links() }}
</div>
@endsection
