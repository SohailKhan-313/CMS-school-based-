@extends('layouts.app')

@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h3 class="mb-0 fw-bold text-dark"><i class="bi bi-mortarboard-fill text-primary me-2"></i>Students Management</h3>
                <p class="text-secondary small mb-0">Manage enrolled students, photos, academic records, and printable profile slips.</p>
            </div>
            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                <a href="{{ route('students.pdf') }}" target="_blank" class="btn btn-outline-danger me-2">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Print PDF Directory
                </a>
                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createStudentModal">
                    <i class="bi bi-person-plus-fill me-1"></i> Enroll New Student
                </button>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        {{-- Flash Alerts --}}
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

        {{-- Filters & Search --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('students.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Search by name, admission #, roll #, guardian...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="class_id" class="form-select">
                            <option value="">-- All Classes --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">-- All Statuses --</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="graduated" {{ request('status') === 'graduated' ? 'selected' : '' }}>Graduated</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel-fill me-1"></i>Filter</button>
                        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Students Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold mb-0 text-dark">Enrolled Students ({{ $students->total() }})</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7 text-secondary">
                        <tr>
                            <th class="ps-3">Admission #</th>
                            <th>Roll #</th>
                            <th>Student</th>
                            <th>Class / Section</th>
                            <th>Gender</th>
                            <th>Guardian</th>
                            <th>Status</th>
                            <th>Fee Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td class="ps-3 fw-bold text-primary">{{ $student->admission_number }}</td>
                                <td><span class="badge bg-secondary-subtle text-secondary">{{ $student->roll_number }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($student->photo_url)
                                            <img src="{{ $student->photo_url }}" alt="{{ $student->name }}" class="rounded-circle me-2 object-fit-cover shadow-sm border" style="width: 40px; height: 40px;">
                                        @else
                                            <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 40px; height: 40px; font-size: 14px;">
                                                {{ strtoupper(substr($student->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold text-dark">{{ $student->name }}</div>
                                            @if($student->email)<div class="small text-muted">{{ $student->email }}</div>@endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info-emphasis px-2 py-1">
                                        {{ $student->schoolClass ? $student->schoolClass->full_name : 'Not Assigned' }}
                                    </span>
                                </td>
                                <td>{{ ucfirst($student->gender ?? '-') }}</td>
                                <td>
                                    <div>{{ $student->guardian_name ?? '-' }}</div>
                                    @if($student->guardian_phone)<div class="small text-muted">{{ $student->guardian_phone }}</div>@endif
                                </td>
                                <td>
                                    @if($student->status === 'active')
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @elseif($student->status === 'inactive')
                                        <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">Graduated</span>
                                    @endif
                                </td>
                                <td>
                                    @php $due = $student->dueFees(); @endphp
                                    @if($due <= 0 && $student->feeInvoices->count() > 0)
                                        <span class="badge bg-success text-white"><i class="bi bi-check2"></i> Cleared</span>
                                    @elseif($due > 0)
                                        <span class="badge bg-warning text-dark">${{ number_format($due, 0) }} Due</span>
                                    @else
                                        <span class="badge bg-light text-muted">No Invoices</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('students.slip', $student->id) }}" target="_blank" class="btn btn-outline-danger" title="Print Slip (FPDF)">
                                            <i class="bi bi-printer-fill"></i>
                                        </a>
                                        <a href="{{ route('students.show', $student->id) }}" class="btn btn-outline-info" title="View Profile">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('students.edit', $student->id) }}" class="btn btn-outline-primary" title="Edit Student">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this student record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete Student">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No students found matching your criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($students->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- BOOTSTRAP MODAL: ENROLL NEW STUDENT       -->
<!-- ========================================== -->
<div class="modal fade" id="createStudentModal" tabindex="-1" aria-labelledby="createStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="createStudentModalLabel">
                    <i class="bi bi-person-plus-fill me-2"></i>Enroll New Student
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    
                    {{-- Student Photo Upload Header --}}
                    <div class="card mb-4 bg-light border-0">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center gap-3">
                                <div>
                                    <img id="studentPhotoPreview" src="https://via.placeholder.com/100?text=No+Photo" 
                                         alt="Preview" class="rounded-circle object-fit-cover border shadow-sm" 
                                         style="width: 72px; height: 72px;">
                                </div>
                                <div class="flex-grow-1">
                                    <label class="form-label fw-bold mb-1">Student Photo / Avatar</label>
                                    <input type="file" name="photo" id="studentPhotoInput" class="form-control form-control-sm" accept="image/*">
                                    <div class="form-text small">Accepted formats: JPG, PNG, WEBP (Max 2MB). Used for ID cards and profile slips.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Student Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Liam Anderson" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Admission # <span class="text-danger">*</span></label>
                            <input type="text" name="admission_number" class="form-control" placeholder="e.g. ADM-2026-001" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Roll # <span class="text-danger">*</span></label>
                            <input type="text" name="roll_number" class="form-control" placeholder="e.g. 101" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Class & Section <span class="text-danger">*</span></label>
                            <select name="school_class_id" class="form-select" required>
                                <option value="">-- Select Class --</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="graduated">Graduated</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Admission Date</label>
                            <input type="date" name="admission_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Student Email</label>
                            <input type="email" name="email" class="form-control" placeholder="student@school.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Student Phone</label>
                            <input type="text" name="phone" class="form-control" placeholder="+1 (555) 000-0000">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Guardian Name</label>
                            <input type="text" name="guardian_name" class="form-control" placeholder="e.g. John Anderson">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Guardian Phone</label>
                            <input type="text" name="guardian_phone" class="form-control" placeholder="+1 (555) 111-2222">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Guardian Relation</label>
                            <input type="text" name="guardian_relation" class="form-control" placeholder="Father / Mother / Guardian">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Residential Address</label>
                            <textarea name="address" rows="2" class="form-control" placeholder="Street address, city, state..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">
                        <i class="bi bi-check-circle-fill me-1"></i> Save & Enroll Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('studentPhotoInput')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('studentPhotoPreview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush

@endsection
