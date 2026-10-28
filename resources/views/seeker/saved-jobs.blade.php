@extends('layouts.app')
@section('title', 'Bookmarked Jobs - JobFinder')

@section('content')
<div class="section-head">
    <div>
        <h1>Saved Jobs</h1>
        <p class="muted" style="margin:0">Vacancies you have shortlisted for later application.</p>
    </div>
    <a class="btn btn-sm" href="{{ route('jobs.index') }}">Browse More Jobs</a>
</div>

<div class="grid grid-3">
    @forelse($jobs as $job)
        @include('jobs.partials.card', ['job' => $job])
    @empty
        <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 48px;">
            <div style="font-size: 32px; margin-bottom: 8px;">📑</div>
            <p class="muted">You haven't bookmarked any jobs yet.</p>
            <a class="btn btn-sm" style="margin-top: 12px;" href="{{ route('jobs.index') }}">Find Jobs to Save</a>
        </div>
    @endforelse
</div>

<div style="margin-top: 24px;">
    {{ $jobs->links() }}
</div>
@endsection
