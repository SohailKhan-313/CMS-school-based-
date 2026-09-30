<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System - Login</title>

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #20c997 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            border: none;
        }
        .brand-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: #fff;
            padding: 30px 20px;
            text-align: center;
        }
        .demo-chip {
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 8px 12px;
            background: #f8fafc;
            text-align: left;
        }
        .demo-chip:hover {
            background: #eef2ff;
            border-color: #6366f1;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(99, 102, 241, 0.15);
        }
        .demo-chip.active {
            background: #e0e7ff;
            border-color: #4f46e5;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            
            <div class="card login-card">
                <div class="brand-header">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white text-primary rounded-circle mb-2" style="width: 54px; height: 54px; font-size: 24px;">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <h4 class="fw-bold mb-1">EduManage Portal</h4>
                    <p class="text-white-50 small mb-0">Role-Based School Management System</p>
                </div>

                <div class="card-body p-4">

                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            {{ $errors->first() }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form id="loginForm" method="POST" action="{{ route('login.submit') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" 
                                       id="email"
                                       name="email" 
                                       class="form-control" 
                                       placeholder="name@school.com"
                                       value="{{ old('email', 'admin@school.com') }}"
                                       required 
                                       autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold text-secondary small mb-0">Password</label>
                                <span class="badge bg-light text-muted fw-normal">Default: password</span>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                                <input type="password" 
                                       id="password"
                                       name="password" 
                                       class="form-control" 
                                       value="password"
                                       placeholder="Enter password"
                                       required>
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm mb-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Portal
                        </button>
                    </form>

                    <!-- Quick Demo Credentials Selector -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-bold text-uppercase text-muted" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                                <i class="bi bi-key-fill text-warning me-1"></i> 1-Click Demo Accounts
                            </span>
                            <span class="badge bg-secondary-subtle text-secondary small">Password: <code>password</code></span>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="demo-chip" onclick="fillCredentials('admin@school.com', 'password')">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-shield-lock-fill text-primary me-2"></i>
                                        <div>
                                            <div class="fw-bold small text-dark">Admin</div>
                                            <div class="text-muted" style="font-size: 11px;">admin@school.com</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="demo-chip" onclick="fillCredentials('accountant@school.com', 'password')">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-cash-stack text-success me-2"></i>
                                        <div>
                                            <div class="fw-bold small text-dark">Accountant</div>
                                            <div class="text-muted" style="font-size: 11px;">accountant@school.com</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="demo-chip" onclick="fillCredentials('teacher@school.com', 'password')">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-workspace text-info me-2"></i>
                                        <div>
                                            <div class="fw-bold small text-dark">Teacher</div>
                                            <div class="text-muted" style="font-size: 11px;">teacher@school.com</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="demo-chip" onclick="fillCredentials('student@school.com', 'password')">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-backpack4 text-warning me-2"></i>
                                        <div>
                                            <div class="fw-bold small text-dark">Student</div>
                                            <div class="text-muted" style="font-size: 11px;">student@school.com</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="text-center mt-3 text-white-50 small">
                &copy; {{ date('Y') }} EduManage SMS &bull; Laravel 12 & Spatie RBAC
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function fillCredentials(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
        
        // Highlight inputs briefly
        const emailEl = document.getElementById('email');
        const passEl = document.getElementById('password');
        
        emailEl.classList.add('is-valid');
        passEl.classList.add('is-valid');
        
        setTimeout(() => {
            emailEl.classList.remove('is-valid');
            passEl.classList.remove('is-valid');
        }, 1500);
    }

    // Toggle password visibility
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('togglePasswordIcon');

    toggleBtn.addEventListener('click', function () {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('bi-eye');
            toggleIcon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('bi-eye-slash');
            toggleIcon.classList.add('bi-eye');
        }
    });
</script>

</body>
</html>