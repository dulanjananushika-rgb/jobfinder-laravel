@extends('layouts.app')
@section('title', 'Admin Users')
@section('content')
<div class="section-head"><h1>Manage Users</h1><a class="btn secondary" href="#add-user">Add New User</a></div>
<form class="card form" method="get">
    <div class="grid grid-3">
        <input name="search" placeholder="Search name, email or company" value="{{ request('search') }}">
        <select name="role"><option value="">All roles</option>@foreach(['admin','employer','job_seeker'] as $role)<option value="{{ $role }}" @selected(request('role')===$role)>{{ $role }}</option>@endforeach</select>
        <button class="btn">Filter</button>
    </div>
</form>
<br>
<div class="card">
<table><thead><tr><th>Name</th><th>Email</th><th>Company</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@foreach($users as $user)
<tr>
    <td><a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a></td><td>{{ $user->email }}</td><td>{{ $user->company_name ?: '-' }}</td>
    <td><span class="badge blue">{{ $user->role }}</span></td><td><span class="badge green">{{ $user->status }}</span></td>
    <td class="actions"><a href="{{ route('admin.users.edit', $user) }}">Edit</a><form method="post" action="{{ route('admin.users.delete', $user) }}">@csrf @method('DELETE') <button class="btn danger">Delete</button></form></td>
</tr>
@endforeach
</tbody></table>
</div>
{{ $users->links() }}
<br>
<section class="card" id="add-user">
    <h2>Add New User</h2>
    <form class="form" method="post" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.partials.user-form', ['user' => new \App\Models\User(), 'creating' => true])
        <button class="btn">Create User</button>
    </form>
</section>
@endsection
