@extends('layouts.app')
@section('title', 'Candidate Applications - Employer Portal')

@section('content')
<div class="section-head">
    <div>
        <h1>Candidate Applications</h1>
        <p class="muted" style="margin:0">Review applicant qualifications, download verified resumes, and manage hiring stages.</p>
    </div>
</div>

<form class="card actions" method="get" style="margin-bottom: 24px; padding: 16px 20px;">
    <span class="muted" style="font-size: 13.5px; font-weight:600;">Filter by Status:</span>
    <select name="status" style="width: auto; min-width: 180px; min-height: 38px; padding: 6px 12px; font-size: 13.5px;">
        <option value="">All Candidates (Any Status)</option>
        @foreach(['pending', 'reviewed', 'shortlisted', 'interview', 'hired', 'rejected', 'withdrawn'] as $status)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
    <button class="btn btn-sm" type="submit">Filter Pipeline</button>
    @if(request('status'))
        <a class="btn ghost btn-sm" href="{{ route('employer.applications.index') }}">Reset Filter</a>
    @endif
</form>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Candidate</th>
                    <th>Target Position</th>
                    <th>Contact Info</th>
                    <th>Current Status</th>
                    <th>Verified Resume</th>
                    <th>Cover Letter</th>
                    <th>Pipeline Action</th>
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
                        <div style="font-weight: 700; color: var(--text-primary);">{{ $application->jobSeeker->name }}</div>
                        <div class="muted" style="font-size: 11.5px;">Applied {{ $application->created_at->diffForHumans() }}</div>
                    </td>
                    <td>
                        <a href="{{ route('jobs.show', $application->job) }}" style="color:var(--brand-primary); font-weight:600;">
                            {{ $application->job->title }}
                        </a>
                    </td>
                    <td>
                        <div style="font-size:13px;">{{ $application->jobSeeker->email }}</div>
                        <div class="muted" style="font-size:12px;">{{ $application->jobSeeker->phone ?: 'No phone provided' }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($application->status) }}</span>
                    </td>
                    <td>
                        <a class="btn secondary btn-sm" href="{{ route('applications.resume', $application) }}" target="_blank">
                            📄 Download CV
                        </a>
                    </td>
                    <td>
                        <div style="max-width: 220px; font-size: 13px; color: var(--text-secondary); line-height: 1.4;">
                            {{ \Illuminate\Support\Str::limit($application->cover_letter, 110) }}
                        </div>
                    </td>
                    <td>
                        @if($application->status === 'withdrawn')
                            <span class="muted" style="font-size: 12px; font-style: italic;">
                                ⚠️ {{ $application->withdraw_reason ?: 'Candidate withdrew application' }}
                            </span>
                        @else
                            <form method="post" action="{{ route('employer.applications.status', $application) }}" style="display:flex; gap:6px; align-items:center;">
                                @csrf
                                @method('PUT')
                                <select name="status" style="width: auto; min-height: 34px; padding: 4px 8px; font-size: 12.5px;">
                                    @foreach(['pending', 'reviewed', 'shortlisted', 'interview', 'hired', 'rejected'] as $status)
                                        <option value="{{ $status }}" @selected($application->status === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm" type="submit">Update</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 48px;">
                        <div style="font-size: 32px; margin-bottom: 8px;">👥</div>
                        <p class="muted">No candidate applications found for your listings.</p>
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
