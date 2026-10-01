@extends('layouts.app')

@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h3 class="mb-0 fw-bold text-dark"><i class="bi bi-shield-lock-fill text-danger me-2"></i>Administration Center</h3>
                <p class="text-secondary small mb-0">System administration, RBAC security roles, staff overview, and system directories.</p>
            </div>
            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0 d-flex flex-wrap justify-content-start justify-content-sm-end align-items-center gap-2">
                @can('see users')
                <a href="{{ route('admin.users.pdf') }}" target="_blank" class="btn btn-outline-danger w-100 w-sm-auto">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Print Users & Roles PDF
                </a>
                @endcan
                @can('create users')
                <a href="{{ route('users.create') }}" class="btn btn-primary w-100 w-sm-auto">
                    <i class="bi bi-person-plus-fill me-1"></i> Create System User
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        {{-- System Stat Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card border-0 shadow-sm p-3 text-center">
                    <div class="text-muted small text-uppercase">System Users</div>
                    <div class="fs-3 fw-bold text-primary mt-1">{{ $usersCount }}</div>
                    <a href="{{ route('users.index') }}" class="small text-decoration-none mt-1">Manage Users &rarr;</a>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card border-0 shadow-sm p-3 text-center">
                    <div class="text-muted small text-uppercase">Security Roles</div>
                    <div class="fs-3 fw-bold text-success mt-1">{{ $rolesCount }}</div>
                    <a href="{{ route('roles.index') }}" class="small text-decoration-none mt-1">Manage Roles &rarr;</a>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card border-0 shadow-sm p-3 text-center">
                    <div class="text-muted small text-uppercase">Permissions</div>
                    <div class="fs-3 fw-bold text-info mt-1">{{ $permissionsCount }}</div>
                    <a href="{{ route('permissions.index') }}" class="small text-decoration-none mt-1">Manage Privileges &rarr;</a>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card border-0 shadow-sm p-3 text-center">
                    <div class="text-muted small text-uppercase">Students</div>
                    <div class="fs-3 fw-bold text-dark mt-1">{{ $studentsCount }}</div>
                    <a href="{{ route('students.index') }}" class="small text-decoration-none mt-1">View Students &rarr;</a>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card border-0 shadow-sm p-3 text-center">
                    <div class="text-muted small text-uppercase">Faculty Staff</div>
                    <div class="fs-3 fw-bold text-warning mt-1">{{ $teachersCount }}</div>
                    <a href="{{ route('teachers.index') }}" class="small text-decoration-none mt-1">View Faculty &rarr;</a>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card border-0 shadow-sm p-3 text-center">
                    <div class="text-muted small text-uppercase">Classes / Sections</div>
                    <div class="fs-3 fw-bold text-secondary mt-1">{{ $classesCount }}</div>
                    <a href="{{ route('classes.index') }}" class="small text-decoration-none mt-1">View Classes &rarr;</a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Recent Users & Assigned Roles --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-people-fill text-primary me-2"></i>Recent System Users</h5>
                        <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light fs-8 text-secondary">
                                <tr>
                                    <th class="ps-3">Name</th>
                                    <th>Email</th>
                                    <th>Roles</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentUsers as $user)
                                    <tr>
                                        <td class="ps-3 fw-bold text-dark">{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @foreach($user->roles as $role)
                                                <span class="badge bg-primary-subtle text-primary">{{ $role->name }}</span>
                                            @endforeach
                                        </td>
                                        <td class="text-end pe-3">
                                            @can('edit users')
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Quick System Controls --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-gear-fill text-secondary me-2"></i>Administration Shortcuts</h5>
                    </div>
                    <div class="card-body p-3">
                        <div class="list-group list-group-flush">
                            @can('see roles')
                            <a href="{{ route('roles.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-2 py-3">
                                <div>
                                    <div class="fw-semibold text-dark"><i class="bi bi-person-badge text-primary me-2"></i>Role-Based Access Control</div>
                                    <div class="small text-muted">Configure roles, permissions, and guard policies</div>
                                </div>
                                <i class="bi bi-chevron-right text-muted"></i>
                            </a>
                            @endcan
                            @can('see permissions')
                            <a href="{{ route('permissions.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-2 py-3">
                                <div>
                                    <div class="fw-semibold text-dark"><i class="bi bi-key-fill text-warning me-2"></i>Permission Matrix</div>
                                    <div class="small text-muted">Manage granular ability gates and authorization flags</div>
                                </div>
                                <i class="bi bi-chevron-right text-muted"></i>
                            </a>
                            @endcan
                            @can('see users')
                            <a href="{{ route('admin.users.pdf') }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-2 py-3">
                                <div>
                                    <div class="fw-semibold text-danger"><i class="bi bi-file-earmark-pdf-fill me-2"></i>Generate Master Users Report</div>
                                    <div class="small text-muted">Export institutional account records via FPDF</div>
                                </div>
                                <i class="bi bi-box-arrow-up-right text-muted"></i>
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
