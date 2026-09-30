<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\PdfReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Student::with(['schoolClass', 'feeInvoices']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('admission_number', 'like', "%{$search}%")
                    ->orWhere('roll_number', 'like', "%{$search}%")
                    ->orWhere('guardian_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('class_id')) {
            $query->where('school_class_id', $request->input('class_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $students = $query->latest()->paginate(10)->withQueryString();
        $classes = SchoolClass::orderBy('name')->get();

        return view('students.index', compact('students', 'classes'));
    }

    public function create(): View
    {
        $classes = SchoolClass::orderBy('name')->get();

        return view('students.create', compact('classes'));
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('students', 'public');
        }

        Student::create($data);

        return redirect()->route('students.index')->with('success', 'Student enrolled successfully.');
    }

    public function show(Student $student): View
    {
        $student->load(['schoolClass.teacher', 'feeInvoices']);

        return view('students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        $classes = SchoolClass::orderBy('name')->get();

        return view('students.edit', compact('student', 'classes'));
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($student->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($student->photo);
            }
            $data['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student->update($data);

        return redirect()->route('students.index')->with('success', 'Student details updated successfully.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        if ($student->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->photo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($student->photo);
        }

        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student removed successfully.');
    }

    /**
     * Print all students list to PDF using FPDF.
     */
    public function exportPdf(PdfReportService $pdfService): Response
    {
        $students = Student::with('schoolClass')->orderBy('name')->get();
        $pdfService->buildStudentListPdf($students);

        return $pdfService->toResponse('students-directory-'.date('Y-m-d').'.pdf');
    }

    /**
     * Print single student admission slip / ID card using FPDF.
     */
    public function printSlip(Student $student, PdfReportService $pdfService): Response
    {
        $student->load(['schoolClass', 'feeInvoices']);
        $pdfService->buildStudentSlipPdf($student);

        return $pdfService->toResponse('student-slip-'.$student->admission_number.'.pdf');
    }
}
