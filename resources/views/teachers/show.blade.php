@extends('layouts.app')

@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h3 class="mb-0 fw-bold text-dark"><i class="bi bi-person-lines-fill text-primary me-2"></i>Faculty Profile</h3>
                <p class="text-secondary small mb-0">{{ $teacher->name }} ({{ $teacher->employee_code }})</p>
            </div>
            <div class="col-sm-6 mt-3 mt-sm-0 d-flex flex-wrap justify-content-sm-end align-items-center gap-2">
                @can('print teacher')
                <a href="{{ route('teachers.profile', $teacher->id) }}" target="_blank" class="btn btn-danger">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Print Profile Sheet (FPDF)
                </a>
                @endcan
                @can('edit teacher')
                <a href="{{ route('teachers.edit', $teacher->id) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil-fill me-1"></i> Edit Profile
                </a>
                @endcan
                <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm text-center p-4">
                    <div class="mx-auto mb-3">
                        @if($teacher->photo_url)
                            <img src="{{ $teacher->photo_url }}" alt="{{ $teacher->name }}" 
                                 class="rounded-circle object-fit-cover shadow-sm border border-3 border-success" 
                                 style="width: 110px; height: 110px;">
                        @else
                            <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center fw-bold fs-1 mx-auto" style="width: 110px; height: 110px;">
                                {{ strtoupper(substr($teacher->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <h4 class="fw-bold text-dark mb-1">{{ $teacher->name }}</h4>
                    <p class="text-secondary mb-2">{{ $teacher->specialization ?? 'Faculty Member' }}</p>
                    <div class="mb-3">
                        @if($teacher->status === 'active')
                            <span class="badge bg-success-subtle text-success fs-7 px-3 py-1">Active Faculty</span>
                        @elseif($teacher->status === 'on_leave')
                            <span class="badge bg-warning-subtle text-warning fs-7 px-3 py-1">On Leave</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger fs-7 px-3 py-1">Inactive</span>
                        @endif
                    </div>

                    <div class="border-top pt-3 text-start">
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Employee Code:</span>
                            <span class="fw-semibold">{{ $teacher->employee_code }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Email:</span>
                            <span class="fw-semibold">{{ $teacher->email }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Phone:</span>
                            <span class="fw-semibold">{{ $teacher->phone ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Qualification:</span>
                            <span class="fw-semibold">{{ $teacher->qualification ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Joining Date:</span>
                            <span class="fw-semibold">{{ $teacher->joining_date ? $teacher->joining_date->format('d M Y') : 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Monthly Salary:</span>
                            <span class="fw-semibold text-success">{{ number_format((float) $teacher->salary, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-diagram-3-fill text-primary me-2"></i>Assigned Classes as Class Teacher</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light fs-7 text-secondary">
                                <tr>
                                    <th>Class Name</th>
                                    <th>Section</th>
                                    <th>Room #</th>
                                    <th>Capacity</th>
                                    <th>Enrolled Students</th>
                                    <th class="text-end text-nowrap">View Class</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teacher->schoolClasses as $c)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $c->name }}</td>
                                        <td><span class="badge bg-secondary-subtle text-secondary">{{ $c->section }}</span></td>
                                        <td>{{ $c->room_number ?? '-' }}</td>
                                        <td>{{ $c->capacity }}</td>
                                        <td><span class="badge bg-primary text-white">{{ $c->students->count() }} Students</span></td>
                                        <td class="text-end text-nowrap">
                                            @can('show class')
                                            <a href="{{ route('classes.show', $c->id) }}" class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-eye"></i> Details
                                            </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            Not currently assigned as Class Teacher for any section.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
