@extends('layouts.app')
@section('title', 'My Applications')
@section('content')
<h1>My Applications</h1>
<div class="card">
    <table>
        <thead><tr><th>Job</th><th>Company</th><th>Status</th><th>Applied</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($applications as $application)
            <tr>
                <td><a href="{{ route('jobs.show', $application->job) }}">{{ $application->job->title }}</a></td>
                <td>{{ $application->job->employer->company_name ?: $application->job->employer->name }}</td>
                <td>
                    <span class="badge">{{ ucfirst($application->status) }}</span>
                    @if($application->withdrawn_at)
                        <br><span class="muted">Withdrawn {{ $application->withdrawn_at->format('M d, Y') }}</span>
                    @endif
                </td>
                <td>{{ $application->created_at->format('M d, Y') }}</td>
                <td>
                    @if(! in_array($application->status, ['withdrawn', 'hired', 'rejected'], true))
                        <form class="form" method="post" action="{{ route('seeker.applications.withdraw', $application) }}">
                            @csrf
                            @method('PUT')
                            <input name="withdraw_reason" placeholder="Optional reason">
                            <button class="btn secondary">Withdraw</button>
                        </form>
                    @else
                        <span class="muted">No action</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5">No applications yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
{{ $applications->links() }}
@endsection
