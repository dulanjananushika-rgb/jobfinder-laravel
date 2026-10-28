@extends('layouts.app')
@section('title', 'Browse Verified Jobs in Sri Lanka - JobFinder')

@section('hero')
<section class="hero">
    <div class="hero-content">
        <div class="hero-pill">
            <span>✦</span>
            <span>Live Opportunities</span>
        </div>
        <h1>Browse Jobs Across <span class="hero-gradient-text">Sri Lanka</span></h1>
        <p class="hero-subtitle">Filter by industry, location, job type, and experience level to find your ideal role.</p>

        <form class="hero-search" method="get" action="{{ route('jobs.index') }}">
            <div class="search-input-group">
                <span class="search-icon">🔍</span>
                <input name="search" placeholder="Job title, skill, or company" value="{{ request('search') }}">
            </div>
            <div class="search-input-group">
                <span class="search-icon">📍</span>
                <input name="location" placeholder="District or Remote" value="{{ request('location') }}">
            </div>
            <button class="btn" type="submit">Search</button>
        </form>
    </div>
</section>
@endsection

@section('content')
<div class="section-head">
    <div>
        <h2>Verified Vacancies</h2>
        <p class="muted" style="margin:0">Displaying {{ $jobs->total() }} available positions.</p>
    </div>
    <form method="get" class="actions">
        @foreach(request()->except('sort','page') as $key => $value)
            @if(is_array($value))
                @foreach($value as $item)<input type="hidden" name="{{ $key }}[]" value="{{ $item }}">@endforeach
            @else
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
        <select name="sort" onchange="this.form.submit()" style="min-height:38px; padding:6px 12px; font-size:13px;">
            <option value="recent" @selected(request('sort') !== 'oldest')>Sort: Most Recent</option>
            <option value="oldest" @selected(request('sort') === 'oldest')>Sort: Oldest</option>
        </select>
    </form>
</div>

<div class="browse-layout">
    <aside class="side-panel">
        <form class="form" method="get" action="{{ route('jobs.index') }}">
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif

            <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 12px; margin-bottom: 8px;">
                <strong style="font-size:14px; color:var(--text-primary);">Job Type</strong>
                <div style="margin-top: 8px;">
                    @foreach($types as $type)
                        <label class="checkbox-row">
                            <span>{{ $type }}</span>
                            <input type="checkbox" name="type[]" value="{{ $type }}" @checked(in_array($type, (array) request('type')))>
                        </label>
                    @endforeach
                </div>
            </div>

            <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 12px; margin-bottom: 8px;">
                <strong style="font-size:14px; color:var(--text-primary);">Experience Level</strong>
                <div style="margin-top: 8px;">
                    @foreach(['Entry', 'Intermediate', 'Expert'] as $experience)
                        <label class="checkbox-row">
                            <span>{{ $experience }} Level</span>
                            <input type="radio" name="experience" value="{{ $experience }}" @checked(request('experience') === $experience)>
                        </label>
                    @endforeach
                </div>
            </div>

            <div style="margin-bottom: 8px;">
                <label>
                    <span>Industry Category</span>
                    <select name="category">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div style="margin-bottom: 14px;">
                <label>
                    <span>District / Location</span>
                    <select name="location">
                        <option value="">All Districts</option>
                        @foreach($locations as $location)
                            <option value="{{ $location }}" @selected(request('location') === $location)>{{ $location }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="actions" style="margin-top: 6px;">
                <button class="btn btn-sm" style="flex:1;" type="submit">Apply Filters</button>
                <a class="btn secondary btn-sm" href="{{ route('jobs.index') }}">Reset</a>
            </div>
        </form>
    </aside>

    <div>
        <section class="grid grid-3">
            @forelse($jobs as $job)
                @include('jobs.partials.card', ['job' => $job])
            @empty
                <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 48px;">
                    <p class="muted">No vacancies found matching your criteria. Try adjusting your filters.</p>
                    <a class="btn secondary btn-sm" style="margin-top: 14px;" href="{{ route('jobs.index') }}">Clear All Filters</a>
                </div>
            @endforelse
        </section>

        <div style="margin-top: 32px;">
            {{ $jobs->links() }}
        </div>
    </div>
</div>
@endsection
