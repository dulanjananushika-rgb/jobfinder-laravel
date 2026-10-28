@extends('layouts.app')
@section('title', $job->title . ' - ' . ($job->employer->company_name ?: $job->employer->name))

@push('meta')
    <meta name="description" content="{{ Str::limit(strip_tags($job->description), 160) }}">
    <meta property="og:title" content="{{ $job->title }} - {{ $job->employer->company_name ?: $job->employer->name }} | JobFinder">
    <meta property="og:description" content="{{ Str::limit(strip_tags($job->description), 160) }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ route('jobs.show', $job->slug ?: $job->id) }}">
    <meta property="og:site_name" content="JobFinder Sri Lanka">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $job->title }} - {{ $job->employer->company_name ?: $job->employer->name }}">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($job->description), 160) }}">

    <!-- Google for Jobs Schema.org JSON-LD -->
    @php
        $jsonLd = [
            '@context' => 'https://schema.org/',
            '@type' => 'JobPosting',
            'title' => $job->title,
            'description' => $job->description,
            'datePosted' => $job->created_at->toIso8601String(),
            'employmentType' => str_contains(strtolower($job->type), 'part') ? 'PART_TIME' : (str_contains(strtolower($job->type), 'contract') ? 'CONTRACTOR' : (str_contains(strtolower($job->type), 'intern') ? 'INTERN' : 'FULL_TIME')),
            'hiringOrganization' => [
                '@type' => 'Organization',
                'name' => $job->employer->company_name ?: $job->employer->name,
                'sameAs' => $job->employer->website ?: route('companies.show', $job->employer),
            ],
            'jobLocation' => [
                '@type' => 'Place',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $job->location,
                    'addressCountry' => 'LK',
                ],
            ],
        ];

        if ($job->deadline) {
            $jsonLd['validThrough'] = $job->deadline->endOfDay()->toIso8601String();
        }

        if ($job->min_salary) {
            $salaryData = [
                '@type' => 'MonetaryAmount',
                'currency' => $job->salary_currency ?: 'LKR',
                'value' => [
                    '@type' => 'QuantitativeValue',
                    'value' => (float) $job->min_salary,
                    'unitText' => 'MONTH',
                ],
            ];
            if ($job->max_salary) {
                $salaryData['value']['minValue'] = (float) $job->min_salary;
                $salaryData['value']['maxValue'] = (float) $job->max_salary;
            }
            $jsonLd['baseSalary'] = $salaryData;
        }
    @endphp
    <script type="application/ld+json">
    {!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('jobs.index') }}" class="btn ghost btn-sm" style="padding-left:0;">← Back to All Vacancies</a>
</div>

