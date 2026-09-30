<?php

use App\Models\FeeInvoice;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    $permissions = [
        'show student', 'create student', 'edit student', 'delete student', 'print student',
        'show teacher', 'create teacher', 'edit teacher', 'delete teacher', 'print teacher',
        'show accountant', 'create fee', 'edit fee', 'delete fee', 'print fee',
        'show class', 'create class', 'edit class', 'delete class', 'print class',
        'show admin',
        'see roles', 'create roles', 'edit roles', 'delete roles',
        'see permissions', 'create permissions', 'edit permissions', 'delete permissions',
        'see users', 'create users', 'edit users', 'delete users',
    ];
    foreach ($permissions as $perm) {
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    $this->adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $this->adminRole->syncPermissions(\Spatie\Permission\Models\Permission::all());

    $this->admin = User::firstOrCreate(
        ['email' => 'admin_test@school.com'],
        ['name' => 'Admin Tester', 'password' => bcrypt('password')]
    );
    $this->admin->syncRoles([$this->adminRole]);
    $this->actingAs($this->admin);
});

test('dashboard displays interconnected stats and charts', function () {
    $response = $this->get(route('home'));

    $response->assertStatus(200);
    $response->assertSee('Institutional Dashboard');
    $response->assertSee('Total Students');
    $response->assertSee('Faculty Members');
    $response->assertSee('Fees Collected');
});

test('students module has full CRUD and FPDF generation', function () {
    $teacher = Teacher::create([
        'name' => 'Initial Teacher',
        'employee_code' => 'TCH-001',
        'email' => 'init.teacher@school.com',
        'status' => 'active',
    ]);

    $class = SchoolClass::create([
        'name' => 'Grade 8',
        'section' => 'A',
        'capacity' => 30,
        'teacher_id' => $teacher->id,
    ]);

    // 1. Index
    $this->get(route('students.index'))->assertStatus(200)->assertSee('Students Management');

    // 2. Create View
    $this->get(route('students.create'))->assertStatus(200)->assertSee('Enroll New Student');

    // 3. Store
    $storeData = [
        'name' => 'Test Student John',
        'admission_number' => 'ADM-TEST-999',
        'roll_number' => '08A-99',
        'school_class_id' => $class->id,
        'gender' => 'male',
        'email' => 'john.test@example.com',
        'phone' => '1234567890',
        'guardian_name' => 'Mr. John Sr',
        'guardian_phone' => '0987654321',
        'guardian_relation' => 'Father',
        'status' => 'active',
    ];
    $this->post(route('students.store'), $storeData)->assertRedirect(route('students.index'));

    $student = Student::where('admission_number', 'ADM-TEST-999')->firstOrFail();
    expect($student->name)->toBe('Test Student John');

    // 4. Show
    $this->get(route('students.show', $student->id))->assertStatus(200)->assertSee('Student Profile');

    // 5. Edit View
    $this->get(route('students.edit', $student->id))->assertStatus(200)->assertSee('Edit Student');

    // 6. Update
    $updateData = array_merge($storeData, ['name' => 'Test Student John Updated']);
    $this->put(route('students.update', $student->id), $updateData)->assertRedirect(route('students.index'));
    expect($student->fresh()->name)->toBe('Test Student John Updated');

    // 7. FPDF All Students Directory
    $pdfListResponse = $this->get(route('students.pdf'));
    $pdfListResponse->assertStatus(200);
    $pdfListResponse->assertHeader('Content-Type', 'application/pdf');

    // 8. FPDF Single Student Slip
    $pdfSlipResponse = $this->get(route('students.slip', $student->id));
    $pdfSlipResponse->assertStatus(200);
    $pdfSlipResponse->assertHeader('Content-Type', 'application/pdf');

    // 9. Destroy
    $this->delete(route('students.destroy', $student->id))->assertRedirect(route('students.index'));
    expect(Student::where('admission_number', 'ADM-TEST-999')->exists())->toBeFalse();
});

