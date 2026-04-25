<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FútRivals — Admin Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg:     #0d1117;
            --card:   #161b24;
            --border: rgba(255,255,255,0.08);
            --accent: #c8f026;
            --text:   #f0f4f8;
            --muted:  #8b99b0;
        }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Pitch grid background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(200,240,38,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(200,240,38,.04) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
        }
        /* Glow blob */
        body::after {
            content: '';
            position: fixed;
            top: -200px; left: 50%;
            transform: translateX(-50%);
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(200,240,38,.12) 0%, transparent 65%);
            pointer-events: none;
        }

        .login-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 48px 44px;
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
            animation: slideUp .6s cubic-bezier(.16,1,.3,1) both;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .login-logo {
            text-align: center;
            margin-bottom: 36px;
        }
        .login-logo .brand {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 42px;
            letter-spacing: 1px;
            color: var(--accent);
            line-height: 1;
        }
        .login-logo .brand span { color: var(--text); }
        .login-logo .sub {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 4px;
            color: var(--muted);
            margin-top: 6px;
        }

        .login-title {
            font-family: 'Syne', sans-serif;
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 8px;
        }
        .login-desc {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 28px;
        }

        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--muted);
            margin-bottom: 8px;
        }
        .form-control {
            width: 100%;
            background: rgba(255,255,255,.05);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 13px 16px;
            color: var(--text);
            font-size: 14px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
            font-family: 'DM Sans', sans-serif;
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(200,240,38,.12);
        }
        .form-control::placeholder { color: rgba(255,255,255,.25); }

        .form-error {
            margin-top: 6px;
            font-size: 12px;
            color: #ef4444;
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
        }
        .remember-row input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: var(--accent);
            cursor: pointer;
        }
        .remember-row label { font-size: 13px; color: var(--muted); cursor: pointer; }

        .btn-login {
            width: 100%;
            background: var(--accent);
            color: #0d1117;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-family: 'Syne', sans-serif;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: .5px;
            cursor: pointer;
            transition: background .2s, box-shadow .2s;
        }
        .btn-login:hover {
            background: #d4f52e;
            box-shadow: 0 0 30px rgba(200,240,38,.35);
        }

        .login-footer {
            margin-top: 28px;
            text-align: center;
            font-size: 12px;
            color: var(--muted);
        }

        .alert-error {
            background: rgba(239,68,68,.12);
            border: 1px solid rgba(239,68,68,.25);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            color: #ef4444;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-logo">
        <div class="brand">Fút<span>Rivals</span></div>
        <div class="sub">Super Admin</div>
    </div>

    <div class="login-title">Welcome back 👋</div>
    <div class="login-desc">Sign in to manage the FútRivals platform.</div>

    @if($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.login.post') }}">
        @csrf
        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input
                id="email"
                type="email"
                name="email"
                class="form-control"
                value="{{ old('email') }}"
                placeholder="admin@futrivals.com"
                required
                autofocus
            >
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input
                id="password"
                type="password"
                name="password"
                class="form-control"
                placeholder="••••••••"
                required
            >
        </div>

        <div class="remember-row">
            <input type="checkbox" id="remember" name="remember">
            <label for="remember">Keep me signed in</label>
        </div>

        <button type="submit" class="btn-login">Sign In to Dashboard</button>
    </form>

    <div class="login-footer">
        FútRivals Admin Panel &copy; {{ date('Y') }}
    </div>
</div>
</body>
</html>
