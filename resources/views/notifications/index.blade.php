@extends('layouts.app')
@section('title', 'Notification Center - JobFinder')

@section('content')
<div style="max-width: 860px; margin: 24px auto;">
    <div class="section-head" style="margin-bottom: 20px;">
        <div>
            <h1>Notification Center</h1>
            <p class="muted" style="margin:0">Stay updated on your applications, interviews, and listing approvals.</p>
        </div>
        <div class="actions">
            @if($unreadCount > 0)
                <form method="post" action="{{ route('notifications.markRead') }}">
                    @csrf
                    <button class="btn secondary btn-sm" type="submit">Mark All as Read ({{ $unreadCount }})</button>
                </form>
            @endif
        </div>
    </div>

    <!-- Filter Tabs -->
    <div style="display:flex; gap:8px; margin-bottom: 16px; border-bottom: 1px solid var(--border-light); padding-bottom: 10px;">
        <a href="{{ route('notifications.index') }}" class="btn {{ !request('filter') ? '' : 'ghost' }} btn-sm" style="border-radius:20px;">
            All ({{ $totalCount }})
        </a>
        <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="btn {{ request('filter') === 'unread' ? '' : 'ghost' }} btn-sm" style="border-radius:20px;">
            Unread ({{ $unreadCount }})
        </a>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        @forelse($notifications as $notification)
            @php
                $isUnread = is_null($notification->read_at);
                $type = $notification->data['type'] ?? 'general';
                $icon = match($type) {
                    'interview_scheduled' => '📅',
                    'status_updated' => '⚡',
                    'job_approved' => '✅',
                    'job_expired' => '⏳',
                    'application_received' => '📨',
                    default => '🔔',
                };
            @endphp
            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; padding:16px 20px; border-bottom:1px solid var(--border-light); background: {{ $isUnread ? '#f0fdf4' : 'transparent' }};">
                <div style="display:flex; align-items:flex-start; gap:14px; flex:1;">
                    <div style="width:36px; height:36px; border-radius:50%; background:{{ $isUnread ? 'var(--primary-subtle)' : '#f1f5f9' }}; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">
                        {{ $icon }}
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:14px; font-weight:{{ $isUnread ? '600' : '500' }}; color:var(--text-heading); margin-bottom:4px;">
                            {{ $notification->data['message'] ?? 'Notification alert' }}
                        </div>
                        <div class="muted" style="font-size:12px;">
                            {{ $notification->created_at->diffForHumans() }}
                            @if($isUnread)
                                <span class="badge green" style="font-size:10px; margin-left:6px; padding:1px 6px;">New</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div style="display:flex; align-items:center; gap:8px;">
                    @if(!empty($notification->data['url']))
                        <form method="post" action="{{ route('notifications.read', $notification->id) }}">
                            @csrf
                            <button class="btn secondary btn-sm" type="submit" style="padding:4px 10px; font-size:12px;">
                                View Details &rarr;
                            </button>
                        </form>
                    @endif

                    <form method="post" action="{{ route('notifications.destroy', $notification->id) }}" onsubmit="return confirm('Delete this notification?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn ghost btn-sm" type="submit" style="padding:4px 8px; font-size:12px; color:var(--muted);" title="Delete notification">
                            ✕
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div style="text-align:center; padding:50px 20px;">
                <div style="font-size:36px; margin-bottom:10px;">🎉</div>
                <h3 style="font-size:17px; margin-bottom:4px;">All caught up!</h3>
                <p class="muted" style="font-size:13.5px; margin:0;">You have no notifications in this view.</p>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 20px;">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
