<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'JobFinder - Sri Lanka Career Network')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="shell">
    <header class="topbar">
        <div class="nav-container">
            <a class="brand" href="{{ route('home') }}">
                <span class="brand-icon-box">💼</span>
                <span>Job<span class="brand-text-highlight">Finder</span></span>
            </a>

            <nav class="nav-links">
                <a class="nav-link {{ request()->routeIs('jobs.*') ? 'active' : '' }}" href="{{ route('jobs.index') }}">Find Jobs</a>
                <a class="nav-link {{ request()->routeIs('companies') ? 'active' : '' }}" href="{{ route('companies') }}">Browse Employers</a>
                @auth
                    @if(auth()->user()->isEmployer())
                        <a class="nav-link {{ request()->routeIs('employer.jobs.*') ? 'active' : '' }}" href="{{ auth()->user()->isVerifiedEmployer() ? route('employer.jobs.create') : route('dashboard') }}">Post Vacancy</a>
                        <a class="nav-link {{ request()->routeIs('employer.applications.*') ? 'active' : '' }}" href="{{ route('employer.applications.index') }}">Review Applicants</a>
                    @endif
                    @if(auth()->user()->isJobSeeker())
                        <a class="nav-link {{ request()->routeIs('seeker.applications') ? 'active' : '' }}" href="{{ route('seeker.applications') }}">My Applications</a>
                        <a class="nav-link {{ request()->routeIs('seeker.saved-jobs') ? 'active' : '' }}" href="{{ route('seeker.saved-jobs') }}">Saved Vacancies</a>
                    @endif
                    @if(auth()->user()->isAdmin())
                        <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users') }}">Users</a>
                        <a class="nav-link {{ request()->routeIs('admin.jobs*') ? 'active' : '' }}" href="{{ route('admin.jobs') }}">Moderate Jobs</a>
                        <a class="nav-link {{ request()->routeIs('admin.applications*') ? 'active' : '' }}" href="{{ route('admin.applications') }}">Applications</a>
                        <a class="nav-link {{ request()->routeIs('admin.messages*') ? 'active' : '' }}" href="{{ route('admin.messages') }}">Inquiries</a>
                    @endif
                @else
                    <a class="nav-link" href="{{ route('register') }}">Post a Vacancy</a>
                @endauth
                <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
                <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
            </nav>

            <div class="nav-actions">
                @auth
                    <!-- In-App Notification Center -->
                    <div class="notif-wrapper">
                        <button type="button" class="notif-bell-btn" id="notifBellBtn" title="View Notifications">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                            @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                            @if($unreadCount > 0)
                                <span class="notif-badge-count">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                            @endif
                        </button>
                        <div class="notif-dropdown" id="notifDropdown">
                            <div class="notif-header">
                                <h4>Notifications</h4>
                                @if($unreadCount > 0)
                                    <form method="post" action="{{ route('notifications.markRead') }}" style="display:inline">
                                        @csrf
                                        <button type="submit" class="btn ghost btn-sm" style="font-size:11.5px;padding:2px 6px;">Mark all read</button>
                                    </form>
                                @endif
                            </div>
                            <div class="notif-list">
                                @forelse(auth()->user()->notifications()->latest()->limit(8)->get() as $notification)
                                    <a class="notif-item {{ $notification->read_at ? '' : 'unread' }}" href="{{ $notification->data['url'] ?? route('dashboard') }}">
                                        <div class="notif-item-msg">{{ $notification->data['message'] ?? 'Notification received' }}</div>
                                        <div class="notif-item-time">{{ $notification->created_at->diffForHumans() }}</div>
                                    </a>
                                @empty
                                    <div style="padding: 24px; text-align: center; color: var(--text-muted); font-size: 13px;">
                                        No notifications yet.
                                    </div>
                                @endforelse
                            </div>
                            <div style="padding: 10px; text-align: center; border-top: 1px solid var(--border-light); background: #f8fafc;">
                                <a href="{{ route('notifications.index') }}" style="font-size: 12.5px; font-weight: 600; color: var(--primary); text-decoration: none;">
                                    View All Notifications &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                    <a class="profile-chip" href="{{ route('profile.edit') }}">
                        <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        <span>{{ auth()->user()->name }}</span>
                    </a>
                    <a class="btn secondary btn-sm" href="{{ route('dashboard') }}">Dashboard</a>
                    <form method="post" action="{{ route('logout') }}" style="display:inline">
                        @csrf
                        <button class="btn ghost btn-sm" style="color:var(--text-muted)">Logout</button>
                    </form>
                @else
                    <a class="btn ghost btn-sm" href="{{ route('login') }}">Sign In</a>
                    <a class="btn btn-sm" href="{{ route('register') }}">Create Account</a>
                @endauth

                <!-- Mobile Menu Button -->
                <button type="button" class="mobile-toggle-btn" id="mobileMenuBtn" aria-label="Toggle menu">☰</button>
            </div>
        </div>
    </header>

    <!-- Mobile Drawer & Backdrop -->
    <div class="mobile-backdrop" id="mobileBackdrop"></div>
    <div class="mobile-drawer" id="mobileDrawer">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; padding-bottom:12px; border-bottom:1px solid var(--border-light);">
            <div class="brand">
                <span class="brand-icon-box">💼</span>
                <span>Job<span class="brand-text-highlight">Finder</span></span>
            </div>
            <button type="button" id="closeDrawerBtn" style="background:none; border:none; color:var(--text-heading); font-size:20px; cursor:pointer;">✕</button>
        </div>

        <nav style="display:flex; flex-direction:column; gap:6px;">
            <a class="nav-link" href="{{ route('home') }}">Home</a>
            <a class="nav-link" href="{{ route('jobs.index') }}">Browse Jobs</a>
            <a class="nav-link" href="{{ route('companies') }}">Browse Employers</a>
            @auth
                <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                @if(auth()->user()->isEmployer())
                    <a class="nav-link" href="{{ route('employer.jobs.index') }}">My Job Postings</a>
                    <a class="nav-link" href="{{ route('employer.applications.index') }}">Candidate Resumes</a>
                @endif
                @if(auth()->user()->isJobSeeker())
                    <a class="nav-link" href="{{ route('seeker.applications') }}">My Applications</a>
                    <a class="nav-link" href="{{ route('seeker.saved-jobs') }}">Saved Vacancies</a>
                @endif
                @if(auth()->user()->isAdmin())
                    <a class="nav-link" href="{{ route('admin.users') }}">User Management</a>
                    <a class="nav-link" href="{{ route('admin.jobs') }}">Job Moderation</a>
                    <a class="nav-link" href="{{ route('admin.applications') }}">All Applications</a>
                @endif
                <a class="nav-link" href="{{ route('profile.edit') }}">Profile Settings</a>
                <form method="post" action="{{ route('logout') }}" style="margin-top:10px;">
                    @csrf
                    <button class="btn danger btn-sm" style="width:100%">Sign Out</button>
                </form>
            @else
                <a class="btn btn-sm" style="margin-top:10px; width:100%;" href="{{ route('login') }}">Sign In</a>
                <a class="btn secondary btn-sm" style="width:100%;" href="{{ route('register') }}">Create Account</a>
            @endauth
        </nav>
    </div>

    <main>
        @hasSection('hero')
            @yield('hero')
        @endif

        <div class="wrap">
            @if (session('status'))
                <div class="alert">
                    <span>✓</span>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="errors">
                    <strong>Please review the following:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="footer">
        <div class="footer-inner">
            <div>
                <strong>JobFinder.lk</strong> &copy; {{ date('Y') }} &middot; Sri Lanka's Verified Employment Portal
            </div>
            <div class="footer-links">
                <a href="{{ route('privacy') }}">Privacy Policy</a>
                <a href="{{ route('terms') }}">Terms of Service</a>
                <a href="{{ route('career-advice') }}">Career Advice</a>
                <a href="{{ route('resume-tips') }}">Resume Tips</a>
                <a href="{{ route('interview-tips') }}">Interview Tips</a>
                <a href="{{ route('blog') }}">Blog</a>
                <a href="{{ route('contact') }}">Contact Us</a>
            </div>
        </div>
    </footer>
</div>

<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
