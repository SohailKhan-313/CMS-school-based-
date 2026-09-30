@extends('layouts.app')
@section('content')

<h2 class="fw-bold fs-4">Permissions List</h2>

<div class="container mt-5">
    <div class="d-flex justify-content-between mb-4">
        <h4>All Permissions</h4>
        <a href="{{ route('permissions.create') }}" class="btn btn-primary">+ Add Permission</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-striped text-center">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Permission Name</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $permission)
                        <tr>
                            <td>{{ $permission->id }}</td>
                            <td>{{ $permission->name }}</td>
                            <td>
                                <div class="d-flex justify-content-start align-items-center gap-1">
                                    <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-sm btn-primary rounded-2">Edit</a>
                                    <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="d-inline m-0 p-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm rounded-2"
                                            onclick="return confirm('Are you sure you want to delete this permission?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection