<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolClassRequest;
use App\Http\Requests\UpdateSchoolClassRequest;
use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Services\PdfReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    public function index(Request $request): View
    {
        $query = SchoolClass::with('teacher')->withCount('students');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('section', 'like', "%{$search}%")
                    ->orWhere('room_number', 'like', "%{$search}%");
            });
        }

        $classes = $query->orderBy('name')->paginate(10)->withQueryString();
        $teachers = Teacher::where('status', 'active')->orderBy('name')->get();

        return view('classes.index', compact('classes', 'teachers'));
    }

    public function create(): View
    {
        $teachers = Teacher::where('status', 'active')->orderBy('name')->get();

        return view('classes.create', compact('teachers'));
    }

    public function store(StoreSchoolClassRequest $request): RedirectResponse
    {
        SchoolClass::create($request->validated());

        return redirect()->route('classes.index')->with('success', 'Class created successfully.');
    }

    public function show(SchoolClass $class): View
    {
        $class->load(['teacher', 'students']);

        return view('classes.show', compact('class'));
    }

    public function edit(SchoolClass $class): View
    {
        $teachers = Teacher::where('status', 'active')->orderBy('name')->get();

        return view('classes.edit', compact('class', 'teachers'));
    }

    public function update(UpdateSchoolClassRequest $request, SchoolClass $class): RedirectResponse
    {
        $class->update($request->validated());

        return redirect()->route('classes.index')->with('success', 'Class updated successfully.');
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        $class->delete();

        return redirect()->route('classes.index')->with('success', 'Class deleted successfully.');
    }

    /**
     * Print all classes roster to PDF using FPDF.
     */
    public function exportPdf(PdfReportService $pdfService): Response
    {
        $classes = SchoolClass::with('teacher')->orderBy('name')->get();
        $pdfService->buildClassListPdf($classes);

        return $pdfService->toResponse('classes-roster-'.date('Y-m-d').'.pdf');
    }
}
