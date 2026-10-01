@extends('layouts.app')
@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h3 class="mb-0 fw-bold text-dark"><i class="bi bi-key-fill text-primary me-2"></i>System Permissions</h3>
                <p class="text-secondary small mb-0">Fine-grained operational rights and privilege tokens.</p>
            </div>
            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                @can('create permissions')
                <a href="{{ route('permissions.create') }}" class="btn btn-primary w-100 w-sm-auto"><i class="bi bi-plus-lg me-1"></i> Add Permission</a>
                @endcan
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
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
                                <th>Permission Name</th>
                                <th class="text-end pe-3 text-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $permission)
                                <tr>
                                    <td class="ps-3 fw-bold text-muted">{{ $permission->id }}</td>
                                    <td><code class="text-primary fw-semibold">{{ $permission->name }}</code></td>
                                    <td class="text-end pe-3 text-nowrap">
                                        <div class="d-flex justify-content-end align-items-center gap-1">
                                            @can('edit permissions')
                                            <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-sm btn-outline-primary rounded-2" title="Edit Permission"><i class="bi bi-pencil-fill"></i></a>
                                            @endcan
                                            @can('delete permissions')
                                            <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="d-inline m-0 p-0" onsubmit="return confirm('Are you sure you want to delete this permission?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-2" title="Delete Permission">
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