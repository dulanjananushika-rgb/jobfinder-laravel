@extends('layouts.app')
@section('title', 'About Us - JobFinder.lk')

@section('content')
<div class="grid grid-2" style="margin-bottom: 36px; align-items: center;">
    <section class="card" style="padding: 36px;">
        <span class="badge blue" style="margin-bottom: 12px;">About JobFinder</span>
        <h1 style="font-size: 32px; margin-bottom: 14px;">Sri Lanka's Modern Career Platform</h1>
        <p style="font-size: 15px; line-height: 1.7; margin-bottom: 20px;">
            JobFinder connects ambitious professionals with verified, authentic employers across all 25 districts of Sri Lanka. Built on trust, transparent compensation, and private resume security.
        </p>
        <div class="actions">
            <a class="btn" href="{{ route('jobs.index') }}">Explore Openings</a>
            <a class="btn secondary" href="{{ route('contact') }}">Contact Our Team</a>
        </div>
    </section>

    <section class="grid grid-2">
        <div class="card">
            <div style="font-size: 24px; margin-bottom: 8px;">🎯</div>
            <h2>Mission</h2>
            <p class="muted">Eliminate fraudulent postings and streamline hiring with verified credentials and direct application tracking.</p>
        </div>
        <div class="card">
            <div style="font-size: 24px; margin-bottom: 8px;">🔭</div>
            <h2>Vision</h2>
            <p class="muted">Empower every Sri Lankan job seeker to discover career-defining opportunities with real employers.</p>
        </div>
        <div class="card">
            <div style="font-size: 24px; margin-bottom: 8px;">🛡️</div>
            <h2>Privacy</h2>
            <p class="muted">Strict access control to resumes, protecting candidates' sensitive personal data from public exposure.</p>
        </div>
        <div class="card">
            <div style="font-size: 24px; margin-bottom: 8px;">🇱🇰</div>
            <h2>Local Impact</h2>
            <p class="muted">Engineered specifically for Sri Lankan corporate, banking, technology, and regional business domains.</p>
        </div>
    </section>
</div>

<div class="section-head">
    <div>
        <h2>Leadership & Governance</h2>
        <p class="muted" style="margin:0">Dedicated professionals committed to elevating Sri Lanka's employment standards.</p>
    </div>
</div>

<div class="grid grid-3">
    @foreach(['Rajiv Perera - Founder & CEO', 'Nayana Silva - Technology Lead', 'Chamari Fernando - Community Operations'] as $member)
        <div class="card" style="padding: 24px;">
            <div class="company-logo" style="margin-bottom: 14px; width:48px; height:48px; font-size:18px;">
                {{ substr($member, 0, 1) }}
            </div>
            <h3>{{ $member }}</h3>
            <p class="muted" style="font-size: 13.5px;">Committed to building ethical, secure, and modern digital infrastructure for local job opportunities.</p>
        </div>
    @endforeach
</div>
@endsection
