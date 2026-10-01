@extends('layouts.app')

@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h3 class="mb-0 fw-bold text-dark"><i class="bi bi-people-fill text-primary me-2"></i>System Users</h3>
                <p class="text-secondary small mb-0">Authorized accounts, assigned roles, and authentication credentials.</p>
            </div>
            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0 d-flex flex-wrap justify-content-start justify-content-sm-end gap-2">
                @can('see users')
                <a href="{{ route('admin.users.pdf') }}" target="_blank" class="btn btn-outline-danger w-100 w-sm-auto">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Print PDF Directory
                </a>
                @endcan
                @can('create users')
                <a href="{{ route('users.create') }}" class="btn btn-primary w-100 w-sm-auto">
                    <i class="bi bi-plus-lg me-1"></i> Add User
                </a>
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
                                <th>Name</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th class="text-end pe-3 text-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td class="ps-3 fw-bold text-muted">{{ $user->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle me-2 object-fit-cover border shadow-xs" style="width: 32px; height: 32px;">
                                            <span class="fw-bold text-dark">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($user->roles as $role)
                                                <span class="badge bg-primary-subtle text-primary">{{ $role->name }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-end pe-3 text-nowrap">
                                        <div class="d-flex justify-content-end align-items-center gap-1">
                                            @can('edit users')
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary rounded-2" title="Edit User">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            @endcan

                                            @can('delete users')
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline m-0 p-0" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-2" title="Delete User">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection