@extends('layouts.app')
@section('title', 'Browse Verified Jobs - JobFinder.lk')

@section('hero')
<section class="hero">
    <div class="hero-content">
        <div class="hero-eyebrow">
            <span>🔍</span>
            <span>Explore Vacancies</span>
        </div>
        <h1>Browse Jobs in <span class="hero-emerald-text">Sri Lanka</span></h1>
        <p class="hero-subtitle">Filter by industry, location, job type, and experience level to find your next opportunity.</p>

        <form class="hero-search" method="get" action="{{ route('jobs.index') }}">
            <div class="search-field-box">
                <span class="search-field-icon">🔍</span>
                <input name="search" placeholder="Job title, skill, or employer" value="{{ request('search') }}">
            </div>
            <div class="search-field-box">
                <span class="search-field-icon">📍</span>
                <input name="location" placeholder="District or Remote" value="{{ request('location') }}">
            </div>
            <button class="btn" type="submit">Filter Jobs</button>
        </form>
    </div>
</section>
@endsection

@section('content')
<div class="section-head">
    <div>
        <h2>Available Vacancies</h2>
        <p class="muted" style="margin:0">Showing {{ $jobs->total() }} verified openings.</p>
    </div>
    <form method="get" class="actions">
        @foreach(request()->except('sort','page') as $key => $value)
            @if(is_array($value))
                @foreach($value as $item)<input type="hidden" name="{{ $key }}[]" value="{{ $item }}">@endforeach
            @else
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
        <select name="sort" onchange="this.form.submit()" style="min-height:36px; padding:4px 10px; font-size:13px; width:auto;">
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

            <div style="border-bottom: 1px solid var(--border-light); padding-bottom: 12px; margin-bottom: 6px;">
                <strong style="font-size:13.5px; color:var(--text-heading);">Employment Type</strong>
                <div style="margin-top: 6px;">
                    @foreach($types as $type)
                        <label class="checkbox-row">
                            <span>{{ $type }}</span>
                            <input type="checkbox" name="type[]" value="{{ $type }}" @checked(in_array($type, (array) request('type')))>
                        </label>
                    @endforeach
                </div>
            </div>

            <div style="border-bottom: 1px solid var(--border-light); padding-bottom: 12px; margin-bottom: 6px;">
                <strong style="font-size:13.5px; color:var(--text-heading);">Experience Level</strong>
                <div style="margin-top: 6px;">
                    @foreach(['Entry', 'Intermediate', 'Expert'] as $experience)
                        <label class="checkbox-row">
                            <span>{{ $experience }} Level</span>
                            <input type="radio" name="experience" value="{{ $experience }}" @checked(request('experience') === $experience)>
                        </label>
                    @endforeach
                </div>
            </div>

            <div style="border-bottom: 1px solid var(--border-light); padding-bottom: 12px; margin-bottom: 6px;">
                <strong style="font-size:13.5px; color:var(--text-heading);">Monthly Salary (Rs)</strong>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:6px; margin-top:8px;">
                    <input type="number" step="10000" name="min_salary" placeholder="Min Rs" value="{{ request('min_salary') }}" style="min-height:34px; font-size:12.5px; padding:4px 8px;">
                    <input type="number" step="10000" name="max_salary" placeholder="Max Rs" value="{{ request('max_salary') }}" style="min-height:34px; font-size:12.5px; padding:4px 8px;">
                </div>
            </div>

            <div style="margin-bottom: 6px;">
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

            <div style="margin-bottom: 12px;">
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

        <!-- Job Alerts Email Subscription Card -->
        <div class="card" style="margin-top: 18px; padding: 18px; background: #f0fdf4; border: 1px solid #bbf7d0;">
            <div style="font-weight: 700; font-size: 13.5px; color: #047857; margin-bottom: 4px; display:flex; align-items:center; gap:6px;">
                <span>🔔</span> Get Job Alerts
            </div>
            <p class="muted" style="font-size: 12px; margin-bottom: 12px; line-height: 1.4;">Never miss a matching vacancy. Receive daily email alerts for new job postings.</p>

            <form class="form" method="post" action="{{ route('job-alerts.store') }}">
                @csrf
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                @if(request('location'))
                    <input type="hidden" name="location" value="{{ request('location') }}">
                @endif
                @if(request('search'))
                    <input type="hidden" name="keyword" value="{{ request('search') }}">
                @endif

                <label style="margin-bottom:8px;">
                    <input type="email" name="email" value="{{ auth()->check() ? auth()->user()->email : '' }}" placeholder="your.name@email.com" required style="min-height:36px; font-size:12.5px;">
                </label>
                <button class="btn btn-sm" type="submit" style="width:100%; justify-content:center;">Activate Alert</button>
            </form>
        </div>
    </aside>

    <div>
        <section class="grid grid-3">
            @forelse($jobs as $job)
                @include('jobs.partials.card', ['job' => $job])
            @empty
                <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 48px;">
                    <p class="muted">No vacancies found matching your criteria.</p>
                    <a class="btn secondary btn-sm" style="margin-top: 12px;" href="{{ route('jobs.index') }}">Clear Filters</a>
                </div>
            @endforelse
        </section>

        <div style="margin-top: 28px;">
            {{ $jobs->links() }}
        </div>
    </div>
</div>
@endsection
