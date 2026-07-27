@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="card" style="max-width:520px;margin:auto">
    <h1>Login</h1>
    <form class="form" method="post" action="{{ route('login.store') }}">
        @csrf
        <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>
        <label>Password <input type="password" name="password" required></label>
        <label style="display:flex;gap:8px;align-items:center;font-weight:400"><input style="width:auto;min-height:auto" type="checkbox" name="remember" value="1"> Remember me</label>
        <button class="btn">Login</button>
    </form>
</div>
@endsection
