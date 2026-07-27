@extends('layouts.app')
@section('title', 'Contact JobFinder')
@section('content')
<div class="grid grid-2">
    <section class="card">
        <h1>Contact us</h1>
        <p class="muted">Have a hiring question, account issue, or partnership idea? Send us a message.</p>
        <form class="form" method="post" action="{{ route('contact.store') }}">
            @csrf
            <label>Name <input name="name" value="{{ old('name') }}" placeholder="Your name" required></label>
            <label>Email <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required></label>
            <label>Subject <input name="subject" value="{{ old('subject') }}" placeholder="What is this about?" required></label>
            <label>Message <textarea name="message" placeholder="Type your message" required>{{ old('message') }}</textarea></label>
            <button class="btn">Send Message</button>
        </form>
    </section>
    <aside class="grid">
        <div class="card"><h2>Phone</h2><p>+94 11 234 5678</p></div>
        <div class="card"><h2>Email</h2><p>info@jobfinder.lk</p></div>
        <div class="card"><h2>FAQ</h2><p class="muted">Job seekers can apply from each job page. Employers can manage jobs and applications from the dashboard.</p></div>
    </aside>
</div>
@endsection
