@extends('layouts.app')
@section('title', 'Application Thread #APP-' . $application->id . ' - JobFinder')

@section('content')
<div style="margin-bottom: 20px;">
    @if(auth()->user()->isEmployer())
        <a href="{{ route('employer.applications.index') }}" class="btn ghost btn-sm" style="padding-left:0;">← Back to Applications</a>
    @else
        <a href="{{ route('seeker.applications') }}" class="btn ghost btn-sm" style="padding-left:0;">← Back to My Applications</a>
    @endif
</div>

<div class="grid grid-2" style="grid-template-columns: 1.8fr 1.2fr; align-items: start;">
    <!-- Chat Messages Panel -->
    <section class="card" style="padding: 24px; display:flex; flex-direction:column; min-height: 520px;">
        <div style="border-bottom: 1px solid var(--border-light); padding-bottom: 14px; margin-bottom: 16px; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h2 style="font-size: 18px; margin-bottom: 2px;">
                    Conversation: {{ $application->jobSeeker->name }} &bull; {{ $application->job->employer->company_name ?: $application->job->employer->name }}
                </h2>
                <div class="muted" style="font-size: 13px;">Regarding role: <strong>{{ $application->job->title }}</strong></div>
            </div>
            <span class="badge {{ $application->status === 'hired' ? 'green' : ($application->status === 'interview' ? 'peach' : 'blue') }}">
                Status: {{ ucfirst($application->status) }}
            </span>
        </div>

        <!-- Messages Scrollable Thread -->
        <div id="chatMessagesThread" style="flex:1; overflow-y:auto; display:flex; flex-direction:column; gap:12px; margin-bottom: 20px; max-height: 380px; padding-right: 6px;">
            <!-- Initial Application Pitch Message -->
            <div style="background: #f8fafc; border: 1px solid var(--border-light); border-radius: var(--radius-sm); padding: 12px 16px;">
                <div style="font-size: 12px; font-weight: 600; color: var(--text-muted); margin-bottom: 4px;">
                    {{ $application->jobSeeker->name }} (Initial Application Pitch) &bull; {{ $application->created_at->format('M d, Y h:i A') }}
                </div>
                <div style="font-size: 13.5px; line-height: 1.5; color: var(--text-body);">
                    {{ $application->cover_letter }}
                </div>
            </div>

            @forelse($application->messages as $msg)
                @php $isMe = $msg->sender_id === auth()->id(); @endphp
                <div style="align-self: {{ $isMe ? 'flex-end' : 'flex-start' }}; max-width: 80%;">
                    <div style="font-size: 11px; font-weight: 600; color: var(--text-muted); margin-bottom: 2px; text-align: {{ $isMe ? 'right' : 'left' }};">
                        {{ $isMe ? 'You' : $msg->sender->name }} &bull; {{ $msg->created_at->format('M d, h:i A') }}
                    </div>
                    <div style="background: {{ $isMe ? '#ecfdf5' : '#ffffff' }}; border: 1px solid {{ $isMe ? '#a7f3d0' : 'var(--border-light)' }}; border-radius: var(--radius-sm); padding: 10px 14px; font-size: 13.5px; color: var(--text-heading); line-height: 1.45;">
                        {{ $msg->message }}
                    </div>
                </div>
            @empty
                <div class="muted" style="text-align: center; padding: 24px 0; font-size: 13px;">
                    No follow-up messages yet. Start the conversation below!
                </div>
            @endforelse
        </div>

        <!-- Send Message Form -->
        <form class="form" method="post" action="{{ route('applications.messages.store', $application) }}" style="border-top: 1px solid var(--border-light); padding-top: 14px;">
            @csrf
            <div style="display:flex; gap: 8px;">
                <input name="message" placeholder="Type your message to candidate or employer..." required style="flex:1;">
                <button class="btn" type="submit">Send</button>
            </div>
        </form>
    </section>

    <!-- Sidebar: Application Overview & Interview Scheduler -->
    <aside class="grid" style="gap: 18px;">
        <!-- Application Summary Card -->
        <div class="card" style="padding: 20px;">
            <h3 style="margin-bottom: 12px;">Candidate Overview</h3>
            <div style="font-size: 13.5px; display:flex; flex-direction:column; gap: 8px;">
                <div><strong>Candidate:</strong> {{ $application->jobSeeker->name }}</div>
                <div><strong>Email:</strong> {{ $application->jobSeeker->email }}</div>
                <div><strong>Phone:</strong> {{ $application->jobSeeker->phone ?: 'Not provided' }}</div>
                @if($application->ai_match_score)
                    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: var(--radius-sm); padding: 8px 10px; margin: 4px 0;">
                        <strong style="color:#047857;">🤖 AI Match Score: {{ $application->ai_match_score }}%</strong>
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">{{ $application->ai_summary }}</div>
                    </div>
                @endif
                <div style="margin-top: 8px;">
                    <a class="btn secondary btn-sm" href="{{ route('applications.resume', $application) }}" target="_blank">
                        📄 Download Verified Resume
                    </a>
                </div>
            </div>
        </div>

        <!-- Interview Details / Scheduler -->
        <div class="card" style="padding: 20px;">
            <h3 style="margin-bottom: 12px;">Interview Status</h3>
            @if($application->interview)
                <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: var(--radius-sm); padding: 12px; margin-bottom: 14px;">
                    <div style="font-weight: 700; color: #b45309;">📅 Interview Scheduled</div>
                    <div style="font-size: 13.5px; margin-top: 4px;">
                        <strong>Date & Time:</strong> {{ $application->interview->scheduled_at->format('M d, Y @ h:i A') }}
                    </div>
                    <div style="font-size: 13px; margin-top: 2px;">
                        <strong>Type:</strong> {{ ucfirst($application->interview->location_type) }}
                    </div>
                    @if($application->interview->meeting_link)
                        <div style="margin-top: 8px;">
                            <a class="btn btn-sm" href="{{ $application->interview->meeting_link }}" target="_blank">
                                🔗 Join Video Meeting
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <p class="muted" style="font-size: 13px; margin-bottom: 14px;">No formal interview has been scheduled yet.</p>
            @endif

            @if(auth()->user()->isEmployer() && $application->status !== 'withdrawn')
                <h4 style="font-size: 14px; margin-bottom: 8px; border-top: 1px solid var(--border-light); padding-top: 12px;">
                    {{ $application->interview ? 'Reschedule Interview' : 'Schedule Interview' }}
                </h4>
                <form class="form" method="post" action="{{ route('employer.applications.interview', $application) }}">
                    @csrf
                    <label>
                        <span>Date & Time <span style="color:#ef4444">*</span></span>
                        <input type="datetime-local" name="scheduled_at" required min="{{ now()->format('Y-m-d\TH:i') }}">
                    </label>

                    <label>
                        <span>Interview Medium</span>
                        <select name="location_type" required>
                            <option value="remote">Remote (Video Meeting)</option>
                            <option value="in_person">In-Person (Office)</option>
                        </select>
                    </label>

                    <label>
                        <span>Meeting Link (Google Meet / Zoom)</span>
                        <input type="url" name="meeting_link" placeholder="https://meet.google.com/xyz">
                    </label>

                    <label>
                        <span>Interview Preparation Notes</span>
                        <textarea name="notes" placeholder="Topics, interviewers, preparation instructions..." style="min-height: 70px;"></textarea>
                    </label>

                    <button class="btn btn-sm" type="submit" style="width:100%;">Dispatch Interview Invite</button>
                </form>
            @endif
        </div>
    </aside>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const thread = document.getElementById('chatMessagesThread');
    if (thread) {
        thread.scrollTop = thread.scrollHeight;
        
        // Auto-refresh chat thread every 4 seconds if tab is active
        setInterval(function() {
            if (!document.hidden) {
                fetch(window.location.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newThread = doc.getElementById('chatMessagesThread');
                    if (newThread && newThread.innerHTML !== thread.innerHTML) {
                        const wasAtBottom = (thread.scrollHeight - thread.scrollTop) <= (thread.clientHeight + 50);
                        thread.innerHTML = newThread.innerHTML;
                        if (wasAtBottom) {
                            thread.scrollTop = thread.scrollHeight;
                        }
                    }
                }).catch(() => {});
            }
        }, 4000);
    }
});
</script>
@endsection