test('teachers module has full CRUD and FPDF generation', function () {
    // 1. Index
    $this->get(route('teachers.index'))->assertStatus(200)->assertSee('Faculty Members');

    // 2. Create View
    $this->get(route('teachers.create'))->assertStatus(200)->assertSee('Add Faculty Member');

    // 3. Store
    $storeData = [
        'name' => 'Prof. Alan Turing',
        'employee_code' => 'TCH-TEST-888',
        'email' => 'turing@school.com',
        'phone' => '1122334455',
        'qualification' => 'Ph.D. Mathematics',
        'specialization' => 'Computer Science',
        'salary' => 6000.00,
        'joining_date' => '2024-01-01',
        'status' => 'active',
    ];
    $this->post(route('teachers.store'), $storeData)->assertRedirect(route('teachers.index'));

    $teacher = Teacher::where('employee_code', 'TCH-TEST-888')->firstOrFail();

    // 4. Show
    $this->get(route('teachers.show', $teacher->id))->assertStatus(200)->assertSee('Faculty Profile');

    // 5. Edit
    $this->get(route('teachers.edit', $teacher->id))->assertStatus(200)->assertSee('Edit Faculty Member');

    // 6. Update
    $updateData = array_merge($storeData, ['name' => 'Prof. Alan Mathison Turing']);
    $this->put(route('teachers.update', $teacher->id), $updateData)->assertRedirect(route('teachers.index'));
    expect($teacher->fresh()->name)->toBe('Prof. Alan Mathison Turing');

    // 7. FPDF All Teachers Directory
    $pdfList = $this->get(route('teachers.pdf'));
    $pdfList->assertStatus(200)->assertHeader('Content-Type', 'application/pdf');

    // 8. FPDF Single Teacher Profile
    $pdfProfile = $this->get(route('teachers.profile', $teacher->id));
    $pdfProfile->assertStatus(200)->assertHeader('Content-Type', 'application/pdf');

    // 9. Destroy
    $this->delete(route('teachers.destroy', $teacher->id))->assertRedirect(route('teachers.index'));
    expect(Teacher::where('employee_code', 'TCH-TEST-888')->exists())->toBeFalse();
});

test('classes module has full CRUD and FPDF generation', function () {
    // 1. Index
    $this->get(route('classes.index'))->assertStatus(200)->assertSee('Active Classes');

    // 2. Create View
    $this->get(route('classes.create'))->assertStatus(200)->assertSee('Create Class');

    // 3. Store
    $storeData = [
        'name' => 'Grade 11',
        'section' => 'Science',
        'room_number' => 'Lab 3',
        'capacity' => 25,
    ];
    $this->post(route('classes.store'), $storeData)->assertRedirect(route('classes.index'));

    $class = SchoolClass::where('name', 'Grade 11')->where('section', 'Science')->firstOrFail();

    // 4. Show
    $this->get(route('classes.show', $class->id))->assertStatus(200)->assertSee('Grade 11 - Science');

    // 5. Edit
    $this->get(route('classes.edit', $class->id))->assertStatus(200);

    // 6. Update
    $updateData = array_merge($storeData, ['room_number' => 'Lab 4']);
    $this->put(route('classes.update', $class->id), $updateData)->assertRedirect(route('classes.index'));
    expect($class->fresh()->room_number)->toBe('Lab 4');

    // 7. FPDF Classes Roster
    $pdfRoster = $this->get(route('classes.pdf'));
    $pdfRoster->assertStatus(200)->assertHeader('Content-Type', 'application/pdf');

    // 8. Destroy
    $this->delete(route('classes.destroy', $class->id))->assertRedirect(route('classes.index'));
    expect(SchoolClass::where('name', 'Grade 11')->exists())->toBeFalse();
});

