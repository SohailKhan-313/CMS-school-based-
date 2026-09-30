<?php

namespace App\Http\Controllers;

use App\Models\FeeInvoice;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Interconnected Model Summaries
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 'active')->count();
        $totalTeachers = Teacher::count();
        $activeTeachers = Teacher::where('status', 'active')->count();
        $totalClasses = SchoolClass::count();
        $totalUsers = User::count();

        // Financial Metrics
        $totalBilled = (float) FeeInvoice::sum('total_amount');
        $totalCollected = (float) FeeInvoice::sum('paid_amount');
        $totalOutstanding = max(0, $totalBilled - $totalCollected);
        $collectionRate = $totalBilled > 0 ? round(($totalCollected / $totalBilled) * 100, 1) : 0;

        // Class Enrollment Breakdown (for ApexChart) with Natural Grade Ordering
        $classesDistribution = SchoolClass::withCount('students')
            ->get()
            ->sortBy(function ($class) {
                $nameLower = strtolower($class->name);
                if (str_contains($nameLower, 'nursery')) {
                    $order = 0;
                } elseif (str_contains($nameLower, 'prep') || str_contains($nameLower, 'kg') || str_contains($nameLower, 'kindergarten')) {
                    $order = 1;
                } elseif (preg_match('/\d+/', $class->name, $matches)) {
                    $order = ((int) $matches[0]) + 2;
                } else {
                    $order = 999;
                }

                return sprintf('%04d-%s-%s', $order, $class->name, $class->section);
            })
            ->values();

        $classLabels = $classesDistribution->map(fn ($c) => $c->full_name)->toArray();
        $classStudentCounts = $classesDistribution->pluck('students_count')->toArray();

        // Active Faculty for Modal Selection
        $teachers = Teacher::where('status', 'active')->orderBy('name')->get();

        // Fee Status Breakdown (for Pie/Donut Chart)
        $paidInvoicesCount = FeeInvoice::where('status', 'paid')->count();
        $partialInvoicesCount = FeeInvoice::where('status', 'partial')->count();
        $unpaidInvoicesCount = FeeInvoice::where('status', 'unpaid')->count();

        // Recent Records
        $recentStudents = Student::with('schoolClass')->latest()->take(5)->get();
        $recentInvoices = FeeInvoice::with(['student.schoolClass'])->latest()->take(5)->get();
        $activeFaculty = Teacher::withCount('schoolClasses')->latest()->take(4)->get();

        return view('dashboard', compact(
            'totalStudents',
            'activeStudents',
            'totalTeachers',
            'activeTeachers',
            'totalClasses',
            'totalUsers',
            'totalBilled',
            'totalCollected',
            'totalOutstanding',
            'collectionRate',
            'classLabels',
            'classStudentCounts',
            'teachers',
            'paidInvoicesCount',
            'partialInvoicesCount',
            'unpaidInvoicesCount',
            'recentStudents',
            'recentInvoices',
            'activeFaculty'
        ));
    }
}
