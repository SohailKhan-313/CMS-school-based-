@extends('layouts.app')
@section('content')

        <h2 class="fw-bold fs-4">Roles List</h2>


    <div class="container mt-5">

        <div class="d-flex justify-content-between mb-4">
            <h4>All Roles</h4>
            <a href="{{ route('roles.create') }}" class="btn btn-primary">+ Add Role</a>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card shadow">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>#</th>
                            <th>Role Name</th>
                            <th>permissions</th>
                            <th width="200">Actions</th>
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
                                <td>
                                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-primary">Edit</a>

                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this role?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection