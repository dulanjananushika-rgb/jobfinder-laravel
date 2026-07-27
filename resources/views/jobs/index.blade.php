@extends('layouts.app')
@section('title', 'Browse Jobs')
@section('hero')
<section class="hero">
    <h1>Find jobs that fit your life in Sri Lanka <span class="spark">✦</span></h1>
    <form class="hero-search" method="get" action="{{ route('jobs.index') }}">
        <input name="search" placeholder="Job title, skill, or company" value="{{ request('search') }}">
        <input name="location" placeholder="Colombo, Kandy, Gampaha, Remote" value="{{ request('location') }}">
        <button class="btn">Search</button>
    </form>
</section>
@endsection
@section('content')
<div class="section-head">
    <h2>Latest verified jobs</h2>
    <form method="get" class="actions">
        @foreach(request()->except('sort','page') as $key => $value)
            @if(is_array($value))
                @foreach($value as $item)<input type="hidden" name="{{ $key }}[]" value="{{ $item }}">@endforeach
            @else
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
        <select name="sort" onchange="this.form.submit()">
            <option value="recent" @selected(request('sort') !== 'oldest')>Most recent</option>
            <option value="oldest" @selected(request('sort') === 'oldest')>Oldest</option>
        </select>
    </form>
</div>
<div class="browse-layout">
    <aside class="side-panel">
        <form class="form" method="get">
            <strong>Job Type</strong>
            @foreach($types as $type)
                <label class="checkbox-row"><span>{{ $type }}</span><input type="checkbox" name="type[]" value="{{ $type }}" @checked(in_array($type, (array) request('type')))></label>
            @endforeach
            <strong>Experience Level</strong>
            @foreach(['Entry','Intermediate','Expert'] as $experience)
                <label class="checkbox-row"><span>{{ $experience }}</span><input type="radio" name="experience" value="{{ $experience }}" @checked(request('experience') === $experience)></label>
            @endforeach
            <strong>Job Categories</strong>
            <select name="category"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>@endforeach</select>
            <strong>Locations</strong>
            <select name="location"><option value="">All locations</option>@foreach($locations as $location)<option value="{{ $location }}" @selected(request('location') === $location)>{{ $location }}</option>@endforeach</select>
            <div class="actions"><button class="btn">Apply filters</button><a class="btn ghost" href="{{ route('jobs.index') }}">Clear all</a></div>
        </form>
    </aside>
    <section class="grid grid-3">
        @forelse($jobs as $job)
            @include('jobs.partials.card', ['job' => $job])
        @empty
            <div class="card"><p class="muted">No jobs found.</p></div>
        @endforelse
    </section>
</div>
<div class="pagination">{{ $jobs->links() }}</div>
@endsection