test('fees and accountant module has full CRUD and FPDF challan generation', function () {
    $class = SchoolClass::create(['name' => 'Grade 10', 'section' => 'A', 'capacity' => 30]);
    $student = Student::create([
        'school_class_id' => $class->id,
        'admission_number' => 'ADM-FEE-001',
        'roll_number' => '10A-01',
        'name' => 'Fee Student',
        'gender' => 'female',
        'status' => 'active',
    ]);

    // 1. Index
    $this->get(route('accountant.index'))->assertStatus(200)->assertSee('Fee Invoices');

    // 2. Create View
    $this->get(route('accountant.create'))->assertStatus(200)->assertSee('Generate Fee Invoice');

    // 3. Store
    $storeData = [
        'student_id' => $student->id,
        'invoice_number' => 'INV-TEST-777',
        'title' => 'Lab & Activity Fee 2026',
        'fee_type' => 'tuition',
        'total_amount' => 350.00,
        'paid_amount' => 150.00,
        'due_date' => '2026-11-15',
        'status' => 'partial',
        'payment_method' => 'cash',
    ];
    $this->post(route('accountant.store'), $storeData)->assertRedirect(route('accountant.index'));

    $invoice = FeeInvoice::where('invoice_number', 'INV-TEST-777')->firstOrFail();

    // 4. Show
    $this->get(route('accountant.show', $invoice->id))->assertStatus(200)->assertSee('Invoice Details');

    // 5. Edit
    $this->get(route('accountant.edit', $invoice->id))->assertStatus(200)->assertSee('Update Fee Invoice');

    // 6. Update
    $updateData = array_merge($storeData, ['paid_amount' => 350.00, 'status' => 'paid']);
    $this->put(route('accountant.update', $invoice->id), $updateData)->assertRedirect(route('accountant.index'));
    expect($invoice->fresh()->status)->toBe('paid');

    // 7. FPDF All Invoices Ledger
    $pdfLedger = $this->get(route('accountant.pdf'));
    $pdfLedger->assertStatus(200)->assertHeader('Content-Type', 'application/pdf');

    // 8. FPDF Single Official Challan
    $pdfChallan = $this->get(route('accountant.challan', $invoice->id));
    $pdfChallan->assertStatus(200)->assertHeader('Content-Type', 'application/pdf');

    // 9. Destroy
    $this->delete(route('accountant.destroy', $invoice->id))->assertRedirect(route('accountant.index'));
    expect(FeeInvoice::where('invoice_number', 'INV-TEST-777')->exists())->toBeFalse();
});

test('admin center and users report pdf', function () {
    $response = $this->get(route('admin'));
    $response->assertStatus(200);
    $response->assertSee('Administration Center');

    $pdfUsers = $this->get(route('admin.users.pdf'));
    $pdfUsers->assertStatus(200);
    $pdfUsers->assertHeader('Content-Type', 'application/pdf');
});

test('students and faculty photo uploads and modal forms work seamlessly', function () {
    \Illuminate\Support\Facades\Storage::fake('public');

    $teacher = Teacher::create([
        'name' => 'Prof. Photo Teacher',
        'employee_code' => 'TCH-PH-01',
        'email' => 'photo.teacher@school.com',
        'status' => 'active',
    ]);

    $class = SchoolClass::create([
        'name' => 'Class 10',
        'section' => 'B',
        'capacity' => 30,
        'teacher_id' => $teacher->id,
    ]);

    // 1. Verify Modals exist in the blades
    $this->get(route('students.index'))->assertStatus(200)->assertSee('id="createStudentModal"', false);
    $this->get(route('teachers.index'))->assertStatus(200)->assertSee('id="createTeacherModal"', false);
    $this->get(route('classes.index'))->assertStatus(200)->assertSee('id="createClassModal"', false);
    $this->get(route('accountant.index'))->assertStatus(200)->assertSee('id="createInvoiceModal"', false);

    // 2. Student Store with Photo
    $studentPhoto = \Illuminate\Http\UploadedFile::fake()->image('student_avatar.jpg');
    $studentData = [
        'name' => 'Photo Student',
        'admission_number' => 'ADM-PH-001',
        'roll_number' => '10B-01',
        'school_class_id' => $class->id,
        'gender' => 'male',
        'status' => 'active',
        'photo' => $studentPhoto,
    ];
    $this->post(route('students.store'), $studentData)->assertRedirect(route('students.index'));

    $newStudent = Student::where('admission_number', 'ADM-PH-001')->firstOrFail();
    expect($newStudent->photo)->not->toBeNull();
    \Illuminate\Support\Facades\Storage::disk('public')->assertExists($newStudent->photo);

    // 3. Teacher Store with Photo
    $teacherPhoto = \Illuminate\Http\UploadedFile::fake()->image('teacher_avatar.jpg');
    $teacherData = [
        'name' => 'Dr. Uploaded Photo',
        'employee_code' => 'TCH-PH-02',
        'email' => 'dr.photo@school.com',
        'status' => 'active',
        'photo' => $teacherPhoto,
    ];
    $this->post(route('teachers.store'), $teacherData)->assertRedirect(route('teachers.index'));

    $newTeacher = Teacher::where('employee_code', 'TCH-PH-02')->firstOrFail();
    expect($newTeacher->photo)->not->toBeNull();
    \Illuminate\Support\Facades\Storage::disk('public')->assertExists($newTeacher->photo);
});

