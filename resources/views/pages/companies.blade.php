@extends('layouts.app')
@section('title', 'Registered Employers - JobFinder.lk')

@section('content')
<div class="section-head">
    <div>
        <h1>Registered Employers</h1>
        <p class="muted" style="margin:0">Verified companies and organizations actively recruiting talent in Sri Lanka.</p>
    </div>
</div>

<div class="grid grid-3">
@forelse(\App\Models\User::where('role','employer')->where('status', 'active')->latest()->get() as $company)
    <div class="card" style="display:flex; flex-direction:column; gap:12px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <div class="company-logo" style="width:44px; height:44px; font-size:18px;">
                {{ strtoupper(substr($company->company_name ?: $company->name, 0, 1)) }}
            </div>
            <div>
                <h3 style="margin-bottom:2px;">{{ $company->company_name ?: $company->name }}</h3>
                @if($company->isVerifiedEmployer())
                    <span class="badge green" style="font-size:11px;">✓ Verified</span>
                @endif
            </div>
        </div>

        <p class="muted" style="font-size:13px; line-height:1.5;">
            {{ \Illuminate\Support\Str::limit($company->bio ?: 'Verified employer registered on JobFinder.', 110) }}
        </p>

        <div style="margin-top:auto; padding-top:10px; border-top:1px solid var(--border-light); display:flex; justify-content:space-between; align-items:center;">
            <span class="badge emerald">{{ $company->jobs()->active()->count() }} active jobs</span>
            <a class="btn secondary btn-sm" href="{{ route('jobs.index', ['search' => $company->company_name ?: $company->name]) }}">View Jobs →</a>
        </div>
    </div>
@empty
    <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 48px;">
        <p class="muted">No employer profiles listed currently.</p>
    </div>
@endforelse
</div>
@endsection
