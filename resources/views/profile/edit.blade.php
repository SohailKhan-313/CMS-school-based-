@extends('layouts.app')

@section('content')
<div class="app-content-header py-3 bg-white border-bottom">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h3 class="mb-0 fw-bold text-dark"><i class="bi bi-person-gear text-primary me-2"></i>My Profile Settings</h3>
                <p class="text-secondary small mb-0">Update your account information, profile avatar picture, and password security.</p>
            </div>
            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Return to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<div class="app-content py-4">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                {{-- Success Alert --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill me-2 fs-5 align-middle"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Please check the form errors</div>
                        <ul class="mb-0 small ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Profile Avatar Section --}}
                            <div class="d-flex flex-column flex-sm-row align-items-center gap-4 mb-4 pb-4 border-bottom">
                                <div class="position-relative">
                                    <img id="userAvatarPreview" 
                                         src="{{ $user->avatar_url }}" 
                                         alt="{{ $user->name }}" 
                                         class="rounded-circle object-fit-cover shadow border border-3 border-primary" 
                                         style="width: 100px; height: 100px;">
                                </div>
                                <div class="flex-grow-1 text-center text-sm-start">
                                    <h5 class="fw-bold text-dark mb-1">Profile Picture</h5>
                                    <p class="text-muted small mb-2">Upload a real avatar or photo (JPEG, PNG, WEBP, max 2MB). It will display on your navbar header and profile.</p>
                                    <input type="file" name="avatar" id="avatarInput" class="form-control form-control-sm w-auto d-inline-block" accept="image/*">
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               name="name" 
                                               id="name" 
                                               value="{{ old('name', $user->name) }}" 
                                               required>
                                    </div>
                                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               name="email" 
                                               id="email" 
                                               value="{{ old('email', $user->email) }}" 
                                               required>
                                    </div>
                                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <div class="bg-light p-3 rounded-3 mb-4">
                                <h6 class="fw-bold text-dark mb-1"><i class="bi bi-shield-lock me-1 text-primary"></i> Change Password</h6>
                                <p class="text-muted small mb-3">Leave both password fields blank if you do not want to change your current password.</p>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="password" class="form-label fw-semibold small">New Password</label>
                                        <input type="password" 
                                               class="form-control form-control-sm @error('password') is-invalid @enderror" 
                                               name="password" 
                                               id="password" 
                                               placeholder="Min 6 characters">
                                        @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="password_confirmation" class="form-label fw-semibold small">Confirm New Password</label>
                                        <input type="password" 
                                               class="form-control form-control-sm" 
                                               name="password_confirmation" 
                                               id="password_confirmation" 
                                               placeholder="Re-type new password">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('home') }}" class="btn btn-light px-4">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                    <i class="bi bi-check-circle-fill me-1"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('avatarInput')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('userAvatarPreview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush

@endsection