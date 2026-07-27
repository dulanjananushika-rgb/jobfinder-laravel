<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'featuredJobs' => Job::active()->with('employer')->latest()->limit(6)->get(),
            'stats' => [
                'jobs' => Job::active()->count(),
                'companies' => User::where('role', 'employer')->count(),
                'seekers' => User::where('role', 'job_seeker')->count(),
                'hires' => JobApplication::where('status', 'hired')->count(),
            ],
        ]);
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }
}
