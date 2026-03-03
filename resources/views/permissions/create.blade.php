@extends('layouts.app')
@section('content')

<h2 class="fw-bold fs-4">Add Permission</h2>

<div class="container mt-5">

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('permissions.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Permission Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter permission name" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Permission</button>
                <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

</div>
@endsection