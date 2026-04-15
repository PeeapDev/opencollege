<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Modules\Settings\Models\Institution;
use App\Modules\Settings\Models\Role;
use App\Modules\Academic\Models\AcademicYear;
use App\Modules\Academic\Models\Semester;
use App\Modules\Academic\Models\Faculty;
use App\Modules\Academic\Models\Department;
use App\Modules\Academic\Models\Program;
use App\Modules\Academic\Models\Course;
use App\Modules\Staff\Models\Designation;

class OpenCollegeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Platform Institution (ID=1, super admin, main domain)
        $platform = Institution::create([
            'name' => 'OpenCollege Platform',
            'code' => 'OCP',
            'domain' => null,
            'email' => 'admin@college.edu.sl',
            'phone' => '+232-76-000-000',
            'address' => 'Tower Hill, Freetown',
            'city' => 'Freetown',
            'country' => 'Sierra Leone',
            'currency' => 'SLL',
            'currency_symbol' => 'Le',
            'timezone' => 'Africa/Freetown',
        ]);

        // Platform super admin role + user
        $superAdminRole = Role::create(['institution_id' => $platform->id, 'name' => 'super_admin', 'display_name' => 'Super Admin', 'is_system' => true]);
        $superAdmin = User::create([
            'name' => 'Platform Administrator',
            'email' => 'admin@college.edu.sl',
            'password' => Hash::make('admin123'),
            'current_institution_id' => $platform->id,
        ]);
        $superAdmin->roles()->attach($superAdminRole->id, ['institution_id' => $platform->id]);

        // 2. Sample College: College of Sierra Leone
        $institution = Institution::create([
            'name' => 'College of Sierra Leone',
            'code' => 'CSL',
            'domain' => 'csl',
            'type' => 'college',
            'email' => 'info@csl.college.edu.sl',
            'phone' => '+232-76-111-111',
            'address' => 'Tower Hill, Freetown',
            'city' => 'Freetown',
            'country' => 'Sierra Leone',
            'currency' => 'SLL',
            'currency_symbol' => 'Le',
            'timezone' => 'Africa/Freetown',
            'accreditation_status' => 'accredited',
            'plan' => 'premium',
            'subscription_start' => now(),
            'subscription_end' => now()->addYear(),
        ]);

        // College roles
        $adminRole = Role::create(['institution_id' => $institution->id, 'name' => 'admin', 'display_name' => 'College Admin', 'is_system' => true]);
        $registrarRole = Role::create(['institution_id' => $institution->id, 'name' => 'registrar', 'display_name' => 'Registrar', 'is_system' => true]);
        $lecturerRole = Role::create(['institution_id' => $institution->id, 'name' => 'lecturer', 'display_name' => 'Lecturer', 'is_system' => true]);
        $studentRole = Role::create(['institution_id' => $institution->id, 'name' => 'student', 'display_name' => 'Student', 'is_system' => true]);
        Role::create(['institution_id' => $institution->id, 'name' => 'librarian', 'display_name' => 'Librarian', 'is_system' => true]);
        Role::create(['institution_id' => $institution->id, 'name' => 'accountant', 'display_name' => 'Accountant', 'is_system' => true]);

        // College admin user
        $admin = User::create([
            'name' => 'CSL Administrator',
            'email' => 'admin@csl.college.edu.sl',
            'password' => Hash::make('college123'),
            'current_institution_id' => $institution->id,
        ]);
        $admin->roles()->attach($adminRole->id, ['institution_id' => $institution->id]);

        // 4. Academic Year & Semesters
        $ay = AcademicYear::create([
            'institution_id' => $institution->id,
            'title' => '2025/2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-08-31',
            'is_current' => true,
        ]);

        $sem1 = Semester::create([
            'institution_id' => $institution->id,
            'academic_year_id' => $ay->id,
            'name' => 'First Semester',
            'start_date' => '2025-09-01',
            'end_date' => '2026-01-31',
            'is_current' => true,
        ]);

        Semester::create([
            'institution_id' => $institution->id,
            'academic_year_id' => $ay->id,
            'name' => 'Second Semester',
            'start_date' => '2026-02-01',
            'end_date' => '2026-06-30',
        ]);

        // 5. Designations
        $designations = [];
        foreach (['Professor', 'Associate Professor', 'Senior Lecturer', 'Lecturer', 'Assistant Lecturer', 'Dean', 'Registrar', 'Librarian'] as $title) {
            $designations[$title] = Designation::create(['institution_id' => $institution->id, 'title' => $title]);
        }

        // 6. Faculties & Departments
        $facData = [
            'Faculty of Science' => ['FOS', ['Computer Science' => 'CS', 'Mathematics' => 'MTH', 'Physics' => 'PHY', 'Chemistry' => 'CHM']],
            'Faculty of Arts' => ['FOA', ['English' => 'ENG', 'History' => 'HIS', 'Political Science' => 'POL']],
            'Faculty of Engineering' => ['FOE', ['Civil Engineering' => 'CE', 'Electrical Engineering' => 'EE']],
            'Faculty of Education' => ['FED', ['Education Studies' => 'EDU', 'Curriculum & Instruction' => 'CI']],
            'Faculty of Business' => ['FOB', ['Accounting' => 'ACC', 'Management' => 'MGT', 'Economics' => 'ECO']],
        ];

        $departments = [];
        foreach ($facData as $facName => [$facCode, $depts]) {
            $faculty = Faculty::create([
                'institution_id' => $institution->id,
                'name' => $facName,
                'code' => $facCode,
            ]);
            foreach ($depts as $deptName => $deptCode) {
                $departments[$deptCode] = Department::create([
                    'institution_id' => $institution->id,
                    'faculty_id' => $faculty->id,
                    'name' => $deptName,
                    'code' => $deptCode,
                ]);
            }
        }

        // 7. Programs
        $programData = [
            ['BSc Computer Science', 'BSC-CS', 'CS', 'bachelors', 4, 160],
            ['BSc Mathematics', 'BSC-MTH', 'MTH', 'bachelors', 4, 140],
            ['BA English', 'BA-ENG', 'ENG', 'bachelors', 4, 130],
            ['BSc Civil Engineering', 'BSC-CE', 'CE', 'bachelors', 5, 180],
            ['BEd Education', 'BED-EDU', 'EDU', 'bachelors', 4, 140],
            ['BSc Accounting', 'BSC-ACC', 'ACC', 'bachelors', 4, 150],
            ['BSc Economics', 'BSC-ECO', 'ECO', 'bachelors', 4, 140],
            ['Diploma in Computer Science', 'DIP-CS', 'CS', 'diploma', 2, 80],
            ['MSc Computer Science', 'MSC-CS', 'CS', 'masters', 2, 60],
            ['Certificate in Management', 'CRT-MGT', 'MGT', 'certificate', 1, 30],
        ];

        $programs = [];
        foreach ($programData as [$name, $code, $deptCode, $level, $duration, $credits]) {
            $programs[$code] = Program::create([
                'institution_id' => $institution->id,
                'department_id' => $departments[$deptCode]->id,
                'name' => $name,
                'code' => $code,
                'level' => $level,
                'duration_years' => $duration,
                'total_credits' => $credits,
            ]);
        }

        // 8. Courses
        $courseData = [
            ['CS101', 'Introduction to Programming', 'CS', 3, 'core'],
            ['CS102', 'Data Structures', 'CS', 3, 'core'],
            ['CS201', 'Database Systems', 'CS', 3, 'core'],
            ['CS202', 'Computer Networks', 'CS', 3, 'core'],
            ['CS301', 'Software Engineering', 'CS', 3, 'core'],
            ['CS302', 'Artificial Intelligence', 'CS', 3, 'elective'],
            ['MTH101', 'Calculus I', 'MTH', 4, 'core'],
            ['MTH102', 'Linear Algebra', 'MTH', 3, 'core'],
            ['MTH201', 'Statistics', 'MTH', 3, 'core'],
            ['ENG101', 'English Composition', 'ENG', 3, 'general'],
            ['ENG201', 'Literature Survey', 'ENG', 3, 'core'],
            ['PHY101', 'Physics I', 'PHY', 4, 'core'],
            ['CHM101', 'Chemistry I', 'CHM', 4, 'core'],
            ['CE101', 'Engineering Mechanics', 'CE', 4, 'core'],
            ['ACC101', 'Financial Accounting', 'ACC', 3, 'core'],
            ['ECO101', 'Microeconomics', 'ECO', 3, 'core'],
            ['MGT101', 'Principles of Management', 'MGT', 3, 'core'],
            ['EDU101', 'Foundations of Education', 'EDU', 3, 'core'],
        ];

        foreach ($courseData as [$code, $name, $deptCode, $credits, $type]) {
            Course::create([
                'institution_id' => $institution->id,
                'department_id' => $departments[$deptCode]->id,
                'name' => $name,
                'code' => $code,
                'credit_hours' => $credits,
                'type' => $type,
            ]);
        }

        // 9. Sample staff
        $staffNames = [
            ['Dr. Mohamed Kamara', 'mkamara@college.edu.sl', 'CS', 'Professor'],
            ['Dr. Fatima Sesay', 'fsesay@college.edu.sl', 'MTH', 'Senior Lecturer'],
            ['Prof. Ibrahim Conteh', 'iconteh@college.edu.sl', 'ENG', 'Professor'],
            ['Dr. Aminata Koroma', 'akoroma@college.edu.sl', 'CE', 'Associate Professor'],
            ['Mr. Abu Bangura', 'abangura@college.edu.sl', 'ACC', 'Lecturer'],
        ];

        foreach ($staffNames as [$name, $email, $deptCode, $desTitle]) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('staff123'),
                'current_institution_id' => $institution->id,
            ]);
            $user->roles()->attach($lecturerRole->id, ['institution_id' => $institution->id]);

            \App\Modules\Staff\Models\Staff::create([
                'user_id' => $user->id,
                'institution_id' => $institution->id,
                'staff_id' => 'STF-' . str_pad(rand(100, 999), 3, '0'),
                'department_id' => $departments[$deptCode]->id,
                'designation_id' => $designations[$desTitle]->id,
                'staff_type' => 'academic',
                'joining_date' => now()->subYears(rand(1, 10)),
                'gender' => str_contains($name, 'Fatima') || str_contains($name, 'Aminata') ? 'female' : 'male',
                'qualification' => str_contains($desTitle, 'Prof') ? 'PhD' : 'MSc',
            ]);
        }

        // 10. Sample students
        $studentNames = [
            ['Alhaji Turay', 'male'], ['Mariama Jalloh', 'female'], ['Foday Mansaray', 'male'],
            ['Isata Bangura', 'female'], ['Mohamed Sesay', 'male'], ['Aminata Kamara', 'female'],
            ['Ibrahim Conteh', 'male'], ['Fatmata Koroma', 'female'], ['Abu Bakarr Sesay', 'male'],
            ['Kadiatu Mansaray', 'female'], ['Sorie Kamara', 'male'], ['Hawa Jalloh', 'female'],
            ['Mustapha Bangura', 'male'], ['Zainab Turay', 'female'], ['Alpha Conteh', 'male'],
        ];

        $programCodes = ['BSC-CS', 'BSC-MTH', 'BA-ENG', 'BSC-CE', 'BED-EDU', 'BSC-ACC', 'BSC-ECO'];

        foreach ($studentNames as $i => [$name, $gender]) {
            $progCode = $programCodes[$i % count($programCodes)];
            $email = strtolower(str_replace(' ', '.', $name)) . '@student.college.edu.sl';

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('student123'),
                'current_institution_id' => $institution->id,
            ]);
            $user->roles()->attach($studentRole->id, ['institution_id' => $institution->id]);

            \App\Modules\Student\Models\Student::create([
                'user_id' => $user->id,
                'institution_id' => $institution->id,
                'student_id' => 'STU-2025-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'program_id' => $programs[$progCode]->id,
                'admission_date' => '2025-09-01',
                'current_year' => rand(1, 3),
                'current_semester' => rand(1, 2),
                'gender' => $gender,
                'date_of_birth' => now()->subYears(rand(18, 25))->subDays(rand(0, 365)),
                'nationality' => 'Sierra Leonean',
                'status' => 'active',
            ]);
        }

        // 11. Second sample college: Njala University
        $njala = Institution::create([
            'name' => 'Njala University',
            'code' => 'NJALA',
            'domain' => 'njala',
            'type' => 'university',
            'email' => 'info@njala.college.edu.sl',
            'phone' => '+232-76-222-222',
            'address' => 'Njala Campus',
            'city' => 'Bo',
            'country' => 'Sierra Leone',
            'currency' => 'SLL',
            'currency_symbol' => 'Le',
            'timezone' => 'Africa/Freetown',
            'accreditation_status' => 'accredited',
            'plan' => 'basic',
            'subscription_start' => now(),
            'subscription_end' => now()->addYear(),
        ]);

        $njalaAdminRole = Role::create(['institution_id' => $njala->id, 'name' => 'admin', 'display_name' => 'College Admin', 'is_system' => true]);
        foreach (['registrar' => 'Registrar', 'lecturer' => 'Lecturer', 'student' => 'Student'] as $rn => $rd) {
            Role::create(['institution_id' => $njala->id, 'name' => $rn, 'display_name' => $rd, 'is_system' => true]);
        }
        $njalaAdmin = User::create(['name' => 'Njala Administrator', 'email' => 'admin@njala.college.edu.sl', 'password' => Hash::make('college123'), 'current_institution_id' => $njala->id]);
        $njalaAdmin->roles()->attach($njalaAdminRole->id, ['institution_id' => $njala->id]);
        AcademicYear::create(['institution_id' => $njala->id, 'title' => '2025/2026', 'start_date' => '2025-09-01', 'end_date' => '2026-08-31', 'is_current' => true]);

        $this->command->info('OpenCollege seeded (multi-tenant):');
        $this->command->info('  Platform super admin: admin@college.edu.sl / admin123');
        $this->command->info('  CSL college admin:    admin@csl.college.edu.sl / college123  (subdomain: csl.college.edu.sl)');
        $this->command->info('  Njala college admin:  admin@njala.college.edu.sl / college123 (subdomain: njala.college.edu.sl)');
        $this->command->info('  CSL data: 5 faculties, 14 departments, 10 programs, 18 courses, 5 staff, 15 students');
    }
}
