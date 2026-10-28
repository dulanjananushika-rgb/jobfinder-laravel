<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'regex:/^0\d{9}$/'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_size' => ['nullable', 'string', 'max:100'],
            'company_perks' => ['nullable', 'string', 'max:2000'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'headline' => ['nullable', 'string', 'max:255'],
            'skills' => ['nullable', 'string', 'max:2000'],
            'experience' => ['nullable', 'string', 'max:5000'],
            'education' => ['nullable', 'string', 'max:5000'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('profile-pictures', 'public');
        }

        if (!empty($data['company_name'])) {
            $data['company_slug'] = str($data['company_name'])->slug()->toString();
        }

        $user->update($data);

        return back()->with('status', 'Profile updated successfully.');
    }

    public function password(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->numbers()->symbols()],
        ]);

        auth()->user()->update(['password' => Hash::make($data['password'])]);

        return back()->with('status', 'Password updated.');
    }
}
