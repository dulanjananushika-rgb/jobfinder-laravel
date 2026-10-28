@extends('layouts.app')
@section('title', 'JobFinder.lk - Sri Lanka Premier Job Portal')

@section('hero')
<section class="hero">
    <div class="hero-content">
        <div class="hero-pill">
            <span>✦</span>
            <span>Sri Lanka's #1 Verified Career Platform</span>
        </div>
        <h1>
            Find Your Next Career in <span class="hero-gradient-text">Sri Lanka</span>
        </h1>
        <p class="hero-subtitle">
            Connect directly with verified corporate and startup employers across all 25 districts with zero intermediaries.
        </p>

        <form class="hero-search" method="get" action="{{ route('jobs.index') }}">
            <div class="search-input-group">
                <span class="search-icon">🔍</span>
                <input name="search" placeholder="Job title, skills (e.g. Laravel, React, Sales, Accounts)" value="{{ request('search') }}">
            </div>
            <div class="search-input-group">
                <span class="search-icon">📍</span>
                <input name="location" placeholder="District or Remote (e.g. Colombo, Kandy)" value="{{ request('location') }}">
            </div>
            <button class="btn" type="submit">Search Jobs</button>
        </form>
    </div>
</section>
@endsection

@section('content')
<div class="section-head">
    <div>
        <h2>Platform Highlights</h2>
        <p class="muted" style="margin:0">Real-time statistics across active employers and job vacancies.</p>
    </div>
</div>

<div class="grid grid-4" style="margin-bottom: 36px;">
    @php
        $icons = [
            'jobs' => '💼',
            'employers' => '🏢',
            'applications' => '📄',
            'seekers' => '👥',
        ];
    @endphp
    @foreach($stats as $label => $value)
        <div class="stat-card">
            <div class="stat-icon-wrap">{{ $icons[$label] ?? '📊' }}</div>
            <div>
                <div class="stat-value">{{ number_format($value) }}</div>
                <div class="stat-label">{{ ucfirst($label) }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="section-head">
    <div>
        <h2>Recommended Vacancies</h2>
        <p class="muted" style="margin:0">Fresh roles from verified employers, ready for real applications.</p>
    </div>
    <a class="btn secondary btn-sm" href="{{ route('jobs.index') }}">View All Jobs →</a>
</div>

<div class="actions" style="margin-bottom: 24px;">
    <span class="muted" style="font-size: 13px; font-weight: 600;">Popular:</span>
    @foreach(['Software Engineer', 'Accounts Assistant', 'Digital Marketing', 'Banking Trainee', 'Call Center', 'Graphic Designer', 'HR Executive', 'Store Keeper'] as $term)
        <a class="badge blue" href="{{ route('jobs.index', ['search' => $term]) }}">{{ $term }}</a>
    @endforeach
</div>

<div class="grid grid-3">
    @forelse($featuredJobs as $job)
        @include('jobs.partials.card', ['job' => $job])
    @empty
        <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 48px;">
            <p class="muted">No active jobs found right now. Check back soon!</p>
        </div>
    @endforelse
</div>
@endsection
