@extends('layouts.app')
@section('title', 'Profile')
@section('content')
<h1>Profile Settings</h1>
<div class="grid grid-2">
    <section class="card">
        <h2>Personal Details</h2>
        <form class="form" method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <label>Name <input name="name" value="{{ old('name', $user->name) }}" required></label>
            <label>Email <input type="email" name="email" value="{{ old('email', $user->email) }}" required></label>
            <label>Phone <input name="phone" value="{{ old('phone', $user->phone) }}"></label>
            <label>Company <input name="company_name" value="{{ old('company_name', $user->company_name) }}"></label>
            <label>Website <input name="website" value="{{ old('website', $user->website) }}"></label>
            <label>Address <textarea name="address">{{ old('address', $user->address) }}</textarea></label>
            <label>Bio <textarea name="bio">{{ old('bio', $user->bio) }}</textarea></label>
            <label>Profile Picture <input type="file" name="profile_picture" accept="image/*"></label>
            <button class="btn">Update Profile</button>
        </form>
    </section>
    <section class="card">
        <h2>Password</h2>
        <form class="form" method="post" action="{{ route('profile.password') }}">
            @csrf @method('PUT')
            <label>Current Password <input type="password" name="current_password" required></label>
            <label>New Password <input type="password" name="password" required></label>
            <label>Confirm New Password <input type="password" name="password_confirmation" required></label>
            <button class="btn">Update Password</button>
        </form>
    </section>
</div>
@endsection
