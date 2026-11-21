<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | Nova CRM Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f6f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .reset-card { max-width: 420px; width: 100%; border-radius: 0.75rem; }
    </style>
</head>
<body>
    <div class="reset-card p-4 p-sm-5 bg-white shadow-sm border m-3">
        <div class="text-center mb-4">
            <h4 class="fw-bold mb-1">Reset Password</h4>
            <p class="text-muted small">Enter your email address to receive recovery instructions</p>
        </div>

        @if(session('status'))
            <div class="alert alert-success small mb-3">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label small fw-semibold">Email Address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@company.com">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm mb-3">
                Send Reset Link
            </button>
        </form>

        <div class="text-center small text-muted">
            Remembered your password? <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Sign in</a>
        </div>
    </div>
</body>
</html>
