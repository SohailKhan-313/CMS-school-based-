@extends('layouts.app')

@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h3 class="mb-0 fw-bold text-dark"><i class="bi bi-person-badge-fill text-primary me-2"></i>Student Profile</h3>
                <p class="text-secondary small mb-0">{{ $student->name }} ({{ $student->admission_number }})</p>
            </div>
            <div class="col-sm-6 mt-3 mt-sm-0 d-flex flex-wrap justify-content-sm-end align-items-center gap-2">
                @can('print student')
                <a href="{{ route('students.slip', $student->id) }}" target="_blank" class="btn btn-danger">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Print Official Slip (FPDF)
                </a>
                @endcan
                @can('edit student')
                <a href="{{ route('students.edit', $student->id) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil-fill me-1"></i> Edit Profile
                </a>
                @endcan
                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <div class="row g-4">
            {{-- Left Profile Card --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm text-center p-4">
                    <div class="mx-auto mb-3">
                        @if($student->photo_url)
                            <img src="{{ $student->photo_url }}" alt="{{ $student->name }}" 
                                 class="rounded-circle object-fit-cover shadow-sm border border-3 border-primary" 
                                 style="width: 110px; height: 110px;">
                        @else
                            <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-1 mx-auto" style="width: 110px; height: 110px;">
                                {{ strtoupper(substr($student->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <h4 class="fw-bold text-dark mb-1">{{ $student->name }}</h4>
                    <p class="text-secondary mb-2">{{ $student->admission_number }}</p>
                    <div class="mb-3">
                        @if($student->status === 'active')
                            <span class="badge bg-success-subtle text-success fs-7 px-3 py-1">Active Student</span>
                        @elseif($student->status === 'inactive')
                            <span class="badge bg-danger-subtle text-danger fs-7 px-3 py-1">Inactive</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning fs-7 px-3 py-1">Graduated</span>
                        @endif
                    </div>

                    <div class="border-top pt-3 text-start">
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Roll Number:</span>
                            <span class="fw-semibold">{{ $student->roll_number }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Class & Section:</span>
                            <span class="fw-semibold text-primary">{{ $student->schoolClass ? $student->schoolClass->full_name : 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Class Teacher:</span>
                            <span class="fw-semibold">{{ $student->schoolClass && $student->schoolClass->teacher ? $student->schoolClass->teacher->name : 'Unassigned' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Gender:</span>
                            <span class="fw-semibold">{{ ucfirst($student->gender ?? '-') }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Date of Birth:</span>
                            <span class="fw-semibold">{{ $student->date_of_birth ? $student->date_of_birth->format('d M Y') : 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Admission Date:</span>
                            <span class="fw-semibold">{{ $student->admission_date ? $student->admission_date->format('d M Y') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Guardian Card --}}
                <div class="card border-0 shadow-sm mt-4 p-4">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-people-fill text-primary me-2"></i>Guardian Details</h5>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Guardian Name:</span>
                        <span class="fw-semibold">{{ $student->guardian_name ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Relationship:</span>
                        <span class="fw-semibold">{{ $student->guardian_relation ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Phone:</span>
                        <span class="fw-semibold">{{ $student->guardian_phone ?? 'N/A' }}</span>
                    </div>
                    <div class="py-1">
                        <span class="text-muted d-block">Home Address:</span>
                        <span class="fw-semibold small">{{ $student->address ?? 'No address recorded' }}</span>
                    </div>
                </div>
            </div>

            {{-- Right Financial & Invoices Card --}}
            <div class="col-lg-8">
                {{-- Financial Overview --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 bg-primary text-white shadow-sm p-3">
                            <div class="small text-white-50">Total Fees Incurred</div>
                            <div class="fs-4 fw-bold mt-1">{{ number_format($student->totalFees(), 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 bg-success text-white shadow-sm p-3">
                            <div class="small text-white-50">Total Fees Paid</div>
                            <div class="fs-4 fw-bold mt-1">{{ number_format($student->paidFees(), 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 bg-danger text-white shadow-sm p-3">
                            <div class="small text-white-50">Outstanding Balance</div>
                            <div class="fs-4 fw-bold mt-1">{{ number_format($student->dueFees(), 2) }}</div>
                        </div>
                    </div>
                </div>

                {{-- Fee Invoices Table --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-cash-stack text-success me-2"></i>Fee Invoices & Statements</h5>
                        @can('create fee')
                        <a href="{{ route('accountant.create') }}?student_id={{ $student->id }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-plus-lg me-1"></i> Issue New Invoice
                        </a>
                        @endcan
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light fs-7 text-secondary">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Title</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th class="text-end text-nowrap">Print Receipt</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($student->feeInvoices as $inv)
                                    <tr>
                                        <td class="fw-bold text-primary">{{ $inv->invoice_number }}</td>
                                        <td>{{ $inv->title }}</td>
                                        <td>{{ number_format((float) $inv->total_amount, 2) }}</td>
                                        <td class="text-success">{{ number_format((float) $inv->paid_amount, 2) }}</td>
                                        <td>{{ $inv->due_date ? $inv->due_date->format('d M Y') : '-' }}</td>
                                        <td>
                                            @if($inv->status === 'paid')
                                                <span class="badge bg-success-subtle text-success">Paid</span>
                                            @elseif($inv->status === 'partial')
                                                <span class="badge bg-warning-subtle text-warning">Partial</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">Unpaid</span>
                                            @endif
                                        </td>
                                        <td class="text-end text-nowrap">
                                            @can('print fee')
                                            <a href="{{ route('accountant.challan', $inv->id) }}" target="_blank" class="btn btn-sm btn-outline-danger" title="Print Fee Challan">
                                                <i class="bi bi-file-earmark-pdf-fill"></i> Challan
                                            </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            No fee invoices issued yet for this student.
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
