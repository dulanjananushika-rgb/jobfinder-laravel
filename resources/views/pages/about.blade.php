@extends('layouts.app')
@section('title', 'About JobFinder')
@section('content')
<div class="grid grid-2">
    <section class="card">
        <span class="badge blue">About us</span>
        <h1>Sri Lanka's modern career marketplace</h1>
        <p>JobFinder connects job seekers with trusted employers, clear job information, saved opportunities, resume-based applications, and employer-side applicant management.</p>
    </section>
    <section class="grid grid-2">
        <div class="card"><h2>Mission</h2><p class="muted">Make hiring and job discovery simpler, safer, and faster.</p></div>
        <div class="card"><h2>Vision</h2><p class="muted">Become a reliable digital career partner for Sri Lankan talent.</p></div>
        <div class="card"><h2>Values</h2><p class="muted">Trust, clarity, fairness, and consistent communication.</p></div>
        <div class="card"><h2>Impact</h2><p class="muted">Support employers and job seekers through better matching.</p></div>
    </section>
</div>
<br>
<h2>Leadership</h2>
<div class="grid grid-3">
    @foreach(['Rajiv Perera - Founder','Nayana Silva - Technology Lead','Chamari Fernando - Community Lead'] as $member)
        <div class="card"><div class="company-logo">{{ substr($member,0,1) }}</div><h3>{{ $member }}</h3><p class="muted">Focused on building a practical, trustworthy hiring platform.</p></div>
    @endforeach
</div>
@endsection
