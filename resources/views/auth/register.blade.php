<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RenTara — Register</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg: #091624;
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
            background-image: radial-gradient(circle at top left, rgba(255,255,255,0.06), transparent 20%), radial-gradient(circle at bottom right, rgba(255,255,255,0.03), transparent 16%);
        }

        .auth-shell {
            width: min(980px, 100%);
            display: grid;
            grid-template-columns: 1fr 1.05fr;
            background: var(--surface);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 40px 90px rgba(15, 23, 42, 0.12);
            min-height: 560px;
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

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
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

        .field input,
        .field select {
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

        .field input:focus,
        .field select:focus {
            border-color: var(--button);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .file-wrapper {
            position: relative;
            cursor: pointer;
        }

        .file-wrapper input[type='file'] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            z-index: 2;
        }

        .file-label {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--field-bg);
            border: 1px dashed #dbeafe;
            border-radius: 16px;
            padding: 16px 18px;
            font-size: 0.95rem;
            color: var(--muted);
        }

        .file-wrapper:hover .file-label {
            border-color: var(--button);
            color: var(--button);
        }

        .file-name {
            margin-top: 8px;
            font-size: 0.9rem;
            color: var(--button);
            display: none;
        }

        .checkbox-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .checkbox-row input {
            width: 18px;
            height: 18px;
            accent-color: var(--button);
            margin-top: 4px;
        }

        .checkbox-row label {
            color: var(--muted);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .checkbox-row a {
            color: var(--button);
            text-decoration: none;
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
            box-shadow: 0 18px 40px rgba(37, 99, 235, 0.18);
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
            margin-top: 14px;
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
            border-radius: 18px;
            padding: 16px 18px;
            font-size: 0.95rem;
        }

        @media (max-width: 920px) {
            .auth-shell {
                grid-template-columns: 1fr;
            }
            .panel-left,
            .panel-right {
                padding: 36px 28px;
            }
        }

        @media (max-width: 620px) {
            .auth-shell {
                border-radius: 22px;
            }
            .panel-left {
                text-align: center;
                align-items: center;
            }
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="auth-shell">
    <aside class="panel-left">
        <div>
            <h1>Create your RenTara account</h1>
            <p>Register now to manage cars, bookings and customers with the same system style as the rest of the dashboard.</p>
        </div>
        <a href="{{ route('login') }}" class="panel-action">Back to login</a>
    </aside>

    <section class="panel-right">
        <div class="panel-header">
            <h2>Register</h2>
            <p>Complete your account details and join the RenTara dashboard.</p>
        </div>

        @if ($errors->any())
            <div class="alert-box">
                <strong>Fix the following issues:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="form-card">
            @csrf

            <div class="form-row">
                <div class="field">
                    <label for="name">Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Full name">
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="you@example.com">
                </div>
            </div>

            <div class="form-row">
                <div class="field">
                    <label for="username">Username</label>
                    <input id="username" type="text" name="username" value="{{ old('username') }}" required placeholder="Choose a username">
                </div>
                <div class="field">
                    <label for="date_of_birth">Date of Birth</label>
                    <input id="date_of_birth" type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="field">
                    <label for="phone">Contact Number</label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone') }}" placeholder="+1 234 567 890">
                </div>
            </div>

            <div class="form-row">
                <div class="field">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required placeholder="Create a password">
                </div>
                <div class="field">
                    <label for="password_confirmation">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Re-enter password">
                </div>
            </div>

            <div class="file-wrapper field">
                <label class="file-label" for="image">Upload profile image</label>
                <input id="image" type="file" name="image" accept="image/*">
                <span class="file-name" id="file-name">No file selected</span>
            </div>

            <div class="checkbox-row">
                <input id="terms" type="checkbox" name="terms">
                <label for="terms">I agree to the <a href="#">terms and conditions</a>.</label>
            </div>

            <button type="submit" class="btn-primary">Create account</button>
        </form>

        <p class="footer-note">Already registered? <a href="{{ route('login') }}">Sign in</a></p>
    </section>
</div>

<script>
    const imageInput = document.getElementById('image');
    const fileName = document.getElementById('file-name');
    if (imageInput) {
        imageInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            fileName.textContent = file ? file.name : 'No file selected';
            fileName.style.display = file ? 'block' : 'none';
        });
    }
</script>
</body>
</html>
