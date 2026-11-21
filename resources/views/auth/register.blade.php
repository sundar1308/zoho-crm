<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create CRM Organization | Nova CRM Platform</title>

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
        .register-card {
            max-width: 520px;
            width: 100%;
            border-radius: 0.75rem;
        }
    </style>
</head>
<body>
    <div class="register-card p-4 p-sm-5 bg-white shadow-sm border m-3">
        <div class="text-center mb-4">
            <div class="bg-primary text-white rounded-3 d-inline-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 48px; height: 48px; font-size: 1.5rem;">
                <i class="bi bi-clouds-fill"></i>
            </div>
            <h3 class="fw-bold mb-1">Create CRM Workspace</h3>
            <p class="text-muted small">Start managing leads, deals, and pipelines with your team</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger small mb-3">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label for="organization_name" class="form-label small fw-semibold">Company / Organization Name</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-building text-muted"></i></span>
                    <input type="text" class="form-control @error('organization_name') is-invalid @enderror" id="organization_name" name="organization_name" value="{{ old('organization_name') }}" required placeholder="e.g. Acme Corporation">
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label small fw-semibold">Your Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-person text-muted"></i></span>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Jane Doe">
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label small fw-semibold">Phone Number</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-telephone text-muted"></i></span>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+1 (555) 000-0000">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label small fw-semibold">Corporate Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-envelope text-muted"></i></span>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="jane@company.com">
                </div>
            </div>

            <div class="row g-2 mb-4">
                <div class="col-md-6">
                    <label for="password" class="form-label small fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="••••••••">
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label small fw-semibold">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-check2-circle text-muted"></i></span>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="••••••••">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm mb-3">
                <i class="bi bi-check-lg me-1"></i> Register & Launch CRM
            </button>
        </form>

        <div class="text-center small text-muted">
            Already have an account? <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Sign in</a>
        </div>
    </div>
</body>
</html>
