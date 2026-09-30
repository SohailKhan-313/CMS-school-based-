<?php

namespace App\Http\Controllers;

use App\Models\FeeInvoice;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Services\PdfReportService;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index(): View
    {
        $usersCount = User::count();
        $rolesCount = Role::count();
        $permissionsCount = Permission::count();
        $studentsCount = Student::count();
        $teachersCount = Teacher::count();
        $classesCount = SchoolClass::count();

        $recentUsers = User::with('roles')->latest()->take(5)->get();
        $recentTeachers = Teacher::latest()->take(5)->get();
        $recentInvoices = FeeInvoice::with('student')->latest()->take(5)->get();

        return view('admin.index', compact(
            'usersCount',
            'rolesCount',
            'permissionsCount',
            'studentsCount',
            'teachersCount',
            'classesCount',
            'recentUsers',
            'recentTeachers',
            'recentInvoices'
        ));
    }

    public function exportUsersPdf(PdfReportService $pdfService): Response
    {
        $users = User::with('roles')->orderBy('name')->get();
        $pdfService->buildUserListPdf($users);

        return $pdfService->toResponse('users-directory-'.date('Y-m-d').'.pdf');
    }
}
