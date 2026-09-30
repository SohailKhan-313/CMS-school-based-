@extends('layouts.app')

@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h3 class="mb-0 fw-bold text-dark"><i class="bi bi-diagram-3-fill text-primary me-2"></i>Classes & Sections</h3>
                <p class="text-secondary small mb-0">Manage grade levels, sections, assigned class teachers, and room capacities.</p>
            </div>
            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                <a href="{{ route('classes.pdf') }}" target="_blank" class="btn btn-outline-danger me-2">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Print Classes PDF
                </a>
                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createClassModal">
                    <i class="bi bi-plus-lg me-1"></i> Add New Class
                </button>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        {{-- Flash Alert --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Form Submission Error</div>
                <ul class="mb-0 small ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Classes Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0 text-dark">Active Classes & Sections ({{ $classes->total() }})</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7 text-secondary">
                        <tr>
                            <th class="ps-3">Class Name</th>
                            <th>Section</th>
                            <th>Room #</th>
                            <th>Class Teacher</th>
                            <th>Capacity</th>
                            <th>Enrolled Students</th>
                            <th>Occupancy</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classes as $c)
                            @php
                                $percent = $c->capacity > 0 ? min(100, round(($c->students_count / $c->capacity) * 100)) : 0;
                            @endphp
                            <tr>
                                <td class="ps-3 fw-bold text-dark">{{ $c->name }}</td>
                                <td><span class="badge bg-secondary-subtle text-secondary px-2 py-1">{{ $c->section }}</span></td>
                                <td>{{ $c->room_number ?? '-' }}</td>
                                <td>
                                    @if($c->teacher)
                                        <div class="fw-semibold text-primary">{{ $c->teacher->name }}</div>
                                        <div class="small text-muted">{{ $c->teacher->specialization }}</div>
                                    @else
                                        <span class="text-muted fst-italic">No Class Teacher Assigned</span>
                                    @endif
                                </td>
                                <td>{{ $c->capacity }}</td>
                                <td><span class="fw-bold">{{ $c->students_count }}</span></td>
                                <td style="width: 150px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar {{ $percent >= 90 ? 'bg-danger' : ($percent >= 70 ? 'bg-warning' : 'bg-success') }}" style="width: {{ $percent }}%;"></div>
                                        </div>
                                        <span class="small text-muted">{{ $percent }}%</span>
                                    </div>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-flex justify-content-end align-items-center gap-1">
                                        <a href="{{ route('classes.show', $c->id) }}" class="btn btn-sm btn-outline-info rounded-2" title="View Students">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('classes.edit', $c->id) }}" class="btn btn-sm btn-outline-primary rounded-2" title="Edit Class">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form action="{{ route('classes.destroy', $c->id) }}" method="POST" class="d-inline m-0 p-0" onsubmit="return confirm('Deleting this class will also affect enrolled students. Continue?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-2" title="Delete Class">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No classes created yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($classes->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $classes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- BOOTSTRAP MODAL: ADD NEW CLASS             -->
<!-- ========================================== -->
<div class="modal fade" id="createClassModal" tabindex="-1" aria-labelledby="createClassModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable my-3">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('classes.store') }}" method="POST" class="d-flex flex-column" style="min-height: 0;">
                @csrf
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold" id="createClassModalLabel">
                        <i class="bi bi-plus-circle me-2"></i>Create New Class & Section
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="max-height: calc(85vh - 130px); overflow-y: auto;">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Class Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Grade 10 or Class 9" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Section <span class="text-danger">*</span></label>
                            <input type="text" name="section" class="form-control" placeholder="e.g. A, B, or Green" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Max Capacity <span class="text-danger">*</span></label>
                            <input type="number" name="capacity" class="form-control" value="35" min="1" max="100" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Room Number</label>
                        <input type="text" name="room_number" class="form-control" placeholder="e.g. Room 204 (Science Wing)">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Assigned Class Teacher</label>
                        <select name="teacher_id" class="form-select">
                            <option value="">-- No Class Teacher Assigned --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->specialization ?? 'Faculty' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">
                        <i class="bi bi-check-circle-fill me-1"></i> Save Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
