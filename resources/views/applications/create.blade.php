@extends('layouts.app')
@section('title', 'Apply for '.$job->title)
@section('content')
<div class="card" style="max-width:760px;margin:auto">
    <h1>Apply for {{ $job->title }}</h1>
    <form class="form" method="post" action="{{ route('applications.store', $job) }}" enctype="multipart/form-data">
        @csrf
        <label>Cover Letter <textarea name="cover_letter" required>{{ old('cover_letter') }}</textarea></label>
        <label>Resume <input type="file" name="resume" accept=".pdf,.doc,.docx" required></label>
        <button class="btn">Submit Application</button>
    </form>
</div>
@endsection
