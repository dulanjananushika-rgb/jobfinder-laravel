@extends('layouts.app')
@section('title', 'JobFinder.lk - Sri Lanka Career & Employment Portal')

@section('hero')
<section class="hero">
    <div class="hero-content">
        <div class="hero-eyebrow">
            <span>✓</span>
            <span>Sri Lanka's Verified Employment Portal</span>
        </div>
        <h1>
            Connecting Sri Lanka's Talent with <span class="hero-emerald-text">Leading Employers</span>
        </h1>
        <p class="hero-subtitle">
            Explore authentic career opportunities across Colombo, Kandy, Galle, and all 25 districts with private resume submissions and real-time application tracking.
        </p>

        <form class="hero-search" method="get" action="{{ route('jobs.index') }}">
            <div class="search-field-box">
                <span class="search-field-icon">🔍</span>
                <input name="search" placeholder="Job title, keywords, or skills (e.g. Laravel, Sales, Accounts)" value="{{ request('search') }}">
            </div>
            <div class="search-field-box">
                <span class="search-field-icon">📍</span>
                <input name="location" placeholder="District or Location (e.g. Colombo, Kandy, Remote)" value="{{ request('location') }}">
            </div>
            <button class="btn" type="submit">Find Jobs</button>
        </form>
    </div>
</section>
@endsection

@section('content')
<div class="section-head">
    <div>
        <h2>Platform Overview</h2>
        <p class="muted" style="margin:0">Verified metrics across active organizations and vacancies.</p>
    </div>
</div>

<div class="grid grid-4" style="margin-bottom: 32px;">
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
                <div class="stat-label">Active {{ ucfirst($label) }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="section-head">
    <div>
        <h2>Featured Job Vacancies</h2>
        <p class="muted" style="margin:0">Recent roles published by verified Sri Lankan employers.</p>
    </div>
    <a class="btn secondary btn-sm" href="{{ route('jobs.index') }}">Browse All Vacancies →</a>
</div>

<div class="actions" style="margin-bottom: 20px;">
    <span class="muted" style="font-size: 13px; font-weight: 600;">Popular Searches:</span>
    @foreach(['Software Engineer', 'Accounts Assistant', 'Digital Marketing', 'Banking Trainee', 'Customer Support', 'Graphic Designer', 'HR Executive', 'Store Keeper'] as $term)
        <a class="badge emerald" href="{{ route('jobs.index', ['search' => $term]) }}">{{ $term }}</a>
    @endforeach
</div>

<div class="grid grid-3">
    @forelse($featuredJobs as $job)
        @include('jobs.partials.card', ['job' => $job])
    @empty
        <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 48px;">
            <p class="muted">No active vacancies listed right now. Check back shortly!</p>
        </div>
    @endforelse
</div>
@endsection
