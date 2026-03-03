@extends('layouts.app')
@section('content')


    <h2 class="fw-bold fs-4">Add Role</h2>

    <div class="container mt-5">

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow">
            <div class="card-body">
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Role Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter role name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assign Permissions</label>
                        <div class="row">
                            @foreach($permissions as $permission)
                                <div class="form-check">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                        class="form-check-input">
                                    <label class="form-check-label">{{ $permission->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Add Role</button>
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>

    </div>
@endsection