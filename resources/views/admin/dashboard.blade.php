@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<h1>Admin Dashboard</h1>
<div class="grid grid-4">
    <a class="card" href="{{ route('admin.users') }}"><h2>{{ $usersCount }}</h2><p>Users</p></a>
    <a class="card" href="{{ route('admin.jobs') }}"><h2>{{ $jobsCount }}</h2><p>Jobs</p></a>
    <a class="card" href="{{ route('admin.applications') }}"><h2>{{ $applicationsCount }}</h2><p>Applications</p></a>
    <a class="card" href="{{ route('admin.messages') }}"><h2>{{ $messagesCount }}</h2><p>New Messages</p></a>
</div>
@endsection
