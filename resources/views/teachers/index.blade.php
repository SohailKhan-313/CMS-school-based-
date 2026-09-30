@extends('layouts.app')

@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h3 class="mb-0 fw-bold text-dark"><i class="bi bi-person-workspace text-primary me-2"></i>Teachers & Faculty</h3>
                <p class="text-secondary small mb-0">Manage teaching staff, photos, departmental specializations, and faculty sheets.</p>
            </div>
            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                <a href="{{ route('teachers.pdf') }}" target="_blank" class="btn btn-outline-danger me-2">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Print PDF Directory
                </a>
                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createTeacherModal">
                    <i class="bi bi-person-plus-fill me-1"></i> Add Faculty Member
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

        {{-- Filter & Search --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('teachers.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Search by name, employee code, specialization, email...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">-- All Statuses --</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="on_leave" {{ request('status') === 'on_leave' ? 'selected' : '' }}>On Leave</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel-fill me-1"></i>Filter</button>
                        <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Teachers Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0 text-dark">Faculty Members ({{ $teachers->total() }})</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7 text-secondary">
                        <tr>
                            <th class="ps-3">Code</th>
                            <th>Teacher Name</th>
                            <th>Specialization / Subject</th>
                            <th>Qualification</th>
                            <th>Contact</th>
                            <th>Assigned Classes</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $teacher)
                            <tr>
                                <td class="ps-3 fw-bold text-primary">{{ $teacher->employee_code }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($teacher->photo_url)
                                            <img src="{{ $teacher->photo_url }}" alt="{{ $teacher->name }}" class="rounded-circle me-2 object-fit-cover shadow-sm border" style="width: 40px; height: 40px;">
                                        @else
                                            <div class="rounded-circle bg-success-subtle text-success fw-bold d-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 40px; height: 40px; font-size: 14px;">
                                                {{ strtoupper(substr($teacher->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold text-dark">{{ $teacher->name }}</div>
                                            <div class="small text-muted">{{ $teacher->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $teacher->specialization ?? 'General' }}</span></td>
                                <td>{{ $teacher->qualification ?? '-' }}</td>
                                <td>{{ $teacher->phone ?? '-' }}</td>
                                <td>
                                    @forelse($teacher->schoolClasses as $c)
                                        <span class="badge bg-info-subtle text-info-emphasis me-1">{{ $c->full_name }}</span>
                                    @empty
                                        <span class="text-muted small">None</span>
                                    @endforelse
                                </td>
                                <td>
                                    @if($teacher->status === 'active')
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @elseif($teacher->status === 'on_leave')
                                        <span class="badge bg-warning-subtle text-warning">On Leave</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        {{-- 1-Click Complete Modal View --}}
                                        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewTeacherModal{{ $teacher->id }}" title="View Complete Profile Modal">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                        {{-- Full page view --}}
                                        <a href="{{ route('teachers.show', $teacher->id) }}" class="btn btn-outline-secondary" title="Full Page Profile">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                        {{-- FPDF Print --}}
                                        <a href="{{ route('teachers.profile', $teacher->id) }}" target="_blank" class="btn btn-outline-danger" title="Print Profile Sheet (FPDF)">
                                            <i class="bi bi-printer-fill"></i>
                                        </a>
                                        {{-- Edit --}}
                                        <a href="{{ route('teachers.edit', $teacher->id) }}" class="btn btn-outline-primary" title="Edit Teacher">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        {{-- Delete --}}
                                        <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this faculty record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete Teacher">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- ========================================== -->
                                    <!-- BOOTSTRAP MODAL: VIEW TEACHER PROFILE      -->
                                    <!-- ========================================== -->
                                    <div class="modal fade text-start" id="viewTeacherModal{{ $teacher->id }}" tabindex="-1" aria-labelledby="viewTeacherModalLabel{{ $teacher->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-scrollable my-3">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-dark text-white py-3">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <i class="bi bi-person-badge-fill text-warning fs-5"></i>
                                                        <h5 class="modal-title fw-bold mb-0" id="viewTeacherModalLabel{{ $teacher->id }}">
                                                            Faculty Profile &bull; {{ $teacher->name }}
                                                        </h5>
                                                    </div>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4" style="max-height: calc(85vh - 130px); overflow-y: auto;">
                                                    <div class="row g-4 align-items-center mb-4 pb-3 border-bottom">
                                                        <div class="col-auto text-center">
                                                            @if($teacher->photo_url)
                                                                <img src="{{ $teacher->photo_url }}" alt="{{ $teacher->name }}" class="rounded-circle object-fit-cover shadow border border-3 border-success" style="width: 90px; height: 90px;">
                                                            @else
                                                                <div class="rounded-circle bg-success-subtle text-success fw-bold d-flex align-items-center justify-content-center shadow" style="width: 90px; height: 90px; font-size: 32px;">
                                                                    {{ strtoupper(substr($teacher->name, 0, 2)) }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="col">
                                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                                <h4 class="fw-bold text-dark mb-0">{{ $teacher->name }}</h4>
                                                                @if($teacher->status === 'active')
                                                                    <span class="badge bg-success-subtle text-success">Active</span>
                                                                @elseif($teacher->status === 'on_leave')
                                                                    <span class="badge bg-warning-subtle text-warning">On Leave</span>
                                                                @else
                                                                    <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                                                @endif
                                                            </div>
                                                            <p class="text-secondary mb-1"><strong>Code:</strong> <code>{{ $teacher->employee_code }}</code> | <strong>Department:</strong> {{ $teacher->specialization ?? 'General' }}</p>
                                                            <p class="small text-muted mb-0"><i class="bi bi-envelope me-1"></i>{{ $teacher->email }} &bull; <i class="bi bi-telephone me-1"></i>{{ $teacher->phone ?? 'N/A' }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="row g-3 mb-4">
                                                        <div class="col-sm-6">
                                                            <div class="p-3 bg-light rounded-3">
                                                                <span class="text-muted small d-block">Highest Qualification</span>
                                                                <span class="fw-semibold text-dark">{{ $teacher->qualification ?? 'Not Specified' }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="p-3 bg-light rounded-3">
                                                                <span class="text-muted small d-block">Date of Joining</span>
                                                                <span class="fw-semibold text-dark">{{ $teacher->joining_date ? $teacher->joining_date->format('d M Y') : 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="p-3 bg-light rounded-3">
                                                                <span class="text-muted small d-block">Monthly Salary</span>
                                                                <span class="fw-bold text-success">${{ number_format((float) $teacher->salary, 2) }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="p-3 bg-light rounded-3">
                                                                <span class="text-muted small d-block">Campus / Residence Address</span>
                                                                <span class="fw-semibold text-dark">{{ $teacher->address ?? 'Main Campus' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                                        <i class="bi bi-diagram-3-fill text-primary me-2"></i>Assigned Classes as Class Teacher
                                                    </h6>
                                                    @if($teacher->schoolClasses->count() > 0)
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered align-middle">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Class</th>
                                                                        <th>Section</th>
                                                                        <th>Room</th>
                                                                        <th>Enrolled Students</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($teacher->schoolClasses as $cls)
                                                                        <tr>
                                                                            <td class="fw-bold text-primary">{{ $cls->name }}</td>
                                                                            <td><span class="badge bg-secondary-subtle text-secondary">{{ $cls->section }}</span></td>
                                                                            <td>{{ $cls->room_number ?? 'N/A' }}</td>
                                                                            <td>{{ $cls->students ? $cls->students->count() : 0 }} students</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @else
                                                        <div class="alert alert-light text-muted border py-2 mb-0 small">
                                                            <i class="bi bi-info-circle me-1"></i> No classes are currently assigned to this faculty member.
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer bg-light py-2">
                                                    <a href="{{ route('teachers.profile', $teacher->id) }}" target="_blank" class="btn btn-outline-danger btn-sm">
                                                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Print Official Sheet (FPDF)
                                                    </a>
                                                    <a href="{{ route('teachers.edit', $teacher->id) }}" class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-pencil-fill me-1"></i> Edit Record
                                                    </a>
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                                    No faculty members found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($teachers->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $teachers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- BOOTSTRAP MODAL: ADD FACULTY MEMBER        -->
<!-- ========================================== -->
<div class="modal fade" id="createTeacherModal" tabindex="-1" aria-labelledby="createTeacherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable my-3">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('teachers.store') }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column" style="min-height: 0;">
                @csrf
                <div class="modal-header bg-success text-white py-3">
                    <h5 class="modal-title fw-bold" id="createTeacherModalLabel">
                        <i class="bi bi-person-plus-fill me-2"></i>Add Faculty Member
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4" style="max-height: calc(85vh - 130px); overflow-y: auto;">
                    
                    {{-- Faculty Photo Upload Header --}}
                    <div class="card mb-4 bg-light border-0">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center gap-3">
                                <div>
                                    <img id="teacherPhotoPreview" src="https://via.placeholder.com/100?text=No+Photo" 
                                         alt="Preview" class="rounded-circle object-fit-cover border shadow-sm" 
                                         style="width: 72px; height: 72px;">
                                </div>
                                <div class="flex-grow-1">
                                    <label class="form-label fw-bold mb-1">Faculty Photo / Avatar</label>
                                    <input type="file" name="photo" id="teacherPhotoInput" class="form-control form-control-sm" accept="image/*">
                                    <div class="form-text small">Accepted formats: JPG, PNG, WEBP (Max 2MB). Used on faculty dossiers & profile sheets.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Faculty Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Dr. David Miller" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Employee Code <span class="text-danger">*</span></label>
                            <input type="text" name="employee_code" class="form-control" placeholder="e.g. TCH-1005" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="on_leave">On Leave</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="david.miller@school.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="+1 (555) 345-6789">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Department / Specialization</label>
                            <input type="text" name="specialization" class="form-control" placeholder="e.g. Physics & Chemistry">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Highest Qualification</label>
                            <input type="text" name="qualification" class="form-control" placeholder="e.g. Ph.D. in Physics">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Monthly Salary ($)</label>
                            <input type="number" step="0.01" name="salary" class="form-control" placeholder="5000.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date of Joining</label>
                            <input type="date" name="joining_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Residential Address</label>
                            <textarea name="address" rows="2" class="form-control" placeholder="Street address, city, state..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 fw-semibold">
                        <i class="bi bi-check-circle-fill me-1"></i> Save Faculty Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('teacherPhotoInput')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('teacherPhotoPreview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush

@endsection
