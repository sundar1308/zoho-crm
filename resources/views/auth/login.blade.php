<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In | Nova CRM Platform</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f4f6f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .login-card {
            max-width: 440px;
            width: 100%;
            border-radius: 0.75rem;
        }
    </style>
</head>
<body>
    <div class="login-card p-4 p-sm-5 bg-white shadow-sm border m-3">
        <!-- Logo & Header -->
        <div class="text-center mb-4">
            <div class="bg-primary text-white rounded-3 d-inline-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 48px; height: 48px; font-size: 1.5rem;">
                <i class="bi bi-clouds-fill"></i>
            </div>
            <h3 class="fw-bold mb-1">Nova<span class="text-primary">CRM</span></h3>
            <p class="text-muted small">Sign in to your enterprise workspace</p>
        </div>

        @if(session('status'))
            <div class="alert alert-success small mb-3">{{ session('status') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-info small mb-3">{{ session('info') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger small mb-3">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label small fw-semibold">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-envelope text-muted"></i></span>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', 'admin@crm.local') }}" required autofocus placeholder="name@company.com">
                </div>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <label for="password" class="form-label small fw-semibold">Password</label>
                    <a href="{{ route('password.request') }}" class="small text-decoration-none">Forgot password?</a>
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock text-muted"></i></span>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" value="Password123!" required placeholder="••••••••">
                </div>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                <label class="form-check-label small text-muted" for="remember">
                    Remember my session
                </label>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm mb-3">
                <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to CRM
            </button>
        </form>

        <!-- Demo Credentials Box -->
        <div class="bg-light p-3 rounded small border mb-3">
            <div class="fw-bold text-muted mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Quick Demo Accounts</div>
            <div class="text-muted" style="font-size: 0.78rem;">
                <div><strong>Admin:</strong> admin@crm.local / Password123!</div>
                <div><strong>Sales Manager:</strong> sales.manager@crm.local / Password123!</div>
                <div><strong>Sales Rep:</strong> sales.rep@crm.local / Password123!</div>
            </div>
        </div>

        <div class="text-center small text-muted">
            New organization? <a href="{{ route('register') }}" class="text-decoration-none fw-semibold">Create account</a>
        </div>
    </div>
</body>
</html>
