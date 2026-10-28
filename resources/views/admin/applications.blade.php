@extends('layouts.app')
@section('title', 'All Platform Applications - Admin Moderation')

@section('content')
<div class="section-head">
    <div>
        <h1>Platform Applications</h1>
        <p class="muted" style="margin:0">System-wide candidate submissions and resume verification.</p>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Candidate</th>
                    <th>Target Vacancy</th>
                    <th>Employer</th>
                    <th>Status</th>
                    <th>Secure Resume</th>
                    <th>Cover Letter</th>
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
                        <a href="{{ route('admin.users.show', $application->jobSeeker) }}" style="font-weight:700; color:var(--brand-primary);">
                            {{ $application->jobSeeker->name }}
                        </a>
                        <div class="muted" style="font-size:12px;">{{ $application->jobSeeker->email }}</div>
                    </td>
                    <td>
                        <a href="{{ route('jobs.show', $application->job) }}" style="font-weight:600;">
                            {{ $application->job->title }}
                        </a>
                    </td>
                    <td>
                        <strong>{{ $application->job->employer->company_name ?: $application->job->employer->name }}</strong>
                    </td>
                    <td>
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($application->status) }}</span>
                        <div class="muted" style="font-size:11.5px; margin-top:2px;">{{ $application->created_at->format('M d, Y') }}</div>
                    </td>
                    <td>
                        <a class="btn secondary btn-sm" href="{{ route('applications.resume', $application) }}" target="_blank">
                            📄 Download
                        </a>
                    </td>
                    <td>
                        <div style="max-width: 260px; font-size: 13px; color: var(--text-secondary); line-height: 1.4;">
                            {{ \Illuminate\Support\Str::limit($application->cover_letter, 120) }}
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 48px;">
                        <p class="muted">No applications recorded in the system.</p>
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
