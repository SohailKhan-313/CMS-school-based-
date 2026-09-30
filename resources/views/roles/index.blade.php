@extends('layouts.app')
@section('content')

    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <h2 class="fw-bold fs-4 mb-0">Security Roles</h2>
            @can('create roles')
            <a href="{{ route('roles.create') }}" class="btn btn-primary">+ Add Role</a>
            @endcan
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
                            <th>Role Name</th>
                            <th>permissions</th>
                            <th class="text-nowrap" width="200">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $role->id }}</td>
                                <td>{{ $role->name }}</td>
                                <td>@foreach ($role->permissions as $permission)
                                    {{ $permission->name }}<br>
                                @endforeach
                                </td>
                                <td class="text-nowrap">
                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        @can('edit roles')
                                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-primary rounded-2">Edit</a>
                                        @endcan

                                        @can('delete roles')
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                            class="d-inline m-0 p-0">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-danger btn-sm rounded-2"
                                                onclick="return confirm('Are you sure you want to delete this role?')">
                                                Delete
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
@endsection