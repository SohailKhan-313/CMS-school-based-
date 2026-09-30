@extends('layouts.app')
@section('content')

    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <h2 class="fw-bold fs-4 mb-0">System Permissions</h2>
            @can('create permissions')
            <a href="{{ route('permissions.create') }}" class="btn btn-primary">+ Add Permission</a>
            @endcan
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped text-center align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Permission Name</th>
                        <th class="text-nowrap" width="200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $permission)
                        <tr>
                            <td>{{ $permission->id }}</td>
                            <td>{{ $permission->name }}</td>
                            <td class="text-nowrap">
                                <div class="d-flex justify-content-start align-items-center gap-1">
                                    @can('edit permissions')
                                    <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-sm btn-primary rounded-2">Edit</a>
                                    @endcan
                                    @can('delete permissions')
                                    <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="d-inline m-0 p-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm rounded-2"
                                            onclick="return confirm('Are you sure you want to delete this permission?')">
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