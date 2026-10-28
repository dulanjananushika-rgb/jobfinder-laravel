<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect to OAuth provider or handle sandbox sign-in for local demo.
     */
    public function redirect(string $provider)
    {
        abort_unless(in_array($provider, ['google', 'linkedin']), 404);

        // In local/sandbox development without live client secrets, provide instant demo sign-in
        if (!config("services.{$provider}.client_id")) {
            $email = "demo.{$provider}@jobfinder.lk";
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => ucfirst($provider) . ' Verified Seeker',
                    'password' => Hash::make(Str::random(16)),
                    'role' => 'job_seeker',
                    'status' => 'active',
                    'phone' => '0771234567',
                ]
            );

            Auth::login($user);

            return redirect()->route('dashboard')->with('status', "Successfully signed in via {$provider} (Sandbox Demo).");
        }

        // Production Socialite redirect
        // return Socialite::driver($provider)->redirect();
        return redirect()->route('login');
    }

    public function callback(string $provider)
    {
        abort_unless(in_array($provider, ['google', 'linkedin']), 404);

        return redirect()->route('dashboard');
    }
}
