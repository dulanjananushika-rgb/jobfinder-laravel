@extends('layouts.app')
@section('title', 'Apply for ' . $job->title)

@section('content')
<div style="max-width: 720px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('jobs.show', $job) }}" class="btn ghost btn-sm" style="padding-left:0;">← Back to Job Details</a>
    </div>

    <div class="card" style="padding: 36px;">
        <div class="actions" style="margin-bottom: 12px;">
            <span class="badge blue">{{ $job->main_category }}</span>
            <span class="badge green">{{ $job->type }}</span>
        </div>
        <h1 style="font-size: 26px; margin-bottom: 6px;">Submit Application</h1>
        <p class="muted" style="margin-bottom: 28px;">
            Applying for <strong>{{ $job->title }}</strong> at <strong>{{ $job->employer->company_name ?: $job->employer->name }}</strong>.
        </p>

        <form class="form" method="post" action="{{ route('applications.store', $job) }}" enctype="multipart/form-data">
            @csrf

            <label>
                <span>Cover Letter / Pitch <span style="color:#ef4444">*</span></span>
                <span class="muted" style="font-size: 12px; font-weight: normal;">Introduce yourself and summarize why you are a great fit for this role.</span>
                <textarea name="cover_letter" placeholder="Dear Hiring Team, I am writing to express my interest in..." required style="min-height: 150px;">{{ old('cover_letter') }}</textarea>
            </label>

            <label>
                <span>Resume / CV (PDF, DOC, DOCX up to 5MB) <span style="color:#ef4444">*</span></span>
                <div style="border: 2px dashed var(--border-color); border-radius: 12px; padding: 24px; text-align: center; background: #f8fafc; transition: border-color var(--transition-fast);">
                    <div style="font-size: 32px; margin-bottom: 8px;">📄</div>
                    <div style="font-size: 14px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px;">Upload your latest resume</div>
                    <div class="muted" style="font-size: 12px; margin-bottom: 14px;">Your document is stored on secure private storage and protected by role-based access.</div>
                    <input type="file" name="resume" accept=".pdf,.doc,.docx" required style="border: 1px solid var(--border-color); max-width: 380px; margin: 0 auto; padding: 8px;">
                </div>
            </label>

            <div style="border-top: 1px solid var(--border-color); padding-top: 20px; margin-top: 10px; display: flex; justify-content: space-between; align-items: center;">
                <a href="{{ route('jobs.show', $job) }}" class="btn secondary">Cancel</a>
                <button class="btn" type="submit">Submit Application →</button>
            </div>
        </form>
    </div>
</div>
@endsection
