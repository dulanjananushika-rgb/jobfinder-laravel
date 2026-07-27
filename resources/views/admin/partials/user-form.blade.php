<div class="grid grid-2">
    <label>Name <input name="name" value="{{ old('name', $user->name) }}" required></label>
    <label>Email <input type="email" name="email" value="{{ old('email', $user->email) }}" required></label>
    @if($creating ?? false)
        <label>Password <input type="password" name="password" required></label>
    @endif
    <label>Role <select name="role">@foreach(['admin','employer','job_seeker'] as $role)<option value="{{ $role }}" @selected(old('role', $user->role ?: 'job_seeker') === $role)>{{ $role }}</option>@endforeach</select></label>
    <label>Status <select name="status"><option value="active" @selected(old('status', $user->status ?: 'active') === 'active')>active</option><option value="inactive" @selected(old('status', $user->status) === 'inactive')>inactive</option></select></label>
    <label class="checkbox-row"><span>Verified Employer</span><input type="checkbox" name="employer_verified" value="1" @checked(old('employer_verified', $user->employer_verified_at !== null))></label>
    <label>Phone <input name="phone" value="{{ old('phone', $user->phone) }}"></label>
    <label>Company <input name="company_name" value="{{ old('company_name', $user->company_name) }}"></label>
    <label>Website <input name="website" value="{{ old('website', $user->website) }}"></label>
    <label>Profile Picture <input type="file" name="profile_picture" accept="image/*"></label>
</div>
<label>Address <textarea name="address">{{ old('address', $user->address) }}</textarea></label>
<label>Bio <textarea name="bio">{{ old('bio', $user->bio) }}</textarea></label>
