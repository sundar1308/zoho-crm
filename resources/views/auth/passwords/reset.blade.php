<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set New Password | Nova CRM Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .reset-card { max-width: 440px; width: 100%; border-radius: 0.75rem; }
    </style>
</head>
<body>
    <div class="reset-card p-4 p-sm-5 bg-white shadow-sm border m-3">
        <div class="text-center mb-4">
            <h4 class="fw-bold mb-1">Set New Password</h4>
            <p class="text-muted small">Choose a strong, secure password for your account</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label for="email" class="form-label small fw-semibold">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ $email ?? old('email') }}" required autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label small fw-semibold">New Password</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label small fw-semibold">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm mb-3">
                Update Password
            </button>
        </form>
    </div>
</body>
</html>