test('user profile can upload avatar and navbar displays contact icons and conditional notifications', function () {
    \Illuminate\Support\Facades\Storage::fake('public');

    // 1. Profile edit page loads
    $this->get(route('profile.edit'))->assertStatus(200)->assertSee('Profile Picture');

    // 2. Upload avatar
    $avatar = \Illuminate\Http\UploadedFile::fake()->image('profile_pic.png');
    $response = $this->put(route('profile.update'), [
        'name' => 'Updated Admin Name',
        'email' => $this->admin->email,
        'avatar' => $avatar,
    ]);

    $response->assertRedirect(route('profile.edit'));
    $this->admin->refresh();
    expect($this->admin->avatar)->not->toBeNull();
    \Illuminate\Support\Facades\Storage::disk('public')->assertExists($this->admin->avatar);

    // 3. Navbar rendering: WhatsApp, Email
    $navResponse = $this->get(route('home'));
    $navResponse->assertStatus(200);
    $navResponse->assertSee('bi-whatsapp', false);
    $navResponse->assertSee('bi-envelope-fill', false);
    // 4. Notification bell is HIDDEN when there are no new admissions or vouchers
    $navResponse->assertDontSee('bi-bell-fill', false);

    // 5. Create a new student admission -> notification bell appears!
    $class = SchoolClass::create([
        'name' => 'Grade 1',
        'section' => 'A',
        'capacity' => 30,
    ]);

    Student::create([
        'name' => 'Notification Test Student',
        'admission_number' => 'ADM-NOTIF-001',
        'roll_number' => '01A-01',
        'school_class_id' => $class->id,
        'status' => 'active',
    ]);

    $navResponseWithNotif = $this->get(route('home'));
    $navResponseWithNotif->assertStatus(200);
    $navResponseWithNotif->assertSee('bi-bell-fill', false);
    $navResponseWithNotif->assertSee('ADM-NOTIF-001');
});

test('roles with only view and file permissions do not see irrelevant action buttons and forms', function () {
    // Seed required permissions
    $permissions = [
        'show student', 'create student', 'edit student', 'delete student', 'print student',
        'show teacher', 'print teacher',
        'show class', 'print class',
    ];
    foreach ($permissions as $perm) {
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    $teacherRole = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
    $teacherRole->syncPermissions([
        'show student', 'print student',
        'show teacher', 'print teacher',
        'show class', 'print class',
    ]);

    $teacherUser = User::firstOrCreate(
        ['email' => 'viewer_teacher@school.com'],
        ['name' => 'Viewer Teacher', 'password' => bcrypt('password')]
    );
    $teacherUser->syncRoles([$teacherRole]);

    $class = SchoolClass::create([
        'name' => 'Grade 10',
        'section' => 'B',
        'capacity' => 25,
    ]);

    $student = Student::create([
        'name' => 'Alice Viewer Test',
        'admission_number' => 'ADM-VIEW-001',
        'roll_number' => '10B-01',
        'school_class_id' => $class->id,
        'status' => 'active',
    ]);

    // Act as the user with only show/print permissions
    $this->actingAs($teacherUser);

    $response = $this->get(route('students.index'));
    $response->assertStatus(200);

    // Should see authorized elements:
    $response->assertSee('Alice Viewer Test');
    $response->assertSee('Print PDF Directory');
    $response->assertSee('View Complete Student Modal');
    $response->assertSee('Print Slip (FPDF)');

    // Should NOT see unauthorized buttons or forms:
    $response->assertDontSee('Enroll New Student');
    $response->assertDontSee('createStudentModal');
    $response->assertDontSee('title="Edit Student"', false);
    $response->assertDontSee('title="Delete Student"', false);
    $response->assertDontSee('System Access');
    $response->assertDontSee('Admin Center');
    $response->assertDontSee('Fees &amp; Accounts');

    // Also check student show page
    $showResponse = $this->get(route('students.show', $student->id));
    $showResponse->assertStatus(200);
    $showResponse->assertSee('Print Official Slip (FPDF)');
    $showResponse->assertDontSee('Edit Profile');
    $showResponse->assertDontSee('Issue Fee Voucher');
});
