@extends('layouts.app')

@section('title', 'User Profile & Security')

@section('content')
<div class="container-fluid px-0">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">My Account & Preferences</h4>
        <div class="text-muted small">Manage your personal profile, active organization role, and login credentials</div>
    </div>

    <div class="row g-4">
        <!-- Profile Overview Card -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center p-4">
                    <div class="position-relative d-inline-block mb-3">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="rounded-circle border shadow-sm" style="width: 96px; height: 96px; object-fit: cover;">
                        @else
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm fw-bold mx-auto" style="width: 96px; height: 96px; font-size: 2rem;">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <p class="text-muted small mb-2">{{ $user->title ?? 'Team Member' }}</p>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1">
                        {{ $user->roles->first()->name ?? ($user->is_super_admin ? 'Super Administrator' : 'Staff') }}
                    </span>

                    <hr class="my-3">

                    <div class="text-start small">
                        <div class="mb-2"><strong>Email:</strong> {{ $user->email }}</div>
                        <div class="mb-2"><strong>Phone:</strong> {{ $user->phone ?? 'Not provided' }}</div>
                        <div class="mb-2"><strong>Organization:</strong> {{ $user->organization->name ?? 'Default Org' }}</div>
                        <div><strong>Account Status:</strong> <span class="badge bg-success">Active</span></div>
                    </div>
                </div>
            </div>

            <!-- Teams Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-body border-bottom py-3">
                    <h6 class="m-0 fw-bold"><i class="bi bi-people me-2"></i>My Teams & Departments</h6>
                </div>
                <div class="card-body p-3">
                    @forelse($user->teams as $team)
                        <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light mb-2">
                            <span class="small fw-semibold">{{ $team->name }}</span>
                            <span class="badge bg-secondary-subtle text-secondary small">Member</span>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">You are not currently assigned to a department.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Edit Profile & Password Columns -->
        <div class="col-lg-8">
            <!-- Profile Info Form -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-body border-bottom py-3">
                    <h6 class="m-0 fw-bold"><i class="bi bi-person-lines-fill text-primary me-2"></i>Edit Profile Information</h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label small fw-semibold">Full Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label small fw-semibold">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label small fw-semibold">Phone Number</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+1 (555) 000-0000">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="title" class="form-label small fw-semibold">Designation / Job Title</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $user->title) }}" placeholder="e.g. Senior Account Executive">
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="avatar" class="form-label small fw-semibold">Profile Photo</label>
                            <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/*">
                            <div class="form-text small text-muted">Supports JPG, PNG, WEBP up to 2MB.</div>
                            @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                            <i class="bi bi-save me-1"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>

            <!-- Change Password Form -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-body border-bottom py-3">
                    <h6 class="m-0 fw-bold"><i class="bi bi-shield-lock text-danger me-2"></i>Change Password</h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label small fw-semibold">Current Password</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required placeholder="••••••••">
                            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label small fw-semibold">New Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="••••••••">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label small fw-semibold">Confirm New Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="••••••••">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-danger btn-sm px-4 shadow-sm">
                            <i class="bi bi-key me-1"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
