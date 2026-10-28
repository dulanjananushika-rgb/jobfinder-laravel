@extends('layouts.app')
@section('title', 'Flagged & Reported Vacancies - Admin Portal')

@section('content')
<div class="section-head">
    <div>
        <h1>Reported Vacancy Reports</h1>
        <p class="muted" style="margin:0">Review user-submitted scam flags, policy violations, and suspicious employer postings.</p>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Reported Job</th>
                    <th>Reported By</th>
                    <th>Reason Category</th>
                    <th>Report Details</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse($reports as $report)
                @php
                    $statusColor = match($report->status) {
                        'resolved' => 'green',
                        'dismissed' => 'gray',
                        default => 'amber',
                    };
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('jobs.show', $report->job) }}" style="font-weight:700; color:var(--primary);" target="_blank">
                            {{ $report->job->title }} ↗
                        </a>
                        <div class="muted" style="font-size:12px;">
                            Employer: {{ $report->job->employer->company_name ?: $report->job->employer->name }}
                        </div>
                    </td>
                    <td>
                        <div style="font-weight:600;">{{ $report->user ? $report->user->name : 'Anonymous Guest' }}</div>
                        <div class="muted" style="font-size:11.5px;">{{ $report->user?->email }}</div>
                    </td>
                    <td>
                        <span class="badge red">{{ str_replace('_', ' ', ucfirst($report->reason)) }}</span>
                        <div class="muted" style="font-size:11px; margin-top:2px;">{{ $report->created_at->diffForHumans() }}</div>
                    </td>
                    <td style="max-width:320px; font-size:13px; line-height:1.4;">
                        {{ $report->details }}
                    </td>
                    <td>
                        <span class="badge {{ $statusColor }}">{{ ucfirst($report->status) }}</span>
                    </td>
                    <td>
                        <form method="post" action="{{ route('admin.reports.update', $report) }}" style="display:flex; gap:4px; align-items:center;">
                            @csrf
                            @method('PUT')
                            <select name="status" style="width: auto; min-height: 32px; padding: 2px 6px; font-size: 12px;">
                                <option value="pending" @selected($report->status === 'pending')>Pending</option>
                                <option value="resolved" @selected($report->status === 'resolved')>Resolved / Handled</option>
                                <option value="dismissed" @selected($report->status === 'dismissed')>Dismiss (False alarm)</option>
                            </select>
                            <button class="btn btn-sm" style="min-height:32px; padding:0 8px;" type="submit">Save</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 48px;">
                        <div style="font-size: 32px; margin-bottom: 6px;">🛡️</div>
                        <p class="muted">No reports submitted. All active job postings comply with safety policies.</p>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 20px;">
    {{ $reports->links() }}
</div>
@endsection
