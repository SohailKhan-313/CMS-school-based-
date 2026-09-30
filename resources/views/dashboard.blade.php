@extends('layouts.app')

@section('content')
<!--begin::App Content Header-->
<div class="app-content-header py-3 bg-white border-bottom">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h3 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-speedometer2 text-primary me-2"></i>Institutional Dashboard
                </h3>
                <p class="text-secondary small mb-0">Live interconnected overview of students, faculty, classes, and financial accounting.</p>
            </div>
            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                @canany(['print student', 'print teacher', 'print class', 'print fee', 'see users'])
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Quick PDF Reports
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        @can('print student')
                        <li><a class="dropdown-item" href="{{ route('students.pdf') }}" target="_blank"><i class="bi bi-mortarboard me-2 text-primary"></i>Students Directory</a></li>
                        @endcan
                        @can('print teacher')
                        <li><a class="dropdown-item" href="{{ route('teachers.pdf') }}" target="_blank"><i class="bi bi-person-workspace me-2 text-success"></i>Faculty Directory</a></li>
                        @endcan
                        @can('print class')
                        <li><a class="dropdown-item" href="{{ route('classes.pdf') }}" target="_blank"><i class="bi bi-diagram-3 me-2 text-info"></i>Classes Roster</a></li>
                        @endcan
                        @can('print fee')
                        <li><a class="dropdown-item" href="{{ route('accountant.pdf') }}" target="_blank"><i class="bi bi-cash-stack me-2 text-warning"></i>Fee Ledger Statement</a></li>
                        @endcan
                        @can('see users')
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('admin.users.pdf') }}" target="_blank"><i class="bi bi-people me-2 text-secondary"></i>Users & Roles Summary</a></li>
                        @endcan
                    </ul>
                </div>
                @endcanany
            </div>
        </div>
    </div>
</div>
<!--end::App Content Header-->

