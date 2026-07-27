@extends('layouts.app')
@section('title', 'User Details')
@section('content')
<div class="grid grid-2">
    <section class="card">
        <span class="badge blue">{{ $user->role }}</span>
        <h1>{{ $user->name }}</h1>
        <p class="muted">{{ $user->email }}</p>
        <p><strong>Status:</strong> {{ $user->status }}</p>
        <p><strong>Phone:</strong> {{ $user->phone ?: 'Not provided' }}</p>
        <p><strong>Company:</strong> {{ $user->company_name ?: 'Not provided' }}</p>
        <p><strong>Website:</strong> @if($user->website)<a href="{{ $user->website }}" target="_blank">{{ $user->website }}</a>@else Not provided @endif</p>
        <p><strong>Address:</strong><br>{!! nl2br(e($user->address ?: 'Not provided')) !!}</p>
        <p><strong>Bio:</strong><br>{!! nl2br(e($user->bio ?: 'Not provided')) !!}</p>
        <div class="actions"><a class="btn" href="{{ route('admin.users.edit', $user) }}">Edit</a><a class="btn secondary" href="{{ route('admin.users') }}">Back</a></div>
    </section>
    <section class="grid grid-3">
        <div class="card"><h2>{{ $user->jobs_count }}</h2><p class="muted">Jobs</p></div>
        <div class="card"><h2>{{ $user->applications_count }}</h2><p class="muted">Applications</p></div>
        <div class="card"><h2>{{ $user->saved_jobs_count }}</h2><p class="muted">Saved Jobs</p></div>
    </section>
</div>
@endsection
