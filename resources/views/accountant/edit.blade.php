@extends('layouts.app')

@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h3 class="mb-0 fw-bold text-dark"><i class="bi bi-pencil-square text-success me-2"></i>Update Fee Invoice / Collect Payment</h3>
                <p class="text-secondary small mb-0">Invoice #{{ $invoice->invoice_number }} for {{ $invoice->student ? $invoice->student->name : 'N/A' }}</p>
            </div>
            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                <a href="{{ route('accountant.challan', $invoice->id) }}" target="_blank" class="btn btn-outline-danger me-2">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Print Challan
                </a>
                <a href="{{ route('accountant.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Invoices
                </a>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <div class="card border-0 shadow-sm" style="max-width: 850px;">
            <div class="card-body p-4">
                <form action="{{ route('accountant.update', $invoice->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h5 class="text-success fw-bold mb-3 border-bottom pb-2">Student & Invoice Identification</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Target Student <span class="text-danger">*</span></label>
                            <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                                @foreach($students as $s)
                                    <option value="{{ $s->id }}" {{ old('student_id', $invoice->student_id) == $s->id ? 'selected' : '' }}>
                                        {{ $s->name }} ({{ $s->admission_number }} - {{ $s->schoolClass ? $s->schoolClass->full_name : 'No Class' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Invoice Number <span class="text-danger">*</span></label>
                            <input type="text" name="invoice_number" class="form-control @error('invoice_number') is-invalid @enderror" value="{{ old('invoice_number', $invoice->invoice_number) }}" required>
                            @error('invoice_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Invoice / Fee Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $invoice->title) }}" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fee Category <span class="text-danger">*</span></label>
                            <select name="fee_type" class="form-select @error('fee_type') is-invalid @enderror" required>
                                <option value="tuition" {{ old('fee_type', $invoice->fee_type) === 'tuition' ? 'selected' : '' }}>Tuition Fee</option>
                                <option value="admission" {{ old('fee_type', $invoice->fee_type) === 'admission' ? 'selected' : '' }}>Admission Fee</option>
                                <option value="exam" {{ old('fee_type', $invoice->fee_type) === 'exam' ? 'selected' : '' }}>Examination Fee</option>
                                <option value="transport" {{ old('fee_type', $invoice->fee_type) === 'transport' ? 'selected' : '' }}>Transport Fee</option>
                                <option value="library" {{ old('fee_type', $invoice->fee_type) === 'library' ? 'selected' : '' }}>Library Fee</option>
                                <option value="misc" {{ old('fee_type', $invoice->fee_type) === 'misc' ? 'selected' : '' }}>Miscellaneous</option>
                            </select>
                            @error('fee_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <h5 class="text-success fw-bold mb-3 border-bottom pb-2">Financials & Payment Collection</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Total Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="total_amount" class="form-control @error('total_amount') is-invalid @enderror" value="{{ old('total_amount', $invoice->total_amount) }}" required>
                            @error('total_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Paid Amount</label>
                            <input type="number" step="0.01" name="paid_amount" class="form-control @error('paid_amount') is-invalid @enderror" value="{{ old('paid_amount', $invoice->paid_amount) }}">
                            @error('paid_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="unpaid" {{ old('status', $invoice->status) === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                <option value="partial" {{ old('status', $invoice->status) === 'partial' ? 'selected' : '' }}>Partial</option>
                                <option value="paid" {{ old('status', $invoice->status) === 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Due Date <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '') }}" required>
                            @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Payment Date</label>
                            <input type="date" name="paid_date" class="form-control @error('paid_date') is-invalid @enderror" value="{{ old('paid_date', $invoice->paid_date ? $invoice->paid_date->format('Y-m-d') : date('Y-m-d')) }}">
                            @error('paid_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Payment Method</label>
                            <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror">
                                <option value="">-- None / Pending --</option>
                                <option value="cash" {{ old('payment_method', $invoice->payment_method) === 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="bank_transfer" {{ old('payment_method', $invoice->payment_method) === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="online" {{ old('payment_method', $invoice->payment_method) === 'online' ? 'selected' : '' }}>Online Payment</option>
                                <option value="cheque" {{ old('payment_method', $invoice->payment_method) === 'cheque' ? 'selected' : '' }}>Cheque</option>
                            </select>
                            @error('payment_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Internal Notes / Payment Reference</label>
                            <textarea name="notes" rows="2" class="form-control @error('notes') is-invalid @enderror" placeholder="Cheque number, transaction ID, or installments memo...">{{ old('notes', $invoice->notes) }}</textarea>
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('accountant.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-check-lg me-1"></i> Update Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
