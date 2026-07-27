<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'regex:/^0\d{9}$/'],
            'role' => ['required', 'in:job_seeker,employer'],
            'company_name' => ['nullable', 'required_if:role,employer', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)->numbers()->symbols()],
        ]);

        $user = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
            'status' => 'active',
        ]);

        Auth::login($user);

        $message = $user->isEmployer()
            ? 'Account created. Your employer account must be verified by admin before posting jobs.'
            : 'Account created successfully.';

        return redirect()->route('dashboard')->with('status', $message);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
