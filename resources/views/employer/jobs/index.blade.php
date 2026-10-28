@extends('layouts.app')
@section('title', 'Manage Job Listings - Employer Portal')

@section('content')
<div class="section-head">
    <div>
        <h1>Manage Vacancies</h1>
        <p class="muted" style="margin:0">Track your published roles, views analytics, admin approvals, and candidate traction.</p>
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
                    <th>Tier / Promotion</th>
                    <th>Job Views</th>
                    <th>Applicants</th>
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
                        <a href="{{ route('jobs.show', $job) }}" style="font-weight:700; color:var(--text-heading);">
                            {{ $job->title }}
                        </a>
                        <div class="muted" style="font-size:12px;">📍 {{ $job->location }} &bull; {{ $job->type }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($job->status) }}</span>
                        @if($job->approved_at)
                            <div class="muted" style="font-size:11px; margin-top:2px;">{{ $job->approved_at->format('M d, Y') }}</div>
                        @elseif($job->status === 'rejected' && $job->rejection_reason)
                            <div class="muted" style="font-size:11px; color:#ef4444; margin-top:2px;">{{ $job->rejection_reason }}</div>
                        @endif
                    </td>
                    <td>
                        @if($job->is_featured)
                            <span class="badge amber" style="background:#fffbeb; color:#b45309; border-color:#fde68a;">
                                ★ Featured Active
                            </span>
                        @elseif($job->status === 'active')
                            <form method="post" action="{{ route('employer.jobs.promote', $job) }}" style="display:inline;">
                                @csrf
                                <button class="btn secondary btn-sm" style="font-size:11.5px; border-color:#f59e0b; color:#b45309;" title="Pin to top of search results">
                                    ⚡ Boost to Featured
                                </button>
                            </form>
                        @else
                            <span class="muted" style="font-size:12px;">Standard</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-weight:700; font-family:var(--font-heading);">{{ number_format($job->views_count) }}</span>
                        <span class="muted" style="font-size:12px;"> views</span>
                    </td>
                    <td>
                        <a href="{{ route('employer.applications.index') }}" style="font-weight:700; color:var(--primary);">
                            {{ $job->applications_count }}
                        </a>
                        <span class="muted" style="font-size:12px;"> candidates</span>
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
