@extends('layouts.app')
@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h3 class="mb-0 fw-bold text-dark"><i class="bi bi-person-badge-fill text-primary me-2"></i>Security Roles</h3>
                <p class="text-secondary small mb-0">Role-based access control and system privilege groups.</p>
            </div>
            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                @can('create roles')
                <a href="{{ route('roles.create') }}" class="btn btn-primary w-100 w-sm-auto"><i class="bi bi-plus-lg me-1"></i> Add Role</a>
                @endcan
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light fs-8 text-secondary">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Role Name</th>
                                <th>Permissions</th>
                                <th class="text-end pe-3 text-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <td class="ps-3 fw-bold text-muted">{{ $role->id }}</td>
                                    <td class="fw-bold text-dark">{{ $role->name }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @forelse ($role->permissions as $permission)
                                                <span class="badge bg-secondary-subtle text-secondary small">{{ $permission->name }}</span>
                                            @empty
                                                <span class="text-muted small fst-italic">No permissions assigned</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="text-end pe-3 text-nowrap">
                                        <div class="d-flex justify-content-end align-items-center gap-1">
                                            @can('edit roles')
                                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-outline-primary rounded-2" title="Edit Role"><i class="bi bi-pencil-fill"></i></a>
                                            @endcan

                                            @can('delete roles')
                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                                class="d-inline m-0 p-0" onsubmit="return confirm('Are you sure you want to delete this role?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-2" title="Delete Role">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection