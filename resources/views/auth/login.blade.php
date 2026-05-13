<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RenTara — Login</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg: #fde9d8;
            --panel: #D97706;
            --panel-soft: #FDE68A;
            --surface: #ffffff;
            --text: #0f172a;
            --muted: #64748B;
            --border: #FCD34D;
            --field-bg: #f8fafc;
            --button: #D97706;
            --button-hover: #B45309;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background-image: radial-gradient(circle at top left, rgba(255,255,255,0.08), transparent 22%), radial-gradient(circle at bottom right, rgba(255,255,255,0.04), transparent 18%);
        }

        .auth-shell {
            width: min(940px, 100%);
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            background: var(--surface);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 40px 90px rgba(15, 23, 42, 0.12);
            min-height: 540px;
        }

        .panel-left {
            background: var(--panel);
            color: #0f172a;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 56px 46px;
            gap: 26px;
        }

        .panel-left h1 {
            margin: 0;
            font-size: clamp(2rem, 2.8vw, 3rem);
            line-height: 1.05;
            letter-spacing: -0.04em;
        }

        .panel-left p {
            margin: 0;
            max-width: 320px;
            line-height: 1.75;
            opacity: 0.92;
        }

        .panel-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: min(220px, 100%);
            padding: 14px 24px;
            border-radius: 999px;
            background: var(--surface);
            color: var(--panel);
            text-decoration: none;
            font-weight: 700;
            box-shadow: 0 18px 40px rgba(245, 158, 11, 0.18);
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .panel-action:hover {
            transform: translateY(-1px);
            background: rgba(255, 255, 255, 0.95);
        }

        .panel-right {
            padding: 56px 46px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .panel-header {
            margin-bottom: 28px;
        }

        .panel-header h2 {
            margin: 0;
            font-size: 2rem;
            letter-spacing: -0.04em;
        }

        .panel-header p {
            margin: 10px 0 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .form-card {
            display: grid;
            gap: 18px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .field label {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.08em;
            color: var(--muted);
        }

        .field input {
            width: 100%;
            padding: 16px 18px;
            border-radius: 16px;
            border: 1px solid var(--border);
            background: var(--field-bg);
            color: var(--text);
            font-size: 1rem;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .field input:focus {
            border-color: var(--button);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 16px 20px;
            border-radius: 999px;
            border: none;
            font-weight: 700;
            cursor: pointer;
            background: var(--button);
            color: white;
            transition: transform 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-primary:hover {
            background: var(--button-hover);
            transform: translateY(-1px);
            box-shadow: 0 18px 40px rgba(37, 99, 235, 0.22);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--muted);
            font-size: 0.95rem;
            margin: 16px 0 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .footer-note {
            color: var(--muted);
            font-size: 0.95rem;
        }

        .footer-note a {
            color: var(--button);
            text-decoration: none;
            font-weight: 700;
        }

        .alert-box {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 0.95rem;
        }

        @media (max-width: 860px) {
            .auth-shell {
                grid-template-columns: 1fr;
            }
            .panel-left,
            .panel-right {
                padding: 36px 28px;
            }
        }

        @media (max-width: 520px) {
            .auth-shell {
                border-radius: 22px;
            }
            .panel-left {
                align-items: center;
                text-align: center;
            }
        }
    </style>
</head>
<body>
<div class="auth-shell">
    <aside class="panel-left">
        <div>
            <h1>Welcome back to RenTara</h1>
            <p>Access your bookings, manage vehicles, and continue where you left off with a modern staff dashboard.</p>
        </div>
        <a href="{{ url('/register') }}" class="panel-action">Create account</a>
    </aside>

    <section class="panel-right">
        <div class="panel-header">
            <h2>Login</h2>
            <p>Sign in to continue to your dashboard.</p>
        </div>

        @if ($errors->any())
            <div class="alert-box">{{ $errors->first() }}</div>
        @endif
        @if(session('error'))
            <div class="alert-box">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="form-card">
            @csrf

            <div class="field">
                <label for="username">Email or Username</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" placeholder="Your email or username" required autofocus>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" placeholder="Your password" required>
            </div>

            <button type="submit" class="btn-primary">Login</button>
        </form>

        <div class="divider"><span>or</span></div>
        <p class="footer-note">Don't have an account? <a href="{{ url('/register') }}">Create one</a></p>
    </section>
</div>
</body>
</html>
