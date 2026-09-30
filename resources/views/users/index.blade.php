@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h2 class="fw-bold fs-4 mb-0">Users List</h2>
        <div class="d-flex flex-wrap gap-2">
            @can('see users')
            <a href="{{ route('admin.users.pdf') }}" target="_blank" class="btn btn-outline-danger">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Print PDF Directory
            </a>
            @endcan
            @can('create users')
            <a href="{{ route('users.create') }}" class="btn btn-primary">+ Add User</a>
            @endcan
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-dark text-center">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th class="text-nowrap" width="200">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @foreach ($user->roles as $role)
                                    <span class="badge bg-info text-dark">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td class="text-nowrap">
                                <div class="d-flex justify-content-center align-items-center gap-1">
                                    @can('edit users')
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-primary rounded-2">
                                        Edit
                                    </a>
                                    @endcan

                                    @can('delete users')
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline m-0 p-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger rounded-2"
                                            onclick="return confirm('Are you sure you want to delete this user?')">
                                            Delete
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    
                    @if($users->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center">No users found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            </div>
        </div>
    </div>

</div>
@endsection