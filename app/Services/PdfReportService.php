<?php

namespace App\Services;

use App\Models\FeeInvoice;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use FPDF;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class PdfReportService extends FPDF
{
    protected string $reportTitle = 'School Management Report';

    protected string $reportSubtitle = '';

    public function setReportMeta(string $title, string $subtitle = ''): void
    {
        $this->reportTitle = $title;
        $this->reportSubtitle = $subtitle;
    }

    public function Header(): void
    {
        // Top accent bar
        $this->SetFillColor(24, 43, 73); // Deep Navy
        $this->Rect(0, 0, $this->GetPageWidth(), 6, 'F');

        $this->SetY(12);
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(24, 43, 73);
        $this->Cell(0, 7, 'EXCELLENCE ACADEMY', 0, 1, 'L');

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 4, 'Comprehensive School Management System | Institutional Document', 0, 1, 'L');

        $this->SetY(12);
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(40, 40, 40);
        $this->Cell(0, 7, strtoupper($this->reportTitle), 0, 1, 'R');

        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(120, 120, 120);
        $dateStr = 'Generated: '.date('d M Y, h:i A');
        if ($this->reportSubtitle !== '') {
            $dateStr = $this->reportSubtitle.' | '.$dateStr;
        }
        $this->Cell(0, 4, $dateStr, 0, 1, 'R');

        $this->Ln(3);
        $this->SetDrawColor(210, 215, 225);
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY(), $this->GetPageWidth() - 10, $this->GetY());
        $this->Ln(5);
    }

    public function Footer(): void
    {
        $this->SetY(-15);
        $this->SetDrawColor(220, 220, 220);
        $this->SetLineWidth(0.3);
        $this->Line(10, $this->GetPageHeight() - 16, $this->GetPageWidth() - 10, $this->GetPageHeight() - 16);

        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, 'Excellence Academy CMS - Confidential & Official Record', 0, 0, 'L');
        $this->Cell(0, 10, 'Page '.$this->PageNo().' / {nb}', 0, 0, 'R');
    }

    /**
     * Render table header row.
     *
     * @param  array<string>  $headers
     * @param  array<int>  $widths
     * @param  array<string>  $aligns
     */
    protected function renderTableHeader(array $headers, array $widths, array $aligns): void
    {
        $this->SetFillColor(240, 243, 248);
        $this->SetTextColor(24, 43, 73);
        $this->SetFont('Arial', 'B', 9);
        $this->SetDrawColor(200, 205, 215);

        foreach ($headers as $i => $header) {
            $this->Cell($widths[$i], 8, ' '.$header, 1, 0, $aligns[$i] ?? 'L', true);
        }
        $this->Ln();
    }

    /**
     * Stream / Download PDF response.
     */
    public function toResponse(string $filename): Response
    {
        $this->AliasNbPages();
        $content = $this->Output('S');

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    // ==========================================
    // 1. STUDENTS LIST & SLIP
    // ==========================================

    /**
     * @param  Collection<int, Student>  $students
     */
    public function buildStudentListPdf(Collection $students): void
    {
        $this->setReportMeta('Students Directory', 'Total: '.$students->count().' Enrolled');
        $this->AddPage('L'); // Landscape

        $headers = ['#', 'Adm No', 'Roll', 'Student Name', 'Class', 'Gender', 'Guardian', 'Phone', 'Status'];
        $widths = [10,  28,       18,     55,             30,      18,       45,         40,      23];
        $aligns = ['C', 'C',      'C',    'L',            'L',     'C',      'L',        'L',     'C'];

        $this->renderTableHeader($headers, $widths, $aligns);

        $this->SetFont('Arial', '', 8);
        $fill = false;
        foreach ($students as $index => $student) {
            $this->SetFillColor($fill ? 249 : 255, $fill ? 250 : 255, $fill ? 252 : 255);
            $this->SetTextColor(50, 50, 50);

            $this->Cell($widths[0], 7, (string) ($index + 1), 1, 0, 'C', true);
            $this->Cell($widths[1], 7, $student->admission_number, 1, 0, 'C', true);
            $this->Cell($widths[2], 7, $student->roll_number, 1, 0, 'C', true);
            $this->Cell($widths[3], 7, ' '.$student->name, 1, 0, 'L', true);
            $this->Cell($widths[4], 7, ' '.($student->schoolClass ? $student->schoolClass->full_name : 'N/A'), 1, 0, 'L', true);
            $this->Cell($widths[5], 7, ucfirst($student->gender ?? 'N/A'), 1, 0, 'C', true);
            $this->Cell($widths[6], 7, ' '.($student->guardian_name ?? '-'), 1, 0, 'L', true);
            $this->Cell($widths[7], 7, ' '.($student->guardian_phone ?? $student->phone ?? '-'), 1, 0, 'L', true);

            // Status
            if ($student->status === 'active') {
                $this->SetTextColor(22, 101, 52);
            } else {
                $this->SetTextColor(153, 27, 27);
            }
            $this->Cell($widths[8], 7, ucfirst($student->status), 1, 0, 'C', true);
            $this->Ln();

            $fill = ! $fill;
        }
    }

    public function buildStudentSlipPdf(Student $student): void
    {
        $this->setReportMeta('Student Profile & Admission Card', $student->admission_number);
        $this->AddPage('P'); // Portrait

        $cardTopY = $this->GetY();

        // Student Card Box
        $this->SetFillColor(248, 249, 252);
        $this->SetDrawColor(200, 205, 215);
        $this->Rect(10, $cardTopY, 190, 85, 'DF');

        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(24, 43, 73);
        $this->SetXY(15, $cardTopY + 4);
        $this->Cell(120, 8, $student->name, 0, 1, 'L');

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->SetX(15);
        $this->Cell(120, 5, 'Admission #: '.$student->admission_number.' | Roll #: '.$student->roll_number, 0, 1, 'L');

        // Status Badge Box
        $this->SetXY(145, $this->GetY() - 12);
        $this->SetFillColor($student->status === 'active' ? 220 : 254, $student->status === 'active' ? 252 : 226, $student->status === 'active' ? 231 : 226);
        $this->SetTextColor($student->status === 'active' ? 22 : 153, $student->status === 'active' ? 101 : 27, $student->status === 'active' ? 52 : 27);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(50, 8, 'STATUS: '.strtoupper($student->status), 1, 1, 'C', true);

        // Photo if exists
        if ($student->photo) {
            $photoPath = storage_path('app/public/'.$student->photo);
            if (file_exists($photoPath)) {
                $this->Image($photoPath, 168, $cardTopY + 16, 26, 26);
            }
        }

        $this->Ln(6);

        // Academic & Personal Details Table
        $this->SetX(15);
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(60, 60, 60);

        $details = [
            ['Class / Section:', $student->schoolClass ? $student->schoolClass->full_name : 'Not Assigned', 'Gender:', ucfirst($student->gender ?? 'N/A')],
            ['Date of Birth:', $student->date_of_birth ? $student->date_of_birth->format('d M Y') : 'N/A', 'Admission Date:', $student->admission_date ? $student->admission_date->format('d M Y') : 'N/A'],
            ['Student Email:', $student->email ?? 'N/A', 'Student Phone:', $student->phone ?? 'N/A'],
            ['Guardian Name:', $student->guardian_name ?? 'N/A', 'Relationship:', $student->guardian_relation ?? 'N/A'],
            ['Guardian Phone:', $student->guardian_phone ?? 'N/A', 'Home Address:', $student->address ?? 'N/A'],
        ];

        foreach ($details as $row) {
            $this->SetX(15);
            $this->SetFont('Arial', 'B', 9);
            $this->SetTextColor(80, 80, 80);
            $this->Cell(35, 6, $row[0], 0, 0, 'L');
            $this->SetFont('Arial', '', 9);
            $this->SetTextColor(20, 20, 20);
            $this->Cell(55, 6, $row[1], 0, 0, 'L');

            $this->SetFont('Arial', 'B', 9);
            $this->SetTextColor(80, 80, 80);
            $this->Cell(35, 6, $row[2], 0, 0, 'L');
            $this->SetFont('Arial', '', 9);
            $this->SetTextColor(20, 20, 20);
            $this->Cell(55, 6, $row[3], 0, 1, 'L');
        }

        $this->SetY($this->GetY() + 15);

        // Fee Summary Box for this student
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(24, 43, 73);
        $this->Cell(0, 8, 'Financial & Fee Invoices History', 0, 1, 'L');

        $headers = ['Invoice #', 'Fee Title', 'Total ($)', 'Paid ($)', 'Balance ($)', 'Due Date', 'Status'];
        $widths = [30,         55,          22,          22,         22,            24,         15];
        $aligns = ['C',        'L',         'R',         'R',        'R',           'C',        'C'];

        $this->renderTableHeader($headers, $widths, $aligns);

        $this->SetFont('Arial', '', 8);
        $fill = false;
        foreach ($student->feeInvoices as $inv) {
            $this->SetFillColor($fill ? 249 : 255, $fill ? 250 : 255, $fill ? 252 : 255);
            $this->SetTextColor(40, 40, 40);

            $this->Cell($widths[0], 7, $inv->invoice_number, 1, 0, 'C', true);
            $this->Cell($widths[1], 7, ' '.$inv->title, 1, 0, 'L', true);
            $this->Cell($widths[2], 7, number_format((float) $inv->total_amount, 2), 1, 0, 'R', true);
            $this->Cell($widths[3], 7, number_format((float) $inv->paid_amount, 2), 1, 0, 'R', true);
            $this->Cell($widths[4], 7, number_format((float) $inv->balance, 2), 1, 0, 'R', true);
            $this->Cell($widths[5], 7, $inv->due_date ? $inv->due_date->format('d/m/Y') : '-', 1, 0, 'C', true);

            if ($inv->status === 'paid') {
                $this->SetTextColor(22, 101, 52);
            } elseif ($inv->status === 'partial') {
                $this->SetTextColor(180, 83, 9);
            } else {
                $this->SetTextColor(153, 27, 27);
            }
            $this->Cell($widths[6], 7, ucfirst($inv->status), 1, 0, 'C', true);
            $this->Ln();

            $fill = ! $fill;
        }

        if ($student->feeInvoices->isEmpty()) {
            $this->SetFont('Arial', 'I', 9);
            $this->SetTextColor(120, 120, 120);
            $this->Cell(190, 8, 'No fee records found for this student.', 1, 1, 'C');
        }

        // Signature section
        $this->SetY($this->GetY() + 25);
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(80, 80, 80);
        $this->Cell(60, 4, '__________________________', 0, 0, 'C');
        $this->Cell(70, 4, '', 0, 0, 'C');
        $this->Cell(60, 4, '__________________________', 0, 1, 'C');

        $this->Cell(60, 5, 'Class Teacher Signature', 0, 0, 'C');
        $this->Cell(70, 5, '', 0, 0, 'C');
        $this->Cell(60, 5, 'Principal / Registrar Seal', 0, 1, 'C');
    }

    // ==========================================
    // 2. TEACHERS LIST & PROFILE
    // ==========================================

    /**
     * @param  Collection<int, Teacher>  $teachers
     */
    public function buildTeacherListPdf(Collection $teachers): void
    {
        $this->setReportMeta('Faculty & Teachers Directory', 'Total: '.$teachers->count().' Faculty Members');
        $this->AddPage('L');

        $headers = ['#', 'Code', 'Teacher Name', 'Subject / Specialization', 'Qualification', 'Phone', 'Email', 'Joining Date', 'Status'];
        $widths = [10,  22,     50,             45,                        40,              32,      45,      20,             16];
        $aligns = ['C', 'C',    'L',            'L',                       'L',             'L',     'L',     'C',            'C'];

        $this->renderTableHeader($headers, $widths, $aligns);

        $this->SetFont('Arial', '', 8);
        $fill = false;
        foreach ($teachers as $idx => $teacher) {
            $this->SetFillColor($fill ? 249 : 255, $fill ? 250 : 255, $fill ? 252 : 255);
            $this->SetTextColor(40, 40, 40);

            $this->Cell($widths[0], 7, (string) ($idx + 1), 1, 0, 'C', true);
            $this->Cell($widths[1], 7, $teacher->employee_code, 1, 0, 'C', true);
            $this->Cell($widths[2], 7, ' '.$teacher->name, 1, 0, 'L', true);
            $this->Cell($widths[3], 7, ' '.($teacher->specialization ?? 'General'), 1, 0, 'L', true);
            $this->Cell($widths[4], 7, ' '.($teacher->qualification ?? '-'), 1, 0, 'L', true);
            $this->Cell($widths[5], 7, ' '.($teacher->phone ?? '-'), 1, 0, 'L', true);
            $this->Cell($widths[6], 7, ' '.$teacher->email, 1, 0, 'L', true);
            $this->Cell($widths[7], 7, $teacher->joining_date ? $teacher->joining_date->format('d/m/Y') : '-', 1, 0, 'C', true);

            if ($teacher->status === 'active') {
                $this->SetTextColor(22, 101, 52);
            } else {
                $this->SetTextColor(153, 27, 27);
            }
            $this->Cell($widths[8], 7, ucfirst($teacher->status), 1, 0, 'C', true);
            $this->Ln();

            $fill = ! $fill;
        }
    }

    public function buildTeacherProfilePdf(Teacher $teacher): void
    {
        $this->setReportMeta('Faculty Member Profile Sheet', $teacher->employee_code);
        $this->AddPage('P');

        $cardTopY = $this->GetY();

        // Teacher Info Card Box
        $this->SetFillColor(248, 249, 252);
        $this->SetDrawColor(200, 205, 215);
        $this->Rect(10, $cardTopY, 190, 80, 'DF');

        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(24, 43, 73);
        $this->SetXY(15, $cardTopY + 4);
        $this->Cell(120, 8, $teacher->name, 0, 1, 'L');

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->SetX(15);
        $this->Cell(120, 5, 'Employee Code: '.$teacher->employee_code.' | Department: '.($teacher->specialization ?? 'Academics'), 0, 1, 'L');

        // Status Badge
        $this->SetXY(145, $this->GetY() - 12);
        $this->SetFillColor(220, 252, 231);
        $this->SetTextColor(22, 101, 52);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(50, 8, 'STATUS: '.strtoupper($teacher->status), 1, 1, 'C', true);

        // Photo if exists
        if ($teacher->photo) {
            $photoPath = storage_path('app/public/'.$teacher->photo);
            if (file_exists($photoPath)) {
                $this->Image($photoPath, 168, $cardTopY + 16, 26, 26);
            }
        }

        $this->Ln(8);

        $fields = [
            ['Email Address:', $teacher->email, 'Phone Number:', $teacher->phone ?? 'N/A'],
            ['Qualification:', $teacher->qualification ?? 'N/A', 'Specialization:', $teacher->specialization ?? 'N/A'],
            ['Joining Date:', $teacher->joining_date ? $teacher->joining_date->format('d M Y') : 'N/A', 'Monthly Salary:', '$'.number_format((float) $teacher->salary, 2)],
            ['Office Address:', $teacher->address ?? 'Main Campus', 'System User Account:', $teacher->user ? $teacher->user->email : 'Not Linked'],
        ];

        foreach ($fields as $row) {
            $this->SetX(15);
            $this->SetFont('Arial', 'B', 9);
            $this->SetTextColor(80, 80, 80);
            $this->Cell(35, 6, $row[0], 0, 0, 'L');
            $this->SetFont('Arial', '', 9);
            $this->SetTextColor(20, 20, 20);
            $this->Cell(55, 6, $row[1], 0, 0, 'L');

            $this->SetFont('Arial', 'B', 9);
            $this->SetTextColor(80, 80, 80);
            $this->Cell(35, 6, $row[2], 0, 0, 'L');
            $this->SetFont('Arial', '', 9);
            $this->SetTextColor(20, 20, 20);
            $this->Cell(55, 6, $row[3], 0, 1, 'L');
        }

        $this->SetY($this->GetY() + 15);

        // Assigned Classes
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(24, 43, 73);
        $this->Cell(0, 8, 'Assigned Classes & Academic Duties', 0, 1, 'L');

        $headers = ['Class Name', 'Section', 'Room #', 'Capacity', 'Enrolled Students'];
        $widths = [50,         30,        30,       30,         50];
        $aligns = ['L',        'C',       'C',      'C',        'C'];

        $this->renderTableHeader($headers, $widths, $aligns);

        $this->SetFont('Arial', '', 9);
        $fill = false;
        foreach ($teacher->schoolClasses as $c) {
            $this->SetFillColor($fill ? 249 : 255, $fill ? 250 : 255, $fill ? 252 : 255);
            $this->SetTextColor(40, 40, 40);

            $this->Cell($widths[0], 8, ' '.$c->name, 1, 0, 'L', true);
            $this->Cell($widths[1], 8, $c->section, 1, 0, 'C', true);
            $this->Cell($widths[2], 8, $c->room_number ?? '-', 1, 0, 'C', true);
            $this->Cell($widths[3], 8, (string) $c->capacity, 1, 0, 'C', true);
            $this->Cell($widths[4], 8, (string) $c->students()->count(), 1, 0, 'C', true);
            $this->Ln();

            $fill = ! $fill;
        }

        if ($teacher->schoolClasses->isEmpty()) {
            $this->SetFont('Arial', 'I', 9);
            $this->SetTextColor(120, 120, 120);
            $this->Cell(190, 8, 'No specific classes assigned as Class Teacher.', 1, 1, 'C');
        }
    }

    // ==========================================
    // 3. FEE INVOICES & CHALLAN RECEIPT
    // ==========================================

    /**
     * @param  Collection<int, FeeInvoice>  $invoices
     */
    public function buildFeeInvoiceListPdf(Collection $invoices): void
    {
        $this->setReportMeta('Fees & Accounts Statement', 'Total: '.$invoices->count().' Invoices');
        $this->AddPage('L');

        $headers = ['#', 'Invoice #', 'Student Name', 'Class', 'Fee Title', 'Total ($)', 'Paid ($)', 'Balance ($)', 'Due Date', 'Status'];
        $widths = [10,  28,          50,             25,      50,          22,          22,         22,            25,         26];
        $aligns = ['C', 'C',         'L',            'L',     'L',         'R',         'R',        'R',           'C',        'C'];

        $this->renderTableHeader($headers, $widths, $aligns);

        $this->SetFont('Arial', '', 8);
        $fill = false;
        $totalSum = 0;
        $paidSum = 0;

        foreach ($invoices as $idx => $inv) {
            $this->SetFillColor($fill ? 249 : 255, $fill ? 250 : 255, $fill ? 252 : 255);
            $this->SetTextColor(40, 40, 40);

            $totalSum += (float) $inv->total_amount;
            $paidSum += (float) $inv->paid_amount;

            $this->Cell($widths[0], 7, (string) ($idx + 1), 1, 0, 'C', true);
            $this->Cell($widths[1], 7, $inv->invoice_number, 1, 0, 'C', true);
            $this->Cell($widths[2], 7, ' '.($inv->student ? $inv->student->name : 'N/A'), 1, 0, 'L', true);
            $this->Cell($widths[3], 7, ' '.($inv->student && $inv->student->schoolClass ? $inv->student->schoolClass->name.'-'.$inv->student->schoolClass->section : '-'), 1, 0, 'L', true);
            $this->Cell($widths[4], 7, ' '.$inv->title, 1, 0, 'L', true);
            $this->Cell($widths[5], 7, number_format((float) $inv->total_amount, 2), 1, 0, 'R', true);
            $this->Cell($widths[6], 7, number_format((float) $inv->paid_amount, 2), 1, 0, 'R', true);
            $this->Cell($widths[7], 7, number_format((float) $inv->balance, 2), 1, 0, 'R', true);
            $this->Cell($widths[8], 7, $inv->due_date ? $inv->due_date->format('d/m/Y') : '-', 1, 0, 'C', true);

            if ($inv->status === 'paid') {
                $this->SetTextColor(22, 101, 52);
            } elseif ($inv->status === 'partial') {
                $this->SetTextColor(180, 83, 9);
            } else {
                $this->SetTextColor(153, 27, 27);
            }
            $this->Cell($widths[9], 7, ucfirst($inv->status), 1, 0, 'C', true);
            $this->Ln();

            $fill = ! $fill;
        }

        // Summary Total Row
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(230, 235, 245);
        $this->SetTextColor(24, 43, 73);
        $this->Cell(163, 8, ' GRAND TOTALS: ', 1, 0, 'R', true);
        $this->Cell(22, 8, '$'.number_format($totalSum, 2), 1, 0, 'R', true);
        $this->Cell(22, 8, '$'.number_format($paidSum, 2), 1, 0, 'R', true);
        $this->Cell(22, 8, '$'.number_format(max(0, $totalSum - $paidSum), 2), 1, 0, 'R', true);
        $this->Cell(51, 8, '', 1, 1, 'C', true);
    }

    public function buildFeeChallanPdf(FeeInvoice $invoice): void
    {
        $this->setReportMeta('Official Fee Challan & Payment Receipt', $invoice->invoice_number);
        $this->AddPage('P');

        // Challan Outer Box
        $this->SetFillColor(252, 253, 255);
        $this->SetDrawColor(180, 190, 205);
        $this->SetLineWidth(0.4);
        $this->Rect(10, $this->GetY(), 190, 120, 'DF');

        $this->SetY($this->GetY() + 4);
        $this->SetFont('Arial', 'B', 15);
        $this->SetTextColor(24, 43, 73);
        $this->Cell(0, 7, 'EXCELLENCE ACADEMY - STUDENT FEE CHALLAN', 0, 1, 'C');

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 5, 'Accounts & Finance Branch | Student Copy & Office Record', 0, 1, 'C');

        $this->Ln(4);
        $this->Line(15, $this->GetY(), 195, $this->GetY());
        $this->Ln(4);

        // Challan Meta
        $student = $invoice->student;
        $meta = [
            ['Invoice No:', $invoice->invoice_number, 'Issue Date:', $invoice->created_at ? $invoice->created_at->format('d M Y') : date('d M Y')],
            ['Student Name:', $student ? $student->name : 'N/A', 'Admission No:', $student ? $student->admission_number : 'N/A'],
            ['Class / Section:', $student && $student->schoolClass ? $student->schoolClass->full_name : 'N/A', 'Roll No:', $student ? $student->roll_number : 'N/A'],
            ['Due Date:', $invoice->due_date ? $invoice->due_date->format('d M Y') : 'N/A', 'Payment Method:', ucfirst($invoice->payment_method ?? 'Cash/Bank')],
        ];

        foreach ($meta as $row) {
            $this->SetX(15);
            $this->SetFont('Arial', 'B', 9);
            $this->SetTextColor(70, 70, 70);
            $this->Cell(35, 6, $row[0], 0, 0, 'L');
            $this->SetFont('Arial', '', 9);
            $this->SetTextColor(20, 20, 20);
            $this->Cell(55, 6, $row[1], 0, 0, 'L');

            $this->SetFont('Arial', 'B', 9);
            $this->SetTextColor(70, 70, 70);
            $this->Cell(35, 6, $row[2], 0, 0, 'L');
            $this->SetFont('Arial', '', 9);
            $this->SetTextColor(20, 20, 20);
            $this->Cell(55, 6, $row[3], 0, 1, 'L');
        }

        $this->Ln(4);

        // Breakdown Table
        $this->SetX(15);
        $headers = ['Item Description', 'Fee Category', 'Amount ($)'];
        $widths = [100,                45,             35];
        $aligns = ['L',                 'C',            'R'];

        $this->renderTableHeader($headers, $widths, $aligns);

        $this->SetX(15);
        $this->SetFont('Arial', '', 9);
        $this->Cell(100, 8, ' '.$invoice->title, 1, 0, 'L');
        $this->Cell(45, 8, ucfirst($invoice->fee_type), 1, 0, 'C');
        $this->Cell(35, 8, number_format((float) $invoice->total_amount, 2).' ', 1, 1, 'R');

        if ($invoice->notes) {
            $this->SetX(15);
            $this->SetFont('Arial', 'I', 8);
            $this->SetTextColor(100, 100, 100);
            $this->Cell(180, 6, ' Notes: '.$invoice->notes, 1, 1, 'L');
        }

        // Calculations rows
        $this->SetX(15);
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(245, 247, 250);
        $this->Cell(145, 7, ' Total Billed: ', 1, 0, 'R', true);
        $this->Cell(35, 7, '$'.number_format((float) $invoice->total_amount, 2).' ', 1, 1, 'R', true);

        $this->SetX(15);
        $this->SetTextColor(22, 101, 52);
        $this->Cell(145, 7, ' Total Paid: ', 1, 0, 'R', true);
        $this->Cell(35, 7, '$'.number_format((float) $invoice->paid_amount, 2).' ', 1, 1, 'R', true);

        $this->SetX(15);
        $this->SetTextColor($invoice->balance > 0 ? 153 : 22, $invoice->balance > 0 ? 27 : 101, $invoice->balance > 0 ? 27 : 52);
        $this->Cell(145, 7, ' Net Outstanding Balance: ', 1, 0, 'R', true);
        $this->Cell(35, 7, '$'.number_format((float) $invoice->balance, 2).' ', 1, 1, 'R', true);

        $this->SetY($this->GetY() + 8);
        $this->SetX(15);
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor($invoice->status === 'paid' ? 22 : 153, $invoice->status === 'paid' ? 101 : 27, $invoice->status === 'paid' ? 52 : 27);
        $this->Cell(60, 8, 'PAYMENT STATUS: '.strtoupper($invoice->status), 1, 1, 'C');

        // Stamp and Signature
        $this->SetY($this->GetY() + 25);
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(70, 70, 70);
        $this->Cell(60, 4, '__________________________', 0, 0, 'C');
        $this->Cell(70, 4, '', 0, 0, 'C');
        $this->Cell(60, 4, '__________________________', 0, 1, 'C');

        $this->Cell(60, 5, 'Cashier / Accountant', 0, 0, 'C');
        $this->Cell(70, 5, '', 0, 0, 'C');
        $this->Cell(60, 5, 'Authorized Bank Officer', 0, 1, 'C');
    }

    // ==========================================
    // 4. CLASSES & USERS LISTS
    // ==========================================

    /**
     * @param  Collection<int, SchoolClass>  $classes
     */
    public function buildClassListPdf(Collection $classes): void
    {
        $this->setReportMeta('Academic Classes Roster', 'Total: '.$classes->count().' Classes');
        $this->AddPage('P');

        $headers = ['#', 'Class Name', 'Section', 'Room', 'Class Teacher', 'Capacity', 'Enrolled'];
        $widths = [10,  40,           25,        25,     50,              20,         20];
        $aligns = ['C', 'L',          'C',       'C',    'L',             'C',        'C'];

        $this->renderTableHeader($headers, $widths, $aligns);

        $this->SetFont('Arial', '', 9);
        $fill = false;
        foreach ($classes as $idx => $class) {
            $this->SetFillColor($fill ? 249 : 255, $fill ? 250 : 255, $fill ? 252 : 255);
            $this->SetTextColor(40, 40, 40);

            $this->Cell($widths[0], 7, (string) ($idx + 1), 1, 0, 'C', true);
            $this->Cell($widths[1], 7, ' '.$class->name, 1, 0, 'L', true);
            $this->Cell($widths[2], 7, $class->section, 1, 0, 'C', true);
            $this->Cell($widths[3], 7, $class->room_number ?? '-', 1, 0, 'C', true);
            $this->Cell($widths[4], 7, ' '.($class->teacher ? $class->teacher->name : 'Unassigned'), 1, 0, 'L', true);
            $this->Cell($widths[5], 7, (string) $class->capacity, 1, 0, 'C', true);
            $this->Cell($widths[6], 7, (string) $class->students()->count(), 1, 0, 'C', true);
            $this->Ln();

            $fill = ! $fill;
        }
    }

    /**
     * @param  Collection<int, User>  $users
     */
    public function buildUserListPdf(Collection $users): void
    {
        $this->setReportMeta('System Users & Role Privileges', 'Total: '.$users->count().' Registered Users');
        $this->AddPage('P');

        $headers = ['#', 'User Name', 'Email Address', 'Assigned Roles', 'Registered On'];
        $widths = [12,  55,          65,              35,               23];
        $aligns = ['C', 'L',          'L',             'L',              'C'];

        $this->renderTableHeader($headers, $widths, $aligns);

        $this->SetFont('Arial', '', 9);
        $fill = false;
        foreach ($users as $idx => $user) {
            $this->SetFillColor($fill ? 249 : 255, $fill ? 250 : 255, $fill ? 252 : 255);
            $this->SetTextColor(40, 40, 40);

            $roles = $user->roles->pluck('name')->implode(', ');

            $this->Cell($widths[0], 7, (string) ($idx + 1), 1, 0, 'C', true);
            $this->Cell($widths[1], 7, ' '.$user->name, 1, 0, 'L', true);
            $this->Cell($widths[2], 7, ' '.$user->email, 1, 0, 'L', true);
            $this->Cell($widths[3], 7, ' '.($roles ?: 'None'), 1, 0, 'L', true);
            $this->Cell($widths[4], 7, $user->created_at ? $user->created_at->format('d/m/Y') : '-', 1, 0, 'C', true);
            $this->Ln();

            $fill = ! $fill;
        }
    }
}
