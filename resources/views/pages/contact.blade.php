@extends('layouts.app')
@section('title', 'Contact Us - JobFinder Support')

@section('content')
<div class="section-head">
    <div>
        <h1>Get in Touch</h1>
        <p class="muted" style="margin:0">Have questions about employer verification, candidate applications, or partnership?</p>
    </div>
</div>

<div class="grid grid-2" style="align-items: start;">
    <section class="card" style="padding: 32px;">
        <h2 style="font-size: 20px; margin-bottom: 8px;">Send a Direct Message</h2>
        <p class="muted" style="font-size: 13.5px; margin-bottom: 24px;">Our Colombo operations team responds within 24 business hours.</p>

        <form class="form" method="post" action="{{ route('contact.store') }}">
            @csrf

            <label>
                <span>Full Name <span style="color:#ef4444">*</span></span>
                <input name="name" value="{{ old('name') }}" placeholder="Your name" required>
            </label>

            <label>
                <span>Email Address <span style="color:#ef4444">*</span></span>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="you@company.com" required>
            </label>

            <label>
                <span>Subject <span style="color:#ef4444">*</span></span>
                <input name="subject" value="{{ old('subject') }}" placeholder="How can we assist you?" required>
            </label>

            <label>
                <span>Message Details <span style="color:#ef4444">*</span></span>
                <textarea name="message" placeholder="Provide details about your query..." required style="min-height: 140px;">{{ old('message') }}</textarea>
            </label>

            <button class="btn" style="margin-top: 8px;" type="submit">Submit Inquiry →</button>
        </form>
    </section>

    <aside class="grid" style="gap: 20px;">
        <div class="card" style="padding: 24px;">
            <div style="font-size: 24px; margin-bottom: 8px;">📍</div>
            <h3>Headquarters</h3>
            <p class="muted" style="font-size: 14px;">Galle Road, Colombo 03, Sri Lanka</p>
        </div>

        <div class="card" style="padding: 24px;">
            <div style="font-size: 24px; margin-bottom: 8px;">📞</div>
            <h3>Direct Hotline</h3>
            <p style="font-size: 16px; font-weight: 700; color: var(--brand-primary); margin-top: 4px;">+94 11 234 5678</p>
            <p class="muted" style="font-size: 13px;">Monday to Friday, 8:30 AM – 5:30 PM</p>
        </div>

        <div class="card" style="padding: 24px;">
            <div style="font-size: 24px; margin-bottom: 8px;">✉️</div>
            <h3>Direct Email</h3>
            <p style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin-top: 4px;">support@jobfinder.lk</p>
            <p class="muted" style="font-size: 13px;">For employer verification and billing queries.</p>
        </div>
    </aside>
</div>
@endsection