<div class="grid grid-2" style="grid-template-columns: 1.8fr 1.2fr; align-items: start;">
    <section class="card" style="padding: 28px;">
        <div class="actions" style="margin-bottom: 14px;">
            <span class="badge emerald">{{ $job->main_category }}</span>
            <span class="badge blue">{{ $job->type }}</span>
            @if($job->sub_category)
                <span class="badge gray">{{ $job->sub_category }}</span>
            @endif
            @if($job->status === 'active')
                <span class="badge green">● Active Opening</span>
            @endif
        </div>

        <h1 style="font-size: 26px; margin-bottom: 6px;">{{ $job->title }}</h1>
        <div class="muted" style="display:flex; align-items:center; gap:8px; font-size:14px; margin-bottom:20px;">
            <strong>{{ $job->employer->company_name ?: $job->employer->name }}</strong>
            <span>&bull;</span>
            <span>📍 {{ $job->location }}</span>
            <span>&bull;</span>
            <span>Posted {{ $job->created_at->diffForHumans() }}</span>
        </div>

        <div class="grid grid-3" style="margin-bottom: 24px;">
            <div style="background:#f8fafc; border:1px solid var(--border-light); border-radius:var(--radius-sm); padding:14px;">
                <div class="muted" style="font-size:11.5px; font-weight:600; text-transform:uppercase;">Compensation</div>
                <div style="font-family:var(--font-heading); font-size:15px; font-weight:700; color:var(--primary); margin-top:3px;">
                    {{ $job->formatted_salary }}
                </div>
            </div>

            <div style="background:#f8fafc; border:1px solid var(--border-light); border-radius:var(--radius-sm); padding:14px;">
                <div class="muted" style="font-size:11.5px; font-weight:600; text-transform:uppercase;">Application Deadline</div>
                <div style="font-family:var(--font-heading); font-size:15px; font-weight:700; color:var(--text-heading); margin-top:3px;">
                    {{ optional($job->deadline)->format('M d, Y') ?: 'Until Filled' }}
                </div>
            </div>

            <div style="background:#f8fafc; border:1px solid var(--border-light); border-radius:var(--radius-sm); padding:14px;">
                <div class="muted" style="font-size:11.5px; font-weight:600; text-transform:uppercase;">Applications</div>
                <div style="font-family:var(--font-heading); font-size:15px; font-weight:700; color:var(--text-heading); margin-top:3px;">
                    {{ $job->applications()->count() }} Candidates
                </div>
            </div>
        </div>

        <h2 style="font-size:18px; border-bottom:1px solid var(--border-light); padding-bottom:8px; margin-bottom:12px;">Position Summary</h2>
        <div class="job-richtext" style="line-height:1.7; margin-bottom:24px; font-size:14px;">
            {!! \Illuminate\Support\Str::markdown($job->description, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
        </div>

        <h2 style="font-size:18px; border-bottom:1px solid var(--border-light); padding-bottom:8px; margin-bottom:12px;">Key Responsibilities</h2>
        <div class="job-richtext" style="line-height:1.7; margin-bottom:24px; font-size:14px;">
            {!! \Illuminate\Support\Str::markdown($job->responsibilities ?: 'Responsibilities will be discussed during initial interview.', ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
        </div>

        <h2 style="font-size:18px; border-bottom:1px solid var(--border-light); padding-bottom:8px; margin-bottom:12px;">Qualifications & Requirements</h2>
        <div class="job-richtext" style="line-height:1.7; font-size:14px;">
            {!! \Illuminate\Support\Str::markdown($job->requirements, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
        </div>
    </section>

    <aside class="grid" style="gap: 18px;">
        <!-- Application Actions Panel -->
        <section class="card" style="padding: 22px;">
            <h3 style="margin-bottom: 12px;">Candidate Application</h3>
            @auth
                @if(auth()->user()->isJobSeeker())
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        @if(! $job->isOpenForApplications())
                            <div class="badge amber" style="justify-content:center; padding:8px;">
                                ⚠️ Applications for this job are currently closed
                            </div>
                        @elseif($hasApplied)
                            <div class="badge emerald" style="justify-content:center; padding:10px; font-size:13.5px;">
                                ✓ You have applied for this vacancy
                            </div>
                            <a class="btn secondary btn-sm" href="{{ route('seeker.applications') }}">View Application Status</a>
                        @else
                            <a class="btn" style="width:100%" href="{{ route('applications.create', $job) }}">
                                Apply with Resume →
                            </a>
                        @endif

                        <form method="post" action="{{ route('jobs.save', $job) }}" style="width:100%">
                            @csrf
                            <button class="btn secondary btn-sm" style="width:100%" type="submit">
                                {{ $isSaved ? '♥ Remove from Saved' : '♡ Save Vacancy' }}
                            </button>
                        </form>
                    </div>
                @elseif(auth()->user()->isEmployer() && auth()->id() === $job->employer_id)
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <a class="btn" href="{{ route('employer.jobs.edit', $job) }}">Edit Vacancy</a>
                        <a class="btn secondary" href="{{ route('employer.applications.index') }}">View Candidate Resumes</a>
                    </div>
                @elseif(auth()->user()->isAdmin())
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <span class="badge blue">Admin Review Mode</span>
                        <a class="btn secondary" href="{{ route('admin.jobs') }}">Moderate in Admin Console</a>
                    </div>
                @endif
            @else
                <div style="display:flex; flex-direction:column; gap:10px;">
                    <a class="btn" style="width:100%" href="{{ route('login') }}">Sign In to Apply</a>
                    <a class="btn secondary" style="width:100%" href="{{ route('register') }}">Create Free Account</a>
                </div>
            @endauth
        </section>

        <!-- Company Details Card -->
        <section class="card" style="padding: 22px;">
            <h3 style="margin-bottom: 12px;">Hiring Organization</h3>
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:14px;">
                <div class="company-logo" style="width:44px; height:44px; font-size:18px;">
                    {{ strtoupper(substr($job->employer->company_name ?: $job->employer->name, 0, 1)) }}
                </div>
                <div>
                    <h4 style="font-size:15px; font-weight:700;">{{ $job->employer->company_name ?: $job->employer->name }}</h4>
                    @if($job->employer->isVerifiedEmployer())
                        <span class="badge green" style="font-size:11px; margin-top:2px;">✓ Verified Employer</span>
                    @endif
                </div>
            </div>

            <p class="muted" style="font-size:13px; margin-bottom:14px;">
                {!! nl2br(e($job->employer->bio ?: 'Verified employer registered on JobFinder Sri Lanka.')) !!}
            </p>

            @if($job->employer->website)
                <p style="margin-bottom:10px;">
                    <a class="btn secondary btn-sm" href="{{ $job->employer->website }}" target="_blank">Company Website ↗</a>
                </p>
            @endif

            <div style="font-size:12.5px; color:var(--text-muted); border-top:1px solid var(--border-light); padding-top:10px;">
                <div><strong>Inquiries:</strong> {{ $job->employer->email }}</div>
                @if($job->employer->phone)
                    <div style="margin-top:3px;"><strong>Phone:</strong> {{ $job->employer->phone }}</div>
                @endif
            </div>
        </section>

        <!-- 1-Click Social Sharing Card -->
        <section class="card" style="padding: 22px;">
            <h3 style="margin-bottom: 8px; font-size:15px; display:flex; align-items:center; gap:6px;">
                <span>📢</span> Share this Opportunity
            </h3>
            <p class="muted" style="font-size:12.5px; margin-bottom:14px; line-height:1.4;">Know someone suitable for this vacancy? Share it directly with them.</p>

            @php
                $shareUrl = rawurlencode(route('jobs.show', $job->slug ?: $job->id));
                $shareText = rawurlencode("We are hiring: {$job->title} at " . ($job->employer->company_name ?: $job->employer->name) . " ({$job->location}). View details & apply: ");
            @endphp

            <div style="display:flex; flex-direction:column; gap:8px;">
                <a class="btn secondary btn-sm" style="background:#25D366; color:#ffffff; border-color:#25D366; justify-content:center; gap:8px; font-weight:600;" href="https://api.whatsapp.com/send?text={{ $shareText }}{{ $shareUrl }}" target="_blank">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.842-.981z"/></svg>
                    <span>Share via WhatsApp</span>
                </a>

                <a class="btn secondary btn-sm" style="background:#0A66C2; color:#ffffff; border-color:#0A66C2; justify-content:center; gap:8px; font-weight:600;" href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                    <span>Share on LinkedIn</span>
                </a>

                <button type="button" class="btn secondary btn-sm" id="copyShareBtn" style="justify-content:center; gap:6px;" onclick="copyJobLink('{{ route('jobs.show', $job->slug ?: $job->id) }}')">
                    <span>📋</span> <span id="copyShareText">Copy Link</span>
                </button>
            </div>
        </section>

        <!-- Report Job Safe Flagging Link -->
        <div style="text-align: center; margin-top: 4px;">
            <button type="button" class="btn ghost btn-sm" onclick="document.getElementById('reportJobModal').style.display='flex'" style="font-size:12px; color:var(--text-muted); gap:4px; margin: 0 auto;">
                <span>🚩</span> Report this listing (Scam / Misleading)
            </button>
        </div>
    </aside>
</div>

<!-- Report Job Modal -->
<div id="reportJobModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.65); z-index:9999; align-items:center; justify-content:center; padding:16px;">
    <div class="card" style="max-width:480px; width:100%; padding:26px; box-shadow:var(--shadow-xl); border:1px solid var(--border-medium); margin: auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <h3 style="font-size:17px; margin:0; display:flex; align-items:center; gap:6px;">
                <span>🛡️</span> Report Vacancy Listing
            </h3>
            <button type="button" class="btn ghost btn-sm" onclick="document.getElementById('reportJobModal').style.display='none'" style="padding:2px 8px; font-size:14px;">✕</button>
        </div>
        <p class="muted" style="font-size:13px; margin-bottom:16px;">If this listing asks for money, promotes fraud, or violates safety standards, let our moderation team know.</p>

        <form class="form" method="post" action="{{ route('jobs.report', $job) }}">
            @csrf
            <label>
                <span>Reason for Report <span style="color:#ef4444">*</span></span>
                <select name="reason" required>
                    <option value="fraud_or_scam">Fraud / Fake Job Scam</option>
                    <option value="asks_for_money">Employer asking for payments or deposits</option>
                    <option value="misleading_salary">Misleading salary / fake description</option>
                    <option value="inappropriate_content">Offensive or discriminatory content</option>
                    <option value="expired_or_closed">Role already closed / filled</option>
                    <option value="other">Other reason</option>
                </select>
            </label>

            <label>
                <span>Details / Explanation <span style="color:#ef4444">*</span></span>
                <textarea name="details" placeholder="Describe why this job post is suspicious..." required style="min-height:90px;"></textarea>
            </label>

            <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:14px;">
                <button type="button" class="btn secondary btn-sm" onclick="document.getElementById('reportJobModal').style.display='none'">Cancel</button>
                <button class="btn btn-sm" style="background:#ef4444; border-color:#ef4444;" type="submit">Submit Report</button>
            </div>
        </form>
    </div>
</div>

<script>
function copyJobLink(url) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => {
            const text = document.getElementById('copyShareText');
            text.textContent = 'Copied to Clipboard! ✓';
            setTimeout(() => { text.textContent = 'Copy Link'; }, 3000);
        });
    }
}
</script>
@endsection
