<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'JobFinder')</title>
    <style>
        :root {
            --ink:#111315; --panel:#f6f7f9; --paper:#ffffff; --line:#e1e5eb; --text:#20242b;
            --muted:#707987; --blue:#246fe5; --blue-dark:#155ac2; --mint:#e4f7ee; --violet:#f2eef8;
            --peach:#fff1dc; --danger:#d34444; --ok:#0b8f62; --shadow:0 10px 28px rgba(17,24,39,.08);
        }
        * { box-sizing:border-box; }
        body {
            margin:0; font-family:Inter, Arial, sans-serif; color:var(--text);
            background:var(--panel); min-height:100vh;
        }
        a { color:inherit; text-decoration:none; }
        .shell { width:100%; min-height:100vh; margin:0; overflow:hidden; background:var(--panel); }
        .topbar { background:var(--ink); color:#fff; }
        .nav { min-height:70px; display:flex; align-items:center; gap:24px; padding:0 max(34px, calc((100vw - 1240px) / 2 + 34px)); }
        .brand { margin-right:auto; display:flex; align-items:center; gap:10px; font-size:23px; font-weight:900; letter-spacing:0; color:#fff; }
        .brand-mark {
            width:38px; height:28px; border-radius:12px; display:inline-grid; place-items:center;
            background:linear-gradient(135deg,#2587ff,#63d9ff); color:#fff; font-size:15px; font-weight:900;
            box-shadow:0 8px 22px rgba(37,135,255,.34);
        }
        .nav a, .nav button { color:#d9e0ec; font:inherit; font-size:14px; border:0; background:none; cursor:pointer; }
        .nav a.active, .nav a:hover, .nav button:hover { color:#fff; }
        .profile-chip { display:flex; align-items:center; gap:9px; padding:6px 8px; border:1px solid rgba(255,255,255,.12); border-radius:999px; }
        .avatar { width:30px; height:30px; border-radius:50%; background:#fff; color:var(--blue); display:inline-grid; place-items:center; font-weight:800; }
        .hero { background:var(--ink); color:#fff; padding:10px max(34px, calc((100vw - 1240px) / 2 + 34px)) 34px; position:relative; }
        .hero h1 { margin:18px 0 22px; font-size:34px; line-height:1.08; letter-spacing:0; }
        .spark { color:#fff; font-size:28px; margin-left:8px; }
        .hero-search { display:grid; grid-template-columns:1fr 1fr auto; gap:0; background:#fff; border:6px solid rgba(255,255,255,.08); border-radius:999px; padding:6px; max-width:100%; }
        .hero-search input, .hero-search select { border:0; min-height:48px; padding:0 18px; background:#fff; font:inherit; outline:none; }
        .hero-search input:first-child { border-radius:999px 0 0 999px; border-right:1px solid var(--line); }
        .hero-search .btn { border-radius:999px; min-width:124px; }
        .wrap { max-width:1240px; margin:0 auto; padding:30px 34px 42px; }
        h1 { font-size:32px; margin:0 0 16px; line-height:1.1; letter-spacing:0; }
        h2 { font-size:24px; margin:0 0 14px; letter-spacing:0; }
        h3 { margin:0 0 8px; letter-spacing:0; }
        p { line-height:1.6; }
        .muted { color:var(--muted); }
        .grid { display:grid; gap:18px; }
        .grid-4 { grid-template-columns:repeat(4,minmax(0,1fr)); }
        .grid-3 { grid-template-columns:repeat(3,minmax(0,1fr)); }
        .grid-2 { grid-template-columns:repeat(2,minmax(0,1fr)); }
        .browse-layout { display:grid; grid-template-columns:240px 1fr; gap:22px; align-items:start; }
        .card, .side-panel { background:var(--paper); border:1px solid var(--line); border-radius:10px; padding:18px; box-shadow:0 1px 0 rgba(17,24,39,.03); }
        .job-card { min-height:238px; display:flex; flex-direction:column; gap:11px; }
        .job-card-head { display:flex; justify-content:space-between; gap:12px; align-items:flex-start; }
        .company-logo { width:38px; height:38px; border-radius:9px; display:grid; place-items:center; background:#eef3fb; color:var(--blue); font-weight:900; }
        .btn { display:inline-flex; align-items:center; justify-content:center; min-height:42px; padding:0 18px; border-radius:999px; border:1px solid var(--blue); background:var(--blue); color:#fff; font-weight:800; cursor:pointer; }
        .btn:hover { background:var(--blue-dark); }
        .btn.secondary { background:#fff; color:var(--text); border-color:#dce5ef; }
        .btn.danger { background:var(--danger); border-color:var(--danger); color:#fff; }
        .btn.ghost { background:transparent; border-color:transparent; color:var(--blue); padding:0; min-height:auto; }
        .form { display:grid; gap:14px; }
        label { display:grid; gap:7px; font-weight:800; font-size:14px; }
        input, select, textarea { width:100%; border:1px solid var(--line); border-radius:10px; min-height:44px; padding:10px 12px; font:inherit; background:#fff; }
        textarea { min-height:130px; }
        table { width:100%; border-collapse:separate; border-spacing:0; background:#fff; }
        th, td { padding:13px 12px; border-bottom:1px solid var(--line); text-align:left; vertical-align:top; }
        th { color:var(--muted); font-size:13px; }
        .badge { display:inline-block; padding:5px 9px; border-radius:999px; background:var(--violet); color:#6d43a5; font-size:12px; font-weight:800; }
        .badge.green { background:var(--mint); color:var(--ok); }
        .badge.peach { background:var(--peach); color:#a76412; }
        .badge.blue { background:#e8f2ff; color:var(--blue-dark); }
        .alert { padding:12px 14px; border-radius:10px; margin-bottom:16px; background:#e8f8ef; color:#11623e; }
        .errors { background:#fff2f2; color:#842029; padding:12px 14px; border-radius:10px; margin-bottom:16px; }
        .actions { display:flex; flex-wrap:wrap; gap:10px; align-items:center; }
        .section-head { display:flex; gap:16px; align-items:center; justify-content:space-between; margin-bottom:14px; }
        .checkbox-row { display:flex; align-items:center; justify-content:space-between; gap:8px; padding:7px 0; color:var(--muted); font-size:14px; }
        .checkbox-row input { width:auto; min-height:auto; }
        .price { margin-top:auto; font-weight:900; }
        .footer { border-top:1px solid var(--line); background:#fff; padding:24px max(34px, calc((100vw - 1240px) / 2 + 34px)); color:var(--muted); display:flex; flex-wrap:wrap; gap:18px; justify-content:space-between; }
        .pagination { margin-top:18px; }
        @media (max-width:900px){ .shell{margin:0;border-radius:0}.hero-search,.browse-layout,.grid-2,.grid-3,.grid-4{grid-template-columns:1fr}.nav{flex-wrap:wrap;padding:16px 22px}.hero,.wrap,.footer{padding-left:22px;padding-right:22px}.hero-search input:first-child{border-right:0;border-radius:999px}.hero h1{font-size:30px} }
    </style>
</head>
<body>
<div class="shell">
    <header class="topbar">
        <nav class="nav">
            <a class="brand" href="{{ route('home') }}"><span class="brand-mark">JF</span><span>JobFinder</span></a>
            <a href="{{ route('jobs.index') }}">Find Jobs</a>
            <a href="{{ route('companies') }}">Find Talent</a>
            @auth
                @if(auth()->user()->isEmployer())
                    <a href="{{ auth()->user()->isVerifiedEmployer() ? route('employer.jobs.create') : route('dashboard') }}">Upload Job</a>
                @endif
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.users') }}">Users</a>
                    <a href="{{ route('admin.jobs') }}">Jobs</a>
                    <a href="{{ route('admin.messages') }}">Messages</a>
                @endif
            @else
                <a href="{{ route('register') }}">Upload Job</a>
            @endauth
            <a href="{{ route('about') }}">About us</a>
            <a href="{{ route('contact') }}">Contact</a>
            @auth
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a class="profile-chip" href="{{ route('profile.edit') }}"><span>{{ auth()->user()->name }}</span><span class="avatar">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span></a>
                <form method="post" action="{{ route('logout') }}">@csrf <button>Logout</button></form>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a class="btn" href="{{ route('register') }}">Register</a>
            @endauth
        </nav>
    </header>
    <main>
        @hasSection('hero')
            @yield('hero')
        @endif
        <div class="wrap">
            @if (session('status')) <div class="alert">{{ session('status') }}</div> @endif
            @if ($errors->any())
                <div class="errors"><strong>Please check:</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            @yield('content')
        </div>
    </main>
    <footer class="footer">
        <span>&copy; {{ date('Y') }} JobFinder.lk</span>
        <span class="actions">
            <a href="{{ route('privacy') }}">Privacy</a>
            <a href="{{ route('terms') }}">Terms</a>
            <a href="{{ route('career-advice') }}">Career Advice</a>
            <a href="{{ route('resume-tips') }}">Resume Tips</a>
            <a href="{{ route('interview-tips') }}">Interview Tips</a>
            <a href="{{ route('blog') }}">Blog</a>
        </span>
    </footer>
</div>
</body>
</html>
