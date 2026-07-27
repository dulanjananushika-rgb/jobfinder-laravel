@extends('layouts.app')
@section('title', 'Saved Jobs')
@section('content')
<h1>Saved Jobs</h1>
<div class="grid grid-3">
    @forelse($jobs as $job)
        @include('jobs.partials.card', ['job' => $job])
    @empty
        <p class="muted">You have not saved jobs yet.</p>
    @endforelse
</div>
{{ $jobs->links() }}
@endsection
