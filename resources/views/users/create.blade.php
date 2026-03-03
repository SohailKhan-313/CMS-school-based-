@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create User</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>

        <div class="mb-3">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Assign Roles</label><br>
            @foreach($roles as $role)
                <div class="form-check form-check-inline">
                    <input type="checkbox" 
                           name="roles[]" 
                           value="{{ $role->name }}" 
                           class="form-check-input"
                           {{ (is_array(old('roles')) && in_array($role->name, old('roles'))) ? 'checked' : '' }}>
                    <label class="form-check-label">{{ $role->name }}</label>
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-primary">Create User</button>
    </form>
</div>
@endsection