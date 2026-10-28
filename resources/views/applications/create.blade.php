@extends('layouts.app')
@section('title', 'Apply for ' . $job->title)

@section('content')
<div style="max-width: 720px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('jobs.show', $job) }}" class="btn ghost btn-sm" style="padding-left:0;">← Back to Job Details</a>
    </div>

    <div class="card" style="padding: 32px;">
        <div class="actions" style="margin-bottom: 12px;">
            <span class="badge emerald">{{ $job->main_category }}</span>
            <span class="badge blue">{{ $job->type }}</span>
        </div>
        <h1 style="font-size: 24px; margin-bottom: 4px;">Submit Candidate Application</h1>
        <p class="muted" style="margin-bottom: 24px;">
            Applying for <strong>{{ $job->title }}</strong> at <strong>{{ $job->employer->company_name ?: $job->employer->name }}</strong>.
        </p>

        <form class="form" method="post" action="{{ route('applications.store', $job) }}" enctype="multipart/form-data">
            @csrf

            <!-- Application Mode Toggle -->
            <div style="background:#f8fafc; border:1px solid var(--border-light); border-radius:var(--radius-sm); padding:14px; margin-bottom:12px;">
                <strong style="font-size:13.5px; display:block; margin-bottom:8px;">Choose Application Method:</strong>
                <div style="display:flex; gap:16px;">
                    <label style="display:flex; align-items:center; gap:6px; font-weight:normal; cursor:pointer;">
                        <input type="radio" name="apply_type" value="upload" checked onclick="document.getElementById('uploadBox').style.display='block'; document.getElementById('profileBox').style.display='none';">
                        <span>Upload Custom Resume (PDF/DOCX)</span>
                    </label>
                    <label style="display:flex; align-items:center; gap:6px; font-weight:normal; cursor:pointer;">
                        <input type="radio" name="apply_type" value="profile" onclick="document.getElementById('uploadBox').style.display='none'; document.getElementById('profileBox').style.display='block';">
                        <span>⚡ 1-Click Apply using JobFinder Profile</span>
                    </label>
                </div>
            </div>

            <label>
                <span>Cover Letter / Pitch <span style="color:#ef4444">*</span></span>
                <span class="muted" style="font-size: 12px; font-weight: normal;">Summarize why your background aligns with this position (AI matching engine analyzes this).</span>
                <textarea name="cover_letter" placeholder="Dear Hiring Team, I am enthusiastic about applying for..." required style="min-height: 140px;">{{ old('cover_letter') }}</textarea>
            </label>

            <!-- Custom File Upload Container -->
            <div id="uploadBox">
                <label>
                    <span>Resume / CV Document (PDF, DOC, DOCX up to 5MB)</span>
                    <div style="border: 2px dashed var(--border-light); border-radius: var(--radius-sm); padding: 20px; text-align: center; background: #f8fafc;">
                        <div style="font-size: 26px; margin-bottom: 6px;">📄</div>
                        <div style="font-size: 13.5px; font-weight: 600; color: var(--text-heading); margin-bottom: 2px;">Upload your CV document</div>
                        <div class="muted" style="font-size: 11.5px; margin-bottom: 12px;">Stored privately and shielded by role-based access.</div>
                        <input type="file" name="resume" accept=".pdf,.doc,.docx" style="max-width: 360px; margin: 0 auto; padding: 6px;">
                    </div>
                </label>
            </div>

            <!-- Profile Summary Container -->
            <div id="profileBox" style="display:none; background:#ecfdf5; border:1px solid #a7f3d0; border-radius:var(--radius-sm); padding:16px;">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                    <span style="font-size:18px;">⚡</span>
                    <strong style="color:#047857;">Instant Profile Snapshot</strong>
                </div>
                <p class="muted" style="font-size:12.5px; margin:0 0 10px;">
                    Your registered name, headline, skills, and work history will be bundled as your verified digital CV.
                </p>
                <a href="{{ route('profile.edit') }}" class="btn ghost btn-sm" style="font-size:12px; padding:0;" target="_blank">
                    ✏️ Update Profile Details
                </a>
            </div>

            <div style="border-top: 1px solid var(--border-light); padding-top: 18px; margin-top: 10px; display: flex; justify-content: space-between; align-items: center;">
                <a href="{{ route('jobs.show', $job) }}" class="btn secondary">Cancel</a>
                <button class="btn" type="submit">Submit Application →</button>
            </div>
        </form>
    </div>
</div>
@endsection
