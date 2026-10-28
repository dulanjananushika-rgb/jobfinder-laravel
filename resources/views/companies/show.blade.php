@extends('layouts.app')
@section('title', ($company->company_name ?: $company->name) . ' - Company Showcase - JobFinder')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('companies') }}" class="btn ghost btn-sm" style="padding-left:0;">← Back to All Companies</a>
</div>

<!-- Company Header Card -->
<div class="card" style="padding: 0; overflow: hidden; margin-bottom: 28px;">
    <div style="height: 140px; background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); position: relative;"></div>
    <div style="padding: 0 32px 28px; position: relative;">
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-top: -40px; margin-bottom: 16px; flex-wrap:wrap; gap:16px;">
            <div style="display:flex; align-items:flex-end; gap:18px;">
                <div class="company-logo" style="width: 80px; height: 80px; font-size: 32px; border-radius: 12px; background: #ffffff; box-shadow: var(--shadow-sm); border: 2px solid #ffffff;">
                    {{ strtoupper(substr($company->company_name ?: $company->name, 0, 1)) }}
                </div>
                <div>
                    <h1 style="font-size: 26px; margin-bottom: 4px;">{{ $company->company_name ?: $company->name }}</h1>
                    <div class="muted" style="font-size: 13.5px; display:flex; align-items:center; gap:8px;">
                        @if($company->isVerifiedEmployer())
                            <span class="badge green">✓ Verified Organization</span>
                        @endif
                        <span>📍 {{ $company->address ?: 'Sri Lanka' }}</span>
                        @if($company->company_size)
                            <span>&bull;</span>
                            <span>👥 {{ $company->company_size }} Employees</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="actions">
                @if($company->website)
                    <a class="btn secondary btn-sm" href="{{ $company->website }}" target="_blank">🌐 Official Website ↗</a>
                @endif
                <a class="btn btn-sm" href="#openings">View {{ $activeJobs->total() }} Openings</a>
            </div>
        </div>

        <p style="font-size: 14px; line-height: 1.7; color: var(--text-body); max-width: 860px; margin-bottom: 20px;">
            {!! nl2br(e($company->bio ?: 'Verified employer registered with JobFinder Sri Lanka.')) !!}
        </p>

        @if($company->company_perks)
            <div style="border-top: 1px solid var(--border-light); padding-top: 16px;">
                <strong style="font-size: 13px; color: var(--text-heading); display:block; margin-bottom: 8px;">Company Culture & Perks:</strong>
                <div class="actions">
                    @foreach(array_filter(array_map('trim', explode(',', $company->company_perks))) as $perk)
                        <span class="badge emerald">★ {{ $perk }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Active Vacancies Section -->
<div id="openings">
    <div class="section-head">
        <div>
            <h2>Current Job Openings at {{ $company->company_name ?: $company->name }}</h2>
            <p class="muted" style="margin:0">Active vacancies available for direct application.</p>
        </div>
    </div>

    <div class="grid grid-3">
        @forelse($activeJobs as $job)
            @include('jobs.partials.card', ['job' => $job])
        @empty
            <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 48px;">
                <p class="muted">No open vacancies listed by this company at the moment.</p>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 24px;">
        {{ $activeJobs->links() }}
    </div>
</div>
@endsection
