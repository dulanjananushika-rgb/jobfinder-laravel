@extends('layouts.app')
@section('title', $job->exists ? 'Edit Vacancy - Employer Portal' : 'Post New Vacancy - Employer Portal')

@section('content')
<div style="max-width: 860px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('employer.jobs.index') }}" class="btn ghost btn-sm" style="padding-left:0;">← Back to My Job Postings</a>
    </div>

    <div class="card" style="padding: 36px;">
        <div class="actions" style="margin-bottom: 12px;">
            <span class="badge blue">Employer Portal</span>
            @if($job->exists)
                <span class="badge peach">Status: Pending Re-Approval</span>
            @endif
        </div>

        <h1 style="font-size: 26px; margin-bottom: 6px;">{{ $job->exists ? 'Edit Vacancy Listing' : 'Post a New Vacancy' }}</h1>
        <p class="muted" style="margin-bottom: 28px;">
            Provide complete role criteria and requirements. Once submitted, our admin team reviews and publishes the listing.
        </p>

        <form class="form" method="post" action="{{ $job->exists ? route('employer.jobs.update', $job) : route('employer.jobs.store') }}">
            @csrf
            @if($job->exists)
                @method('PUT')
            @endif

            <div class="grid grid-2">
                <label style="grid-column: 1 / -1;">
                    <span>Job Title <span style="color:#ef4444">*</span></span>
                    <input name="title" value="{{ old('title', $job->title) }}" placeholder="e.g. Senior Laravel Developer, Assistant Accountant" required>
                </label>

                <label>
                    <span>Industry / Category <span style="color:#ef4444">*</span></span>
                    <select name="main_category" id="mainCategorySelect" required onchange="toggleOtherCategory(this.value)">
                        <option value="">Select an industry</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" @selected(old('main_category', $job->main_category) === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                </label>

                <label id="subCategoryWrapper">
                    <span id="subCategoryLabel">{{ old('main_category', $job->main_category) === 'Other' ? 'Specify Custom Category Name *' : 'Sub Category / Specialization' }}</span>
                    <input name="sub_category" id="subCategoryInput" value="{{ old('sub_category', $job->sub_category) }}" placeholder="{{ old('main_category', $job->main_category) === 'Other' ? 'e.g. Drone Pilot, Marine Biology, Event Host' : 'e.g. Backend Development, Auditing' }}">
                </label>

                <label>
                    <span>District / Location <span style="color:#ef4444">*</span></span>
                    <select name="location" required>
                        <option value="">Select a Sri Lankan district</option>
                        @foreach($locations as $location)
                            <option value="{{ $location }}" @selected(old('location', $job->location) === $location)>{{ $location }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span>Employment Type <span style="color:#ef4444">*</span></span>
                    <input name="type" value="{{ old('type', $job->type ?: 'Full-time') }}" placeholder="Full-time, Part-time, Contract, Remote" required>
                </label>

                <label>
                    <span>Salary / Compensation</span>
                    <input name="salary" value="{{ old('salary', $job->salary) }}" placeholder="e.g. Rs 150,000 - 250,000 / Negotiable">
                </label>

                <label>
                    <span>Application Deadline</span>
                    <input type="date" name="deadline" value="{{ old('deadline', optional($job->deadline)->format('Y-m-d')) }}">
                </label>
            </div>

            <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:12px 16px; font-size:13px; color:#1d4ed8; margin: 8px 0;">
                ℹ️ <strong>Security note:</strong> For platform integrity, new job listings and edits to existing jobs are reviewed by administrators before becoming visible to the public.
            </div>

            <label>
                <span>Role Description <span style="color:#ef4444">*</span></span>
                <textarea name="description" placeholder="Describe the role, day-to-day focus, and why candidates should join..." required style="min-height: 140px;">{{ old('description', $job->description) }}</textarea>
            </label>

            <label>
                <span>Requirements & Skills <span style="color:#ef4444">*</span></span>
                <textarea name="requirements" placeholder="Qualifications, education, experience, and tools required..." required style="min-height: 120px;">{{ old('requirements', $job->requirements) }}</textarea>
            </label>

            <label>
                <span>Core Responsibilities</span>
                <textarea name="responsibilities" placeholder="Key responsibilities and expectations..." style="min-height: 120px;">{{ old('responsibilities', $job->responsibilities) }}</textarea>
            </label>

            <div style="border-top:1px solid var(--border-color); padding-top:20px; margin-top:12px; display:flex; justify-content:space-between; align-items:center;">
                <a href="{{ route('employer.jobs.index') }}" class="btn secondary">Cancel</a>
                <button class="btn" type="submit">{{ $job->exists ? 'Update Vacancy →' : 'Publish Vacancy →' }}</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleOtherCategory(val) {
    const label = document.getElementById('subCategoryLabel');
    const input = document.getElementById('subCategoryInput');
    if (!label || !input) return;

    if (val === 'Other') {
        label.innerHTML = 'Specify Custom Category Name <span style="color:#ef4444">*</span>';
        input.placeholder = 'e.g. Drone Pilot, Marine Biology, Event Host';
        input.focus();
    } else {
        label.textContent = 'Sub Category / Specialization';
        input.placeholder = 'e.g. Backend Development, Auditing';
    }
}
</script>
@endsection
