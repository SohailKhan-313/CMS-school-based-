<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeeInvoiceRequest;
use App\Http\Requests\UpdateFeeInvoiceRequest;
use App\Models\FeeInvoice;
use App\Models\Student;
use App\Services\PdfReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AccountantController extends Controller
{
    public function index(Request $request): View
    {
        $query = FeeInvoice::with(['student.schoolClass', 'creator']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('admission_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('fee_type')) {
            $query->where('fee_type', $request->input('fee_type'));
        }

        $invoices = $query->latest()->paginate(10)->withQueryString();

        $totalBilled = (float) FeeInvoice::sum('total_amount');
        $totalCollected = (float) FeeInvoice::sum('paid_amount');
        $totalPending = max(0, $totalBilled - $totalCollected);

        $students = Student::with('schoolClass')->orderBy('name')->get();

        return view('accountant.index', compact('invoices', 'totalBilled', 'totalCollected', 'totalPending', 'students'));
    }

    public function create(): View
    {
        $students = Student::with('schoolClass')->orderBy('name')->get();

        return view('accountant.create', compact('students'));
    }

    public function store(StoreFeeInvoiceRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        FeeInvoice::create($data);

        return redirect()->route('accountant.index')->with('success', 'Fee invoice generated successfully.');
    }

    public function show(FeeInvoice $invoice): View
    {
        $invoice->load(['student.schoolClass', 'creator']);

        return view('accountant.show', compact('invoice'));
    }

    public function edit(FeeInvoice $invoice): View
    {
        $students = Student::with('schoolClass')->orderBy('name')->get();

        return view('accountant.edit', compact('invoice', 'students'));
    }

    public function update(UpdateFeeInvoiceRequest $request, FeeInvoice $invoice): RedirectResponse
    {
        $invoice->update($request->validated());

        return redirect()->route('accountant.index')->with('success', 'Fee invoice updated successfully.');
    }

    public function destroy(FeeInvoice $invoice): RedirectResponse
    {
        $invoice->delete();

        return redirect()->route('accountant.index')->with('success', 'Fee invoice deleted successfully.');
    }

    /**
     * Print all fee statements / ledger to PDF using FPDF.
     */
    public function exportPdf(PdfReportService $pdfService): Response
    {
        $invoices = FeeInvoice::with(['student.schoolClass'])->latest()->get();
        $pdfService->buildFeeInvoiceListPdf($invoices);

        return $pdfService->toResponse('fee-ledger-statement-'.date('Y-m-d').'.pdf');
    }

    /**
     * Print single official fee challan / receipt to PDF using FPDF.
     */
    public function printChallan(FeeInvoice $invoice, PdfReportService $pdfService): Response
    {
        $invoice->load(['student.schoolClass', 'creator']);
        $pdfService->buildFeeChallanPdf($invoice);

        return $pdfService->toResponse('fee-challan-'.$invoice->invoice_number.'.pdf');
    }
}
