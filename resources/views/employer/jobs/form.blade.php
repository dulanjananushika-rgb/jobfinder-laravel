@extends('layouts.app')
@section('title', $job->exists ? 'Edit Job' : 'Post Job')
@section('content')
<div class="card">
<h1>{{ $job->exists ? 'Edit Job' : 'Post Job' }}</h1>
<form class="form" method="post" action="{{ $job->exists ? route('employer.jobs.update', $job) : route('employer.jobs.store') }}">
    @csrf @if($job->exists) @method('PUT') @endif
    <div class="grid grid-2">
        <label>Title <input name="title" value="{{ old('title', $job->title) }}" required></label>
        <label>Main Category <input name="main_category" value="{{ old('main_category', $job->main_category) }}" required></label>
        <label>Sub Category <input name="sub_category" value="{{ old('sub_category', $job->sub_category) }}"></label>
        <label>Location <input name="location" value="{{ old('location', $job->location) }}" required></label>
        <label>Type <input name="type" value="{{ old('type', $job->type) }}" placeholder="Full-time" required></label>
        <label>Salary <input name="salary" value="{{ old('salary', $job->salary) }}"></label>
        <label>Deadline <input type="date" name="deadline" value="{{ old('deadline', optional($job->deadline)->format('Y-m-d')) }}"></label>
    </div>
    <p class="muted">New or edited jobs are submitted to admin for approval before they become public.</p>
    <label>Description <textarea name="description" required>{{ old('description', $job->description) }}</textarea></label>
    <label>Requirements <textarea name="requirements" required>{{ old('requirements', $job->requirements) }}</textarea></label>
    <label>Responsibilities <textarea name="responsibilities">{{ old('responsibilities', $job->responsibilities) }}</textarea></label>
    <button class="btn">{{ $job->exists ? 'Update Job' : 'Post Job' }}</button>
</form>
</div>
@endsection
