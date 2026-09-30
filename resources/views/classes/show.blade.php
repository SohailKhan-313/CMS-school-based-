@extends('layouts.app')

@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h3 class="mb-0 fw-bold text-dark"><i class="bi bi-diagram-3-fill text-primary me-2"></i>{{ $class->full_name }}</h3>
                <p class="text-secondary small mb-0">Room: {{ $class->room_number ?? 'TBA' }} | Class Teacher: {{ $class->teacher ? $class->teacher->name : 'Unassigned' }}</p>
            </div>
            <div class="col-sm-6 mt-3 mt-sm-0 d-flex flex-wrap justify-content-sm-end align-items-center gap-2">
                @can('create student')
                <a href="{{ route('students.create') }}?school_class_id={{ $class->id }}" class="btn btn-primary">
                    <i class="bi bi-person-plus-fill me-1"></i> Enroll Student in this Class
                </a>
                @endcan
                <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="fw-bold text-dark mb-0">Enrolled Students in {{ $class->full_name }} ({{ $class->students->count() }} / {{ $class->capacity }})</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light fs-7 text-secondary">
                        <tr>
                            <th class="ps-3">Admission #</th>
                            <th>Roll #</th>
                            <th>Student Name</th>
                            <th>Gender</th>
                            <th>Guardian Name</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th class="text-end pe-3 text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($class->students as $student)
                            <tr>
                                <td class="ps-3 fw-bold text-primary">{{ $student->admission_number }}</td>
                                <td><span class="badge bg-secondary-subtle text-secondary">{{ $student->roll_number }}</span></td>
                                <td>{{ $student->name }}</td>
                                <td>{{ ucfirst($student->gender ?? '-') }}</td>
                                <td>{{ $student->guardian_name ?? '-' }}</td>
                                <td>{{ $student->guardian_phone ?? $student->phone ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $student->status === 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                        {{ ucfirst($student->status) }}
                                    </span>
                                </td>
                                <td class="text-end pe-3 text-nowrap">
                                    <div class="d-flex justify-content-end align-items-center gap-1">
                                        @can('print student')
                                        <a href="{{ route('students.slip', $student->id) }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-2" title="Print Slip">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                        @endcan
                                        @can('show student')
                                        <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-outline-info rounded-2" title="View Profile">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    No students enrolled in this section yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
