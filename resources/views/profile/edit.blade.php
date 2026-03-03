@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="fw-bold fs-4 mb-4">My Profile</h2>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" 
                   class="form-control" 
                   name="name" 
                   id="name" 
                   value="{{ old('name', $user->name) }}">
            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" 
                   class="form-control" 
                   name="email" 
                   id="email" 
                   value="{{ old('email', $user->email) }}">
            @error('email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">
                Password <small>(leave blank to keep current)</small>
            </label>
            <input type="password" 
                   class="form-control" 
                   name="password" 
                   id="password">
            @error('password')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">
                Confirm Password
            </label>
            <input type="password" 
                   class="form-control" 
                   name="password_confirmation" 
                   id="password_confirmation">
        </div>

        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>
@endsection