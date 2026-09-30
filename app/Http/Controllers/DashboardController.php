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

        // Class Enrollment Breakdown (for ApexChart)
        $classesDistribution = SchoolClass::withCount('students')->orderBy('name')->get();
        $classLabels = $classesDistribution->pluck('full_name')->toArray();
        $classStudentCounts = $classesDistribution->pluck('students_count')->toArray();

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
            'paidInvoicesCount',
            'partialInvoicesCount',
            'unpaidInvoicesCount',
            'recentStudents',
            'recentInvoices',
            'activeFaculty'
        ));
    }
}
