@extends('layouts.app')

@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h3 class="mb-0 fw-bold text-dark"><i class="bi bi-cash-stack text-success me-2"></i>Fees & Accounts Management</h3>
                <p class="text-secondary small mb-0">Record fee collections, issue student challans, and track outstanding balances.</p>
            </div>
            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                @can('print fee')
                <a href="{{ route('accountant.pdf') }}" target="_blank" class="btn btn-outline-danger me-2">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Print Fees Ledger PDF
                </a>
                @endcan
                @can('create fee')
                <button type="button" class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
                    <i class="bi bi-receipt-cutoff me-1"></i> Issue Fee Invoice
                </button>
                @endcan
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

        {{-- Financial Stat Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small text-uppercase fw-semibold">Total Fees Billed</div>
                            <div class="fs-3 fw-bold mt-1">{{ number_format($totalBilled, 2) }}</div>
                        </div>
                        <i class="bi bi-receipt fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small text-uppercase fw-semibold">Total Revenue Collected</div>
                            <div class="fs-3 fw-bold mt-1">{{ number_format($totalCollected, 2) }}</div>
                        </div>
                        <i class="bi bi-cash-coin fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 bg-danger text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small text-uppercase fw-semibold">Outstanding Dues</div>
                            <div class="fs-3 fw-bold mt-1">{{ number_format($totalPending, 2) }}</div>
                        </div>
                        <i class="bi bi-exclamation-triangle-fill fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search & Filters --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('accountant.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Search by invoice #, title, student name...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">-- All Payment Statuses --</option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="fee_type" class="form-select">
                            <option value="">-- Fee Type --</option>
                            <option value="tuition" {{ request('fee_type') === 'tuition' ? 'selected' : '' }}>Tuition</option>
                            <option value="admission" {{ request('fee_type') === 'admission' ? 'selected' : '' }}>Admission</option>
                            <option value="exam" {{ request('fee_type') === 'exam' ? 'selected' : '' }}>Exam</option>
                            <option value="transport" {{ request('fee_type') === 'transport' ? 'selected' : '' }}>Transport</option>
                            <option value="misc" {{ request('fee_type') === 'misc' ? 'selected' : '' }}>Miscellaneous</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel-fill me-1"></i>Filter</button>
                        <a href="{{ route('accountant.index') }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Invoices Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0 text-dark">Fee Invoices & Challan Records ({{ $invoices->total() }})</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7 text-secondary">
                        <tr>
                            <th class="ps-3">Invoice #</th>
                            <th>Student & Class</th>
                            <th>Fee Title</th>
                            <th>Category</th>
                            <th>Billed</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $inv)
                            <tr>
                                <td class="ps-3 fw-bold text-primary">{{ $inv->invoice_number }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $inv->student ? $inv->student->name : 'N/A' }}</div>
                                    <div class="small text-muted">{{ $inv->student && $inv->student->schoolClass ? $inv->student->schoolClass->full_name : '-' }}</div>
                                </td>
                                <td>{{ $inv->title }}</td>
                                <td><span class="badge bg-light text-dark border">{{ ucfirst($inv->fee_type) }}</span></td>
                                <td class="fw-semibold">{{ number_format((float) $inv->total_amount, 2) }}</td>
                                <td class="text-success fw-semibold">{{ number_format((float) $inv->paid_amount, 2) }}</td>
                                <td>
                                    @if($inv->balance > 0)
                                        <span class="text-danger fw-bold">{{ number_format((float) $inv->balance, 2) }}</span>
                                    @else
                                        <span class="text-success"><i class="bi bi-check-circle-fill"></i> Nil</span>
                                    @endif
                                </td>
                                <td>{{ $inv->due_date ? $inv->due_date->format('d M Y') : '-' }}</td>
                                <td>
                                    @if($inv->status === 'paid')
                                        <span class="badge bg-success-subtle text-success px-2 py-1">Paid</span>
                                    @elseif($inv->status === 'partial')
                                        <span class="badge bg-warning-subtle text-warning-emphasis px-2 py-1">Partial</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-2 py-1">Unpaid</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-flex justify-content-end align-items-center gap-1">
                                        @can('print fee')
                                        <a href="{{ route('accountant.challan', $inv->id) }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-2" title="Print Fee Challan (FPDF)">
                                            <i class="bi bi-file-earmark-pdf-fill"></i>
                                        </a>
                                        @endcan
                                        @can('show accountant')
                                        <a href="{{ route('accountant.show', $inv->id) }}" class="btn btn-sm btn-outline-info rounded-2" title="View Details">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        @endcan
                                        @can('edit fee')
                                        <a href="{{ route('accountant.edit', $inv->id) }}" class="btn btn-sm btn-outline-primary rounded-2" title="Edit / Collect Fee">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        @endcan
                                        @can('delete fee')
                                        <form action="{{ route('accountant.destroy', $inv->id) }}" method="POST" class="d-inline m-0 p-0" onsubmit="return confirm('Are you sure you want to delete this fee invoice record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-2" title="Delete Invoice">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="bi bi-receipt fs-1 d-block mb-2"></i>
                                    No fee invoices found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($invoices->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@can('create fee')
<!-- ========================================== -->
<!-- BOOTSTRAP MODAL: ISSUE FEE INVOICE         -->
<!-- ========================================== -->
<div class="modal fade" id="createInvoiceModal" tabindex="-1" aria-labelledby="createInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable my-3">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('accountant.store') }}" method="POST" class="d-flex flex-column" style="min-height: 0;">
                @csrf
                <div class="modal-header bg-success text-white py-3">
                    <h5 class="modal-title fw-bold" id="createInvoiceModalLabel">
                        <i class="bi bi-receipt-cutoff me-2"></i>Issue New Fee Invoice / Challan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="max-height: calc(85vh - 130px); overflow-y: auto;">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Select Student <span class="text-danger">*</span></label>
                            <select name="student_id" class="form-select" required>
                                <option value="">-- Choose Student --</option>
                                @foreach($students as $st)
                                    <option value="{{ $st->id }}">
                                        {{ $st->name }} ({{ $st->admission_number }}) - {{ $st->schoolClass ? $st->schoolClass->full_name : 'No Class' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Invoice Number <span class="text-danger">*</span></label>
                            <input type="text" name="invoice_number" class="form-control" value="INV-{{ date('Ymd') }}-{{ rand(100, 999) }}" required>
                        </div>

                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Fee Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. October 2026 Tuition & Lab Fee" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Fee Category <span class="text-danger">*</span></label>
                            <select name="fee_type" class="form-select" required>
                                <option value="tuition">Tuition Fee</option>
                                <option value="admission">Admission Fee</option>
                                <option value="exam">Examination Fee</option>
                                <option value="transport">Transport Fee</option>
                                <option value="misc">Miscellaneous</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Total Amount Billed <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="total_amount" class="form-control" placeholder="350.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Initial Paid Amount</label>
                            <input type="number" step="0.01" name="paid_amount" class="form-control" value="0.00" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Payment Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="unpaid">Unpaid</option>
                                <option value="partial">Partial</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Due Date <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" class="form-control" value="{{ date('Y-m-d', strtotime('+15 days')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Date (if paid)</label>
                            <input type="date" name="paid_date" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description / Remarks</label>
                            <textarea name="description" rows="2" class="form-control" placeholder="Optional notes, terms, bank account reference..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 fw-semibold">
                        <i class="bi bi-check-circle-fill me-1"></i> Issue Fee Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@endsection
