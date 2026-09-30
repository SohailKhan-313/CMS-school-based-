@extends('layouts.app')

@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h3 class="mb-0 fw-bold text-dark"><i class="bi bi-receipt text-success me-2"></i>Invoice Details</h3>
                <p class="text-secondary small mb-0">{{ $invoice->invoice_number }} - {{ $invoice->title }}</p>
            </div>
            <div class="col-sm-6 mt-3 mt-sm-0 d-flex flex-wrap justify-content-sm-end align-items-center gap-2">
                @can('print fee')
                <a href="{{ route('accountant.challan', $invoice->id) }}" target="_blank" class="btn btn-danger">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Print Official Challan (FPDF)
                </a>
                @endcan
                @can('edit fee')
                <a href="{{ route('accountant.edit', $invoice->id) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil-fill me-1"></i> Edit Invoice
                </a>
                @endcan
                <a href="{{ route('accountant.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <div class="card border-0 shadow-sm" style="max-width: 850px; margin: 0 auto;">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-0">EXCELLENCE ACADEMY</h5>
                    <span class="text-muted small">Official Student Fee Challan & Invoice Record</span>
                </div>
                <div>
                    @if($invoice->status === 'paid')
                        <span class="badge bg-success-subtle text-success fs-7 px-3 py-1">PAID</span>
                    @elseif($invoice->status === 'partial')
                        <span class="badge bg-warning-subtle text-warning-emphasis fs-7 px-3 py-1">PARTIAL</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger fs-7 px-3 py-1">UNPAID</span>
                    @endif
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-4 mb-4">
                    <div class="col-sm-6">
                        <h6 class="text-muted text-uppercase fs-8 fw-bold">Billed To (Student)</h6>
                        <h5 class="fw-bold text-dark mb-1">{{ $invoice->student ? $invoice->student->name : 'N/A' }}</h5>
                        <p class="text-secondary mb-1">Admission #: {{ $invoice->student ? $invoice->student->admission_number : '-' }}</p>
                        <p class="text-secondary mb-1">Class: {{ $invoice->student && $invoice->student->schoolClass ? $invoice->student->schoolClass->full_name : '-' }}</p>
                        <p class="text-secondary mb-0">Guardian: {{ $invoice->student ? $invoice->student->guardian_name : '-' }} ({{ $invoice->student ? $invoice->student->guardian_phone : '-' }})</p>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <h6 class="text-muted text-uppercase fs-8 fw-bold">Invoice Details</h6>
                        <p class="mb-1"><span class="text-muted">Invoice No:</span> <strong class="text-primary">{{ $invoice->invoice_number }}</strong></p>
                        <p class="mb-1"><span class="text-muted">Issue Date:</span> {{ $invoice->created_at ? $invoice->created_at->format('d M Y') : date('d M Y') }}</p>
                        <p class="mb-1"><span class="text-muted">Due Date:</span> <strong class="text-danger">{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : '-' }}</strong></p>
                        <p class="mb-0"><span class="text-muted">Payment Method:</span> {{ ucfirst($invoice->payment_method ?? 'Not Recorded') }}</p>
                    </div>
                </div>

                {{-- Itemized Table --}}
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead class="table-light text-uppercase fs-8 text-secondary">
                            <tr>
                                <th>Fee Item Description</th>
                                <th class="text-center">Category</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $invoice->title }}</div>
                                    @if($invoice->notes)<div class="small text-muted">{{ $invoice->notes }}</div>@endif
                                </td>
                                <td class="text-center align-middle"><span class="badge bg-light text-dark border">{{ ucfirst($invoice->fee_type) }}</span></td>
                                <td class="text-end align-middle fw-bold">{{ number_format((float) $invoice->total_amount, 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-end">Total Billed:</th>
                                <th class="text-end">{{ number_format((float) $invoice->total_amount, 2) }}</th>
                            </tr>
                            <tr class="table-success">
                                <th colspan="2" class="text-end">Total Paid:</th>
                                <th class="text-end text-success">{{ number_format((float) $invoice->paid_amount, 2) }}</th>
                            </tr>
                            <tr class="table-light">
                                <th colspan="2" class="text-end">Outstanding Balance Due:</th>
                                <th class="text-end text-danger">{{ number_format((float) $invoice->balance, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-3 border-top">
                    <div class="text-muted small">
                        Recorded by: {{ $invoice->creator ? $invoice->creator->name : 'System Accountant' }}
                    </div>
                    @can('print fee')
                    <a href="{{ route('accountant.challan', $invoice->id) }}" target="_blank" class="btn btn-danger">
                        <i class="bi bi-printer-fill me-1"></i> Print Official Fee Challan (FPDF)
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
