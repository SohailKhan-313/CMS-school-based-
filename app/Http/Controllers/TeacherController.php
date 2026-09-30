<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\Teacher;
use App\Services\PdfReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        $query = Teacher::with('schoolClasses');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('employee_code', 'like', "%{$search}%")
                    ->orWhere('specialization', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $teachers = $query->latest()->paginate(10)->withQueryString();

        return view('teachers.index', compact('teachers'));
    }

    public function create(): View
    {
        return view('teachers.create');
    }

    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('teachers', 'public');
        }

        Teacher::create($data);

        return redirect()->route('teachers.index')->with('success', 'Teacher added successfully.');
    }

    public function show(Teacher $teacher): View
    {
        $teacher->load(['schoolClasses.students', 'user']);

        return view('teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher): View
    {
        return view('teachers.edit', compact('teacher'));
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($teacher->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($teacher->photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($teacher->photo);
            }
            $data['photo'] = $request->file('photo')->store('teachers', 'public');
        }

        $teacher->update($data);

        return redirect()->route('teachers.index')->with('success', 'Teacher details updated successfully.');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        if ($teacher->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($teacher->photo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($teacher->photo);
        }

        $teacher->delete();

        return redirect()->route('teachers.index')->with('success', 'Teacher removed successfully.');
    }

    /**
     * Print all teachers directory to PDF using FPDF.
     */
    public function exportPdf(PdfReportService $pdfService): Response
    {
        $teachers = Teacher::with('schoolClasses')->orderBy('name')->get();
        $pdfService->buildTeacherListPdf($teachers);

        return $pdfService->toResponse('teachers-directory-'.date('Y-m-d').'.pdf');
    }

    /**
     * Print single teacher profile sheet to PDF using FPDF.
     */
    public function printProfile(Teacher $teacher, PdfReportService $pdfService): Response
    {
        $teacher->load(['schoolClasses.students', 'user']);
        $pdfService->buildTeacherProfilePdf($teacher);

        return $pdfService->toResponse('teacher-profile-'.$teacher->employee_code.'.pdf');
    }
}
