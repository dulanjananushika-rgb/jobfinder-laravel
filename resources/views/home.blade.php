@extends('layouts.app')
@section('title', 'JobFinder - Sri Lanka Job Portal')
@section('hero')
<section class="hero">
    <h1>Find trusted jobs across Sri Lanka <span class="spark">✦</span></h1>
    <form class="hero-search" method="get" action="{{ route('jobs.index') }}">
        <input name="search" placeholder="Laravel, Banking, Sales, Accounts">
        <input name="location" placeholder="Colombo, Kandy, Galle, Remote">
        <button class="btn">Search</button>
    </form>
</section>
@endsection
@section('content')
<div class="section-head">
    <div>
        <h2>Recommended jobs in Sri Lanka</h2>
        <p class="muted" style="margin:0">Fresh roles from verified employers, ready for real applications.</p>
    </div>
    <a class="btn secondary" href="{{ route('jobs.index') }}">Most recent</a>
</div>
<div class="grid grid-4">
    @foreach($stats as $label => $value)
        <div class="card"><h2>{{ $value }}</h2><p class="muted">{{ ucfirst($label) }}</p></div>
    @endforeach
</div>
<br>
<div class="section-head">
    <h2>Popular searches</h2>
</div>
<div class="actions" style="margin-bottom:18px">
    @foreach(['Software Engineer', 'Accounts Assistant', 'Digital Marketing', 'Banking Trainee', 'Call Center', 'Graphic Designer', 'HR Executive', 'Data Entry', 'Store Keeper'] as $term)
        <a class="badge blue" href="{{ route('jobs.index', ['search' => $term]) }}">{{ $term }}</a>
    @endforeach
</div>
<div class="grid grid-3">
    @forelse($featuredJobs as $job)
        @include('jobs.partials.card', ['job' => $job])
    @empty
        <p class="muted">No active jobs yet.</p>
    @endforelse
</div>
@endsection
