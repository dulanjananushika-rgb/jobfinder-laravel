<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = User::where('role', 'employer')
            ->where('status', 'active')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($sub) use ($request) {
                    $sub->where('company_name', 'like', '%' . $request->search . '%')
                        ->orWhere('name', 'like', '%' . $request->search . '%')
                        ->orWhere('bio', 'like', '%' . $request->search . '%');
                });
            })
            ->withCount(['jobs' => fn ($query) => $query->active()])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('pages.companies', compact('companies'));
    }

    public function show(User $company)
    {
        abort_unless($company->isEmployer() && $company->status === 'active', 404);

        $activeJobs = $company->jobs()->active()->latest()->paginate(6);

        return view('companies.show', compact('company', 'activeJobs'));
    }
}
