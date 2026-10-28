@extends('layouts.app')
@section('title', 'My Applications - JobFinder')

@section('content')
<div class="section-head">
    <div>
        <h1>My Applications</h1>
        <p class="muted" style="margin:0">Track your application pipeline, review stages, and access submitted resumes.</p>
    </div>
    <a class="btn btn-sm" href="{{ route('jobs.index') }}">+ Apply for More Jobs</a>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Job Position</th>
                    <th>Company</th>
                    <th>Status</th>
                    <th>Chat & Interview</th>
                    <th>Submitted Resume</th>
                    <th>Applied Date</th>
                    <th>Lifecycle Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse($applications as $application)
                @php
                    $badgeClass = match($application->status) {
                        'hired' => 'green',
                        'interview' => 'peach',
                        'shortlisted' => 'purple',
                        'reviewed' => 'blue',
                        'rejected', 'withdrawn' => 'red',
                        default => 'peach',
                    };
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('jobs.show', $application->job) }}" style="color:var(--primary); font-weight:700;">
                            {{ $application->job->title }}
                        </a>
                        <div class="muted" style="font-size:12px;">📍 {{ $application->job->location }}</div>
                    </td>
                    <td>
                        <a href="{{ route('companies.show', $application->job->employer) }}" style="font-weight:600; color:var(--text-heading);">
                            {{ $application->job->employer->company_name ?: $application->job->employer->name }}
                        </a>
                    </td>
                    <td>
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($application->status) }}</span>
                        @if($application->interview)
                            <div style="font-size: 11px; color: #b45309; margin-top: 3px; font-weight: 600;">
                                📅 Interview: {{ $application->interview->scheduled_at->format('M d, h:i A') }}
                            </div>
                        @endif
                        @if($application->withdrawn_at)
                            <div class="muted" style="font-size:11.5px; margin-top:2px;">
                                Withdrawn {{ $application->withdrawn_at->format('M d, Y') }}
                            </div>
                        @endif
                    </td>
                    <td>
                        <a class="btn secondary btn-sm" href="{{ route('applications.messages', $application) }}">
                            💬 View Thread
                        </a>
                    </td>
                    <td>
                        <a class="btn secondary btn-sm" href="{{ route('applications.resume', $application) }}" target="_blank">
                            📄 Download
                        </a>
                    </td>
                    <td>{{ $application->created_at->format('M d, Y') }}</td>
                    <td>
                        @if(! in_array($application->status, ['withdrawn', 'hired', 'rejected'], true))
                            <form method="post" action="{{ route('seeker.applications.withdraw', $application) }}" style="display:flex; gap:6px; align-items:center;">
                                @csrf
                                @method('PUT')
                                <input name="withdraw_reason" placeholder="Optional reason" style="min-height:32px; padding:3px 6px; font-size:11.5px; width:140px;">
                                <button class="btn danger btn-sm" onclick="return confirm('Withdraw application?')">Withdraw</button>
                            </form>
                        @else
                            <span class="muted" style="font-size:12px;">Completed</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 48px;">
                        <div style="font-size: 28px; margin-bottom: 6px;">📬</div>
                        <p class="muted">You haven't submitted any job applications yet.</p>
                        <a class="btn btn-sm" style="margin-top: 12px;" href="{{ route('jobs.index') }}">Browse Open Vacancies</a>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 24px;">
    {{ $applications->links() }}
</div>
@endsection
