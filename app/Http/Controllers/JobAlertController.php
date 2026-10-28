<?php

namespace App\Http\Controllers;

use App\Models\JobAlert;
use Illuminate\Http\Request;

class JobAlertController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:100'],
            'keyword' => ['nullable', 'string', 'max:100'],
        ]);

        JobAlert::updateOrCreate(
            [
                'email' => $data['email'],
                'category' => $data['category'] ?? null,
                'location' => $data['location'] ?? null,
            ],
            [
                'keyword' => $data['keyword'] ?? null,
                'is_active' => true,
            ]
        );

        return back()->with('status', 'Job alert activated! We will email you when matching vacancies are published.');
    }
}
