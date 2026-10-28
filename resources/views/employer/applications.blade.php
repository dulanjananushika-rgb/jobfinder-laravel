@extends('layouts.app')
@section('title', 'Candidate Applications & ATS Pipeline - Employer Portal')

@section('content')
<div class="section-head">
    <div>
        <h1>Candidate Pipeline & Applications</h1>
        <p class="muted" style="margin:0">Review AI match scores, verified resumes, and schedule candidate interviews.</p>
    </div>
</div>

<form class="card actions" method="get" style="margin-bottom: 20px; padding: 14px 18px;">
    <span class="muted" style="font-size: 13px; font-weight:600;">Filters:</span>
    <select name="status" style="width: auto; min-width: 160px; min-height: 36px; padding: 4px 10px; font-size: 13px;">
        <option value="">All Statuses</option>
        @foreach(['pending', 'reviewed', 'shortlisted', 'interview', 'hired', 'rejected', 'withdrawn'] as $status)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>

    <select name="min_match" style="width: auto; min-width: 170px; min-height: 36px; padding: 4px 10px; font-size: 13px;">
        <option value="">Any AI Match Score</option>
        <option value="85" @selected(request('min_match') === '85')>⭐ Exceptional (85%+ Match)</option>
        <option value="70" @selected(request('min_match') === '70')>✓ Strong (70%+ Match)</option>
        <option value="50" @selected(request('min_match') === '50')>Moderate (50%+ Match)</option>
    </select>

    <button class="btn btn-sm" type="submit">Filter Pipeline</button>
    @if(request('status') || request('min_match'))
        <a class="btn ghost btn-sm" href="{{ route('employer.applications.index') }}">Reset</a>
    @endif
</form>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Candidate</th>
                    <th>Target Vacancy</th>
                    <th>AI Match Score</th>
                    <th>Status</th>
                    <th>Resume</th>
                    <th>Candidate Actions</th>
                    <th>Status Update</th>
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
                        <div style="font-weight: 700; color: var(--text-heading);">{{ $application->jobSeeker->name }}</div>
                        <div class="muted" style="font-size: 12px;">{{ $application->jobSeeker->email }}</div>
                        @if($application->jobSeeker->phone)
                            <div class="muted" style="font-size: 11.5px;">📞 {{ $application->jobSeeker->phone }}</div>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('jobs.show', $application->job) }}" style="color:var(--primary); font-weight:600;">
                            {{ $application->job->title }}
                        </a>
                    </td>
                    <td>
                        @if($application->ai_match_score)
                            <span class="badge {{ $application->ai_match_score >= 80 ? 'green' : ($application->ai_match_score >= 65 ? 'blue' : 'gray') }}" title="{{ $application->ai_summary }}">
                                🤖 {{ $application->ai_match_score }}% Match
                            </span>
                        @else
                            <span class="muted" style="font-size:12px;">Pending analysis</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($application->status) }}</span>
                        @if($application->interview)
                            <div style="font-size: 11px; color: #b45309; margin-top: 3px; font-weight: 600;">
                                📅 {{ $application->interview->scheduled_at->format('M d, h:i A') }}
                            </div>
                        @endif
                        @if($application->statusHistories->isNotEmpty())
                            <div class="muted" style="font-size: 10.5px; margin-top: 3px;">
                                🕒 {{ $application->statusHistories->first()->created_at->diffForHumans() }}
                                @if($application->statusHistories->first()->notes)
                                    <div style="font-style: italic; color: #64748b; font-size:10px;">"{{ Str::limit($application->statusHistories->first()->notes, 30) }}"</div>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td>
                        <a class="btn secondary btn-sm" href="{{ route('applications.resume', $application) }}" target="_blank">
                            📄 CV Document
                        </a>
                    </td>
                    <td>
                        <div class="actions">
                            <a class="btn secondary btn-sm" href="{{ route('applications.messages', $application) }}" title="Direct Chat & Schedule Interview">
                                💬 Chat / Schedule
                                @if($application->messages->count() > 0)
                                    <span style="background:var(--primary); color:#fff; border-radius:999px; font-size:10px; padding:1px 5px; margin-left:2px;">{{ $application->messages->count() }}</span>
                                @endif
                            </a>
                        </div>
                    </td>
                    <td>
                        @if($application->status === 'withdrawn')
                            <span class="muted" style="font-size: 12px; font-style: italic;">
                                ⚠️ Withdrawn
                            </span>
                        @else
                            <form method="post" action="{{ route('employer.applications.status', $application) }}" style="display:flex; gap:4px; align-items:center;">
                                @csrf
                                @method('PUT')
                                <select name="status" style="width: auto; min-height: 32px; padding: 2px 6px; font-size: 12px;">
                                    @foreach(['pending', 'reviewed', 'shortlisted', 'interview', 'hired', 'rejected'] as $status)
                                        <option value="{{ $status }}" @selected($application->status === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm" style="min-height:32px; padding:0 8px;" type="submit">Save</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 48px;">
                        <div style="font-size: 28px; margin-bottom: 6px;">👥</div>
                        <p class="muted">No candidate applications found matching filter parameters.</p>
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
