<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Simple Bank')</title>
    <style>
        body { font-family: system-ui, sans-serif; background:#f3f4f6; margin:0; color:#111827; }
        nav { background:#1e3a8a; color:#fff; padding:12px 24px; display:flex; gap:18px; align-items:center; }
        nav a { color:#fff; text-decoration:none; }
        nav .spacer { flex:1; }
        nav button { background:#ef4444; color:#fff; border:0; padding:6px 12px; border-radius:6px; cursor:pointer; }
        main { max-width:900px; margin:24px auto; padding:0 16px; }
        .card { background:#fff; border-radius:10px; padding:20px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,.1); }
        .stats { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }
        .stat { background:#eff6ff; border-radius:8px; padding:16px; text-align:center; }
        .stat .num { font-size:1.6rem; font-weight:700; color:#1e3a8a; }
        table { width:100%; border-collapse:collapse; }
        th, td { text-align:left; padding:8px; border-bottom:1px solid #e5e7eb; }
        label { display:block; margin-top:12px; font-weight:600; }
        input, select, textarea { width:100%; padding:8px; margin-top:4px; box-sizing:border-box; border:1px solid #d1d5db; border-radius:6px; }
        .btn { display:inline-block; margin-top:16px; background:#1e3a8a; color:#fff; padding:10px 18px; border:0; border-radius:6px; cursor:pointer; text-decoration:none; }
        .alert { padding:10px 14px; border-radius:6px; margin-bottom:16px; }
        .alert-success { background:#dcfce7; color:#166534; }
        .alert-error { background:#fee2e2; color:#991b1b; }
        .alert-warn { background:#fef9c3; color:#854d0e; }
        .badge { padding:2px 8px; border-radius:10px; font-size:.8rem; }
        .deposit { background:#dcfce7; color:#166534; }
        .withdrawal, .transfer { background:#fee2e2; color:#991b1b; }
    </style>
</head>
<body>
@auth
<nav>
    <strong>Simple Bank</strong>
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <a href="{{ route('transactions.index') }}">Transactions</a>
    <a href="{{ route('transactions.create') }}">New Transaction</a>
    <span class="spacer"></span>
    <span>{{ auth()->user()->name }}</span>
    <form method="POST" action="{{ route('logout') }}" style="margin:0">
        @csrf
        <button type="submit">Logout</button>
    </form>
</nav>
@endauth
<main>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('warning'))
        <div class="alert alert-warn">&#9888; {{ session('warning') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif
    @yield('content')
</main>
</body>
</html>
