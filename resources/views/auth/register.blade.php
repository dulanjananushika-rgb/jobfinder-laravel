@extends('layouts.app')
@section('title', 'Register')
@section('content')
<div class="card" style="max-width:720px;margin:auto">
    <h1>Create Account</h1>
    <form class="form" method="post" action="{{ route('register.store') }}">
        @csrf
        <div class="grid grid-2">
            <label>Full Name <input name="name" value="{{ old('name') }}" required></label>
            <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>
            <label>Phone <input name="phone" value="{{ old('phone') }}" placeholder="0771234567" required></label>
            <label>Account Type
                <select name="role" required>
                    <option value="job_seeker" @selected(old('role') === 'job_seeker')>Job Seeker</option>
                    <option value="employer" @selected(old('role') === 'employer')>Employer</option>
                </select>
            </label>
        </div>
        <label>Company Name <input name="company_name" value="{{ old('company_name') }}"></label>
        <div class="grid grid-2">
            <label>Password <input type="password" name="password" required></label>
            <label>Confirm Password <input type="password" name="password_confirmation" required></label>
        </div>
        <button class="btn">Register</button>
    </form>
</div>
@endsection