<!--begin::App Content-->
<div class="app-content py-4">
    <div class="container-fluid">

        {{-- Top 5 Stat Cards --}}
        <div class="row g-3 mb-4">
            {{-- Students --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-secondary small text-uppercase fw-semibold">Total Students</span>
                                <h2 class="fw-bold text-dark my-1">{{ $totalStudents }}</h2>
                                <span class="badge bg-success-subtle text-success fs-8">
                                    <i class="bi bi-check-circle me-1"></i>{{ $activeStudents }} Active Enrolled
                                </span>
                            </div>
                            <div class="bg-primary-subtle text-primary p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                                <i class="bi bi-mortarboard-fill fs-3"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-light px-3 py-2 border-top">
                        <a href="{{ route('students.index') }}" class="small text-decoration-none fw-semibold text-primary">
                            Manage Students &rarr;
                        </a>
                    </div>
                </div>
            </div>

            {{-- Teachers --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-secondary small text-uppercase fw-semibold">Faculty Members</span>
                                <h2 class="fw-bold text-dark my-1">{{ $totalTeachers }}</h2>
                                <span class="badge bg-info-subtle text-info-emphasis fs-8">
                                    <i class="bi bi-person-check me-1"></i>{{ $activeTeachers }} Active Teachers
                                </span>
                            </div>
                            <div class="bg-success-subtle text-success p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                                <i class="bi bi-person-workspace fs-3"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-light px-3 py-2 border-top">
                        <a href="{{ route('teachers.index') }}" class="small text-decoration-none fw-semibold text-success">
                            View Faculty List &rarr;
                        </a>
                    </div>
                </div>
            </div>

            {{-- Fee Revenue Collected --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-secondary small text-uppercase fw-semibold">Fees Collected</span>
                                <h2 class="fw-bold text-success my-1">{{ number_format($totalCollected, 0) }}</h2>
                                <span class="badge bg-success-subtle text-success fs-8">
                                    {{ $collectionRate }}% Collection Rate
                                </span>
                            </div>
                            <div class="bg-warning-subtle text-warning-emphasis p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                                <i class="bi bi-cash-coin fs-3"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-light px-3 py-2 border-top">
                        <a href="{{ route('accountant.index') }}" class="small text-decoration-none fw-semibold text-warning-emphasis">
                            Finance & Ledger &rarr;
                        </a>
                    </div>
                </div>
            </div>

            {{-- Pending Dues --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-secondary small text-uppercase fw-semibold">Pending Dues</span>
                                <h2 class="fw-bold text-danger my-1">{{ number_format($totalOutstanding, 0) }}</h2>
                                <span class="badge bg-danger-subtle text-danger fs-8">
                                    Total Billed: {{ number_format($totalBilled, 0) }}
                                </span>
                            </div>
                            <div class="bg-danger-subtle text-danger p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                                <i class="bi bi-clock-history fs-3"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-light px-3 py-2 border-top">
                        <a href="{{ route('accountant.index') }}?status=unpaid" class="small text-decoration-none fw-semibold text-danger">
                            Track Unpaid Invoices &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Interconnected Charts Row --}}
        <div class="row g-4 mb-4">
            {{-- Class Enrollment Bar Chart --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Class Enrollment Distribution</h5>
                            <span class="text-muted small">Live student counts enrolled per class & section</span>
                        </div>
                        <a href="{{ route('classes.index') }}" class="btn btn-sm btn-outline-primary">
                            All Classes ({{ $totalClasses }})
                        </a>
                    </div>
                    <div class="card-body p-3">
                        <div id="enrollment-chart" style="min-height: 320px; height: 320px; width: 100%;"></div>
                    </div>
                </div>
            </div>

            {{-- Fee Status Donut Chart --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-pie-chart-fill text-success me-2"></i>Fee Invoices Breakdown</h5>
                        <span class="text-muted small">Status breakdown of billed fee challans</span>
                    </div>
                    <div class="card-body p-3">
                        <div id="fee-status-chart" style="min-height: 250px; height: 250px; width: 100%;"></div>
                        <div class="d-flex justify-content-around w-100 mt-2 text-center border-top pt-3">
                            <div>
                                <div class="fs-5 fw-bold text-success">{{ $paidInvoicesCount }}</div>
                                <span class="small text-muted">Paid</span>
                            </div>
                            <div>
                                <div class="fs-5 fw-bold text-warning">{{ $partialInvoicesCount }}</div>
                                <span class="small text-muted">Partial</span>
                            </div>
                            <div>
                                <div class="fs-5 fw-bold text-danger">{{ $unpaidInvoicesCount }}</div>
                                <span class="small text-muted">Unpaid</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Interconnected Tables Row --}}
        <div class="row g-4">
            {{-- Recent Student Admissions --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-mortarboard text-primary me-2"></i>Recent Student Admissions</h5>
                        @can('create student')
                        <a href="{{ route('students.create') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> Enroll Student
                        </a>
                        @endcan
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light fs-8 text-secondary">
                                <tr>
                                    <th class="ps-3">Admission #</th>
                                    <th>Student</th>
                                    <th>Class</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentStudents as $s)
                                    <tr>
                                        <td class="ps-3 fw-bold text-primary">{{ $s->admission_number }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $s->name }}</div>
                                            <div class="small text-muted">{{ $s->guardian_name ?? 'Guardian: N/A' }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info-emphasis">
                                                {{ $s->schoolClass ? $s->schoolClass->full_name : 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="d-flex justify-content-end align-items-center gap-1">
                                                @can('print student')
                                                <a href="{{ route('students.slip', $s->id) }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-2" title="Print Slip (FPDF)">
                                                    <i class="bi bi-printer"></i>
                                                </a>
                                                @endcan
                                                @can('show student')
                                                <a href="{{ route('students.show', $s->id) }}" class="btn btn-sm btn-outline-secondary rounded-2" title="View Profile">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Recent Invoices & Payments --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-cash-stack text-success me-2"></i>Recent Fee Invoices</h5>
                        @can('create fee')
                        <a href="{{ route('accountant.create') }}" class="btn btn-sm btn-success">
                            <i class="bi bi-plus-lg me-1"></i> Issue Invoice
                        </a>
                        @endcan
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light fs-8 text-secondary">
                                <tr>
                                    <th class="ps-3">Invoice #</th>
                                    <th>Student</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Challan PDF</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentInvoices as $inv)
                                    <tr>
                                        <td class="ps-3 fw-bold text-primary">{{ $inv->invoice_number }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $inv->student ? $inv->student->name : 'N/A' }}</div>
                                            <div class="small text-muted">{{ $inv->title }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ number_format((float) $inv->total_amount, 2) }}</div>
                                            @if($inv->balance > 0)
                                                <div class="small text-danger">Bal: {{ number_format((float) $inv->balance, 2) }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($inv->status === 'paid')
                                                <span class="badge bg-success-subtle text-success">Paid</span>
                                            @elseif($inv->status === 'partial')
                                                <span class="badge bg-warning-subtle text-warning">Partial</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">Unpaid</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">
                                            @can('print fee')
                                            <a href="{{ route('accountant.challan', $inv->id) }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-2">
                                                <i class="bi bi-file-earmark-pdf-fill"></i> Challan
                                            </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!--end::App Content-->
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Enrollment Distribution Chart
    const classLabels = @json($classLabels);
    const classCounts = @json($classStudentCounts);

    const enrollmentOptions = {
        series: [{
            name: 'Enrolled Students',
            data: classCounts
        }],
        chart: {
            type: 'bar',
            height: 320,
            toolbar: { show: false },
            animations: {
                enabled: false
            },
            redrawOnParentResize: false,
            redrawOnWindowResize: true
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '45%',
                distributed: true
            }
        },
        dataLabels: { enabled: true },
        colors: ['#0d6efd', '#20c997', '#ffc107', '#fd7e14', '#6f42c1', '#0dcaf0'],
        xaxis: {
            categories: classLabels,
            labels: {
                style: { fontSize: '12px' }
            }
        },
        yaxis: {
            title: { text: 'Number of Students' }
        },
        legend: { show: false }
    };

    const enrollmentChartElem = document.querySelector("#enrollment-chart");
    if (enrollmentChartElem) {
        const enrollmentChart = new ApexCharts(enrollmentChartElem, enrollmentOptions);
        enrollmentChart.render();
    }

    // 2. Fee Status Breakdown Donut Chart
    const paidCount = {{ $paidInvoicesCount }};
    const partialCount = {{ $partialInvoicesCount }};
    const unpaidCount = {{ $unpaidInvoicesCount }};

    const feeStatusOptions = {
        series: [paidCount, partialCount, unpaidCount],
        labels: ['Paid', 'Partial', 'Unpaid'],
        chart: {
            type: 'donut',
            height: 250,
            animations: {
                enabled: false
            },
            redrawOnParentResize: false,
            redrawOnWindowResize: true
        },
        colors: ['#198754', '#ffc107', '#dc3545'],
        legend: {
            position: 'bottom'
        },
        dataLabels: { enabled: true }
    };

    const feeStatusChartElem = document.querySelector("#fee-status-chart");
    if (feeStatusChartElem) {
        const feeStatusChart = new ApexCharts(feeStatusChartElem, feeStatusOptions);
        feeStatusChart.render();
    }
});
</script>
@endpush