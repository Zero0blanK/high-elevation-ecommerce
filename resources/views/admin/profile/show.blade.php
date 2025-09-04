@extends('admin.layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary">
                        <i class="mdi mdi-pencil me-1"></i> Edit Profile
                    </a>
                </div>
                <h4 class="page-title">My Profile</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <div class="card text-center">
                <div class="card-body">
                    <img src="{{ asset('admin/images/users/avatar-1.jpg') }}" class="rounded-circle avatar-lg img-thumbnail" alt="profile-image">
                    
                    <h4 class="mb-0 mt-2">{{ $admin->name }}</h4>
                    <p class="text-muted font-14">{{ $admin->role_display }}</p>

                    <div class="text-start mt-3">
                        <h4 class="font-13 text-uppercase">About Me :</h4>
                        <p class="text-muted font-13 mb-3">
                            Administrator with {{ $admin->role }} privileges. 
                            Member since {{ $admin->created_at->format('F Y') }}.
                        </p>
                        
                        <p class="text-muted mb-2 font-13">
                            <strong>Full Name :</strong> 
                            <span class="ms-2">{{ $admin->name }}</span>
                        </p>

                        <p class="text-muted mb-2 font-13">
                            <strong>Email :</strong> 
                            <span class="ms-2">{{ $admin->email }}</span>
                        </p>

                        <p class="text-muted mb-2 font-13">
                            <strong>Role :</strong> 
                            <span class="ms-2">{{ $admin->role_display }}</span>
                        </p>

                        <p class="text-muted mb-2 font-13">
                            <strong>Status :</strong> 
                            <span class="ms-2">
                                @if($admin->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </span>
                        </p>

                        <p class="text-muted mb-1 font-13">
                            <strong>Last Login :</strong> 
                            <span class="ms-2">
                                {{ $admin->last_login_at ? $admin->last_login_at->format('M d, Y h:i A') : 'Never' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
                        <li class="nav-item">
                            <a href="#profile" data-bs-toggle="tab" aria-expanded="true" class="nav-link rounded-0 active">
                                Profile Information
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#security" data-bs-toggle="tab" aria-expanded="false" class="nav-link rounded-0">
                                Security Settings
                            </a>
                        </li>
                    </ul>
                    
                    <div class="tab-content">
                        <div class="tab-pane show active" id="profile">
                            <h5 class="mb-4 text-uppercase"><i class="mdi mdi-account-circle me-1"></i> Personal Info</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="firstname" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="firstname" value="{{ $admin->name }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" value="{{ $admin->email }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="role" class="form-label">Role</label>
                                        <input type="text" class="form-control" id="role" value="{{ $admin->role_display }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <input type="text" class="form-control" id="status" value="{{ $admin->is_active ? 'Active' : 'Inactive' }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="created" class="form-label">Member Since</label>
                                        <input type="text" class="form-control" id="created" value="{{ $admin->created_at->format('F d, Y') }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="lastlogin" class="form-label">Last Login</label>
                                        <input type="text" class="form-control" id="lastlogin" value="{{ $admin->last_login_at ? $admin->last_login_at->format('M d, Y h:i A') : 'Never' }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <a href="{{ route('admin.profile.edit') }}" class="btn btn-success mt-2">
                                    <i class="mdi mdi-content-save"></i> Edit Profile
                                </a>
                            </div>
                        </div>

                        <div class="tab-pane" id="security">
                            <h5 class="mb-4 text-uppercase"><i class="mdi mdi-security me-1"></i> Security Settings</h5>
                            
                            <form action="{{ route('admin.profile.password.update') }}" method="POST">
                                @csrf
                                @method('PATCH')
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="current_password" class="form-label">Current Password</label>
                                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                                   id="current_password" name="current_password" required>
                                            @error('current_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">New Password</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" name="password" required>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control" 
                                                   id="password_confirmation" name="password_confirmation" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-success mt-2">
                                        <i class="mdi mdi-content-save"></i> Update Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection