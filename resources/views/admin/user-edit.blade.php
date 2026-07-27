@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
<div class="card">
    <h1>Edit {{ $user->name }}</h1>
    <form class="form" method="post" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('admin.partials.user-form', ['user' => $user, 'creating' => false])
        <button class="btn">Update User</button>
    </form>
</div>
@endsection
