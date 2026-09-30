<?php

namespace Database\Seeders;

use App\Models\FeeInvoice;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Permissions
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
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // 2. Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        $accountantRole = Role::firstOrCreate(['name' => 'accountant', 'guard_name' => 'web']);
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        // Admin gets all permissions
        $adminRole->syncPermissions(Permission::all());

        // Teacher permissions
        $teacherRole->syncPermissions([
            'show student', 'print student',
            'show teacher', 'print teacher',
            'show class', 'print class',
        ]);

        // Accountant permissions
        $accountantRole->syncPermissions([
            'show accountant', 'create fee', 'edit fee', 'delete fee', 'print fee',
            'show student', 'print student',
            'show class',
        ]);

        // Student permissions
        $studentRole->syncPermissions([
            'show student', 'print student',
        ]);

        // 3. Create Key Users
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@school.com'],
            ['name' => 'System Admin', 'password' => Hash::make('password')]
        );
        $adminUser->syncRoles([$adminRole]);

        $teacherUser = User::firstOrCreate(
            ['email' => 'teacher@school.com'],
            ['name' => 'Prof. Sarah Jenkins', 'password' => Hash::make('password')]
        );
        $teacherUser->syncRoles([$teacherRole]);

        $accountantUser = User::firstOrCreate(
            ['email' => 'accountant@school.com'],
            ['name' => 'Alex Turner (Finance)', 'password' => Hash::make('password')]
        );
        $accountantUser->syncRoles([$accountantRole]);

        $studentUser = User::firstOrCreate(
            ['email' => 'student@school.com'],
            ['name' => 'Liam Anderson', 'password' => Hash::make('password')]
        );
        $studentUser->syncRoles([$studentRole]);

        // 4. Create Teachers
        $teachersData = [
            [
                'user_id' => $teacherUser->id,
                'employee_code' => 'TCH-1001',
                'name' => 'Prof. Sarah Jenkins',
                'email' => 'teacher@school.com',
                'phone' => '+1 (555) 234-5678',
                'qualification' => 'M.Sc. Mathematics',
                'specialization' => 'Mathematics & Statistics',
                'salary' => 4500.00,
                'joining_date' => '2021-08-15',
                'status' => 'active',
                'address' => '742 Evergreen Terrace, Springfield',
            ],
            [
                'user_id' => null,
                'employee_code' => 'TCH-1002',
                'name' => 'Dr. David Miller',
                'email' => 'david.miller@school.com',
                'phone' => '+1 (555) 345-6789',
                'qualification' => 'Ph.D. Physics',
                'specialization' => 'Physics & Chemistry',
                'salary' => 5200.00,
                'joining_date' => '2020-01-10',
                'status' => 'active',
                'address' => '12 Elm Street, Springfield',
            ],
            [
                'user_id' => null,
                'employee_code' => 'TCH-1003',
                'name' => 'Ms. Elena Rostova',
                'email' => 'elena.rostova@school.com',
                'phone' => '+1 (555) 456-7890',
                'qualification' => 'M.A. English Literature',
                'specialization' => 'English & World Literature',
                'salary' => 4200.00,
                'joining_date' => '2022-09-01',
                'status' => 'active',
                'address' => '88 Oak Avenue, Springfield',
            ],
            [
                'user_id' => null,
                'employee_code' => 'TCH-1004',
                'name' => 'Mr. Marcus Vance',
                'email' => 'marcus.vance@school.com',
                'phone' => '+1 (555) 567-8901',
                'qualification' => 'B.S. Computer Engineering',
                'specialization' => 'Computer Science & Robotics',
                'salary' => 4800.00,
                'joining_date' => '2023-02-15',
                'status' => 'active',
                'address' => '304 Maple Drive, Springfield',
            ],
        ];

        $teachers = [];
        foreach ($teachersData as $t) {
            $teachers[] = Teacher::firstOrCreate(['employee_code' => $t['employee_code']], $t);
        }

        // 5. Create School Classes
        $classesData = [
            ['name' => 'Grade 9', 'section' => 'A', 'room_number' => 'Room 101', 'capacity' => 35, 'teacher_id' => $teachers[0]->id],
            ['name' => 'Grade 9', 'section' => 'B', 'room_number' => 'Room 102', 'capacity' => 35, 'teacher_id' => $teachers[1]->id],
            ['name' => 'Grade 10', 'section' => 'A', 'room_number' => 'Room 201', 'capacity' => 30, 'teacher_id' => $teachers[2]->id],
            ['name' => 'Grade 10', 'section' => 'B', 'room_number' => 'Room 202', 'capacity' => 30, 'teacher_id' => $teachers[3]->id],
        ];

        $classes = [];
        foreach ($classesData as $c) {
            $classes[] = SchoolClass::firstOrCreate(
                ['name' => $c['name'], 'section' => $c['section']],
                $c
            );
        }

        // 6. Create Students
        $studentsData = [
            [
                'user_id' => $studentUser->id,
                'school_class_id' => $classes[0]->id,
                'admission_number' => 'ADM-2024-001',
                'roll_number' => '09A-01',
                'name' => 'Liam Anderson',
                'email' => 'student@school.com',
                'phone' => '+1 (555) 789-0123',
                'gender' => 'male',
                'date_of_birth' => '2009-04-12',
                'admission_date' => '2024-08-01',
                'guardian_name' => 'Robert Anderson',
                'guardian_phone' => '+1 (555) 890-1234',
                'guardian_relation' => 'Father',
                'address' => '45 Hilltop Way, Springfield',
                'status' => 'active',
            ],
            [
                'user_id' => null,
                'school_class_id' => $classes[0]->id,
                'admission_number' => 'ADM-2024-002',
                'roll_number' => '09A-02',
                'name' => 'Sophia Martinez',
                'email' => 'sophia.m@example.com',
                'phone' => '+1 (555) 789-0124',
                'gender' => 'female',
                'date_of_birth' => '2009-07-22',
                'admission_date' => '2024-08-01',
                'guardian_name' => 'Maria Martinez',
                'guardian_phone' => '+1 (555) 890-1235',
                'guardian_relation' => 'Mother',
                'address' => '102 Sunset Blvd, Springfield',
                'status' => 'active',
            ],
            [
                'user_id' => null,
                'school_class_id' => $classes[1]->id,
                'admission_number' => 'ADM-2024-003',
                'roll_number' => '09B-01',
                'name' => 'Ethan Walker',
                'email' => 'ethan.w@example.com',
                'phone' => '+1 (555) 789-0125',
                'gender' => 'male',
                'date_of_birth' => '2009-02-18',
                'admission_date' => '2024-08-05',
                'guardian_name' => 'James Walker',
                'guardian_phone' => '+1 (555) 890-1236',
                'guardian_relation' => 'Father',
                'address' => '54 Meadow Lane, Springfield',
                'status' => 'active',
            ],
            [
                'user_id' => null,
                'school_class_id' => $classes[2]->id,
                'admission_number' => 'ADM-2023-015',
                'roll_number' => '10A-01',
                'name' => 'Olivia Taylor',
                'email' => 'olivia.t@example.com',
                'phone' => '+1 (555) 789-0126',
                'gender' => 'female',
                'date_of_birth' => '2008-11-05',
                'admission_date' => '2023-08-10',
                'guardian_name' => 'George Taylor',
                'guardian_phone' => '+1 (555) 890-1237',
                'guardian_relation' => 'Father',
                'address' => '22 River Road, Springfield',
                'status' => 'active',
            ],
            [
                'user_id' => null,
                'school_class_id' => $classes[3]->id,
                'admission_number' => 'ADM-2023-022',
                'roll_number' => '10B-01',
                'name' => 'Noah Chen',
                'email' => 'noah.c@example.com',
                'phone' => '+1 (555) 789-0127',
                'gender' => 'male',
                'date_of_birth' => '2008-09-30',
                'admission_date' => '2023-08-12',
                'guardian_name' => 'Linda Chen',
                'guardian_phone' => '+1 (555) 890-1238',
                'guardian_relation' => 'Mother',
                'address' => '89 Pine Grove, Springfield',
                'status' => 'active',
            ],
            [
                'user_id' => null,
                'school_class_id' => $classes[2]->id,
                'admission_number' => 'ADM-2023-028',
                'roll_number' => '10A-02',
                'name' => 'Ava Wilson',
                'email' => 'ava.w@example.com',
                'phone' => '+1 (555) 789-0128',
                'gender' => 'female',
                'date_of_birth' => '2008-05-14',
                'admission_date' => '2023-08-15',
                'guardian_name' => 'Arthur Wilson',
                'guardian_phone' => '+1 (555) 890-1239',
                'guardian_relation' => 'Father',
                'address' => '31 Cedar Court, Springfield',
                'status' => 'active',
            ],
        ];

        $students = [];
        foreach ($studentsData as $s) {
            $students[] = Student::firstOrCreate(['admission_number' => $s['admission_number']], $s);
        }

        // 7. Create Fee Invoices
        $invoicesData = [
            [
                'student_id' => $students[0]->id,
                'created_by' => $accountantUser->id,
                'invoice_number' => 'INV-2026-001',
                'title' => 'Tuition Fee - October 2026',
                'fee_type' => 'tuition',
                'total_amount' => 450.00,
                'paid_amount' => 450.00,
                'due_date' => '2026-10-10',
                'paid_date' => '2026-10-05',
                'status' => 'paid',
                'payment_method' => 'bank_transfer',
                'notes' => 'Paid in full via online bank transfer.',
            ],
            [
                'student_id' => $students[1]->id,
                'created_by' => $accountantUser->id,
                'invoice_number' => 'INV-2026-002',
                'title' => 'Tuition Fee - October 2026',
                'fee_type' => 'tuition',
                'total_amount' => 450.00,
                'paid_amount' => 200.00,
                'due_date' => '2026-10-10',
                'paid_date' => '2026-10-08',
                'status' => 'partial',
                'payment_method' => 'cash',
                'notes' => 'Partial cash payment received. Balance due before end of month.',
            ],
            [
                'student_id' => $students[2]->id,
                'created_by' => $accountantUser->id,
                'invoice_number' => 'INV-2026-003',
                'title' => 'Tuition Fee - October 2026',
                'fee_type' => 'tuition',
                'total_amount' => 450.00,
                'paid_amount' => 0.00,
                'due_date' => '2026-10-15',
                'paid_date' => null,
                'status' => 'unpaid',
                'payment_method' => null,
                'notes' => 'Pending payment.',
            ],
            [
                'student_id' => $students[3]->id,
                'created_by' => $accountantUser->id,
                'invoice_number' => 'INV-2026-004',
                'title' => 'Annual Exam Fee - 2026',
                'fee_type' => 'exam',
                'total_amount' => 150.00,
                'paid_amount' => 150.00,
                'due_date' => '2026-10-01',
                'paid_date' => '2026-09-28',
                'status' => 'paid',
                'payment_method' => 'online',
                'notes' => 'Paid via debit card.',
            ],
            [
                'student_id' => $students[4]->id,
                'created_by' => $accountantUser->id,
                'invoice_number' => 'INV-2026-005',
                'title' => 'Tuition & Lab Fee - October 2026',
                'fee_type' => 'tuition',
                'total_amount' => 520.00,
                'paid_amount' => 520.00,
                'due_date' => '2026-10-10',
                'paid_date' => '2026-10-02',
                'status' => 'paid',
                'payment_method' => 'cheque',
                'notes' => 'Cheque #8832 cleared successfully.',
            ],
            [
                'student_id' => $students[5]->id,
                'created_by' => $accountantUser->id,
                'invoice_number' => 'INV-2026-006',
                'title' => 'Transport Fee - Term 1',
                'fee_type' => 'transport',
                'total_amount' => 300.00,
                'paid_amount' => 0.00,
                'due_date' => '2026-10-20',
                'paid_date' => null,
                'status' => 'unpaid',
                'payment_method' => null,
                'notes' => 'First installment due.',
            ],
        ];

        foreach ($invoicesData as $inv) {
            FeeInvoice::firstOrCreate(['invoice_number' => $inv['invoice_number']], $inv);
        }
    }
}
