<?php

namespace Database\Seeders;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\ClassSchedule;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ensure all classes and sessions have share_tokens
        \App\Models\SchoolClass::whereNull('share_token')->get()->each(function ($c) {
            $c->update(['share_token' => \Illuminate\Support\Str::random(32)]);
        });
        \App\Models\AttendanceSession::whereNull('share_token')->get()->each(function ($s) {
            $s->update(['share_token' => \Illuminate\Support\Str::random(32)]);
        });

        // 1. Primary Demo Teacher (Alice)
        $teacher1 = Teacher::firstOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'name' => 'Teacher Alice',
                'password_hash' => Hash::make('password'),
            ]
        );

        // 2. Secondary Demo Teacher (Bob - for isolation)
        $teacher2 = Teacher::firstOrCreate(
            ['email' => 'teacher2@example.com'],
            [
                'name' => 'Teacher Bob',
                'password_hash' => Hash::make('password'),
            ]
        );

        // 3. Classes for Teacher Alice
        $class1 = SchoolClass::firstOrCreate(
            [
                'teacher_id' => $teacher1->id,
                'class_name' => 'MIS-Y2-A',
                'subject_name' => 'Java Programming',
            ]
        );

        $class2 = SchoolClass::firstOrCreate(
            [
                'teacher_id' => $teacher1->id,
                'class_name' => 'CS-Y3-B',
                'subject_name' => 'Database Systems',
            ]
        );

        $class3 = SchoolClass::firstOrCreate(
            [
                'teacher_id' => $teacher1->id,
                'class_name' => 'WEB-Y1-C',
                'subject_name' => 'Full-Stack Web Development',
            ]
        );

        // Class for Teacher Bob
        $classBob = SchoolClass::firstOrCreate(
            [
                'teacher_id' => $teacher2->id,
                'class_name' => 'IT-Y1-A',
                'subject_name' => 'Computer Networks',
            ]
        );

        // 4. Class Schedules
        $schedules = [
            // Class 1 (MIS-Y2-A)
            ['class_id' => $class1->id, 'day_of_week' => 'Monday', 'shift' => 'Morning', 'start_time' => '08:00:00', 'end_time' => '09:30:00'],
            ['class_id' => $class1->id, 'day_of_week' => 'Wednesday', 'shift' => 'Morning', 'start_time' => '08:00:00', 'end_time' => '09:30:00'],
            // Class 2 (CS-Y3-B)
            ['class_id' => $class2->id, 'day_of_week' => 'Tuesday', 'shift' => 'Afternoon', 'start_time' => '13:00:00', 'end_time' => '14:30:00'],
            ['class_id' => $class2->id, 'day_of_week' => 'Thursday', 'shift' => 'Afternoon', 'start_time' => '13:00:00', 'end_time' => '14:30:00'],
            // Class 3 (WEB-Y1-C)
            ['class_id' => $class3->id, 'day_of_week' => 'Friday', 'shift' => 'Morning', 'start_time' => '08:30:00', 'end_time' => '11:30:00'],
            // Bob's class
            ['class_id' => $classBob->id, 'day_of_week' => 'Monday', 'shift' => 'Afternoon', 'start_time' => '14:00:00', 'end_time' => '15:30:00'],
        ];

        foreach ($schedules as $sched) {
            ClassSchedule::firstOrCreate(
                [
                    'class_id' => $sched['class_id'],
                    'day_of_week' => $sched['day_of_week'],
                    'start_time' => $sched['start_time'],
                ],
                [
                    'shift' => $sched['shift'],
                    'end_time' => $sched['end_time'],
                ]
            );
        }

        // 5. Students in Class 1 (MIS-Y2-A)
        $studentsClass1Data = [
            ['code' => 'STU-00101', 'name' => 'Sophea Chan'],
            ['code' => 'STU-00102', 'name' => 'Bopha Kim'],
            ['code' => 'STU-00103', 'name' => 'Rathana Seng'],
            ['code' => 'STU-00104', 'name' => 'Sreymom Meas'],
            ['code' => 'STU-00105', 'name' => 'Dara Sok'],
            ['code' => 'STU-00106', 'name' => 'Pisey Heng'],
            ['code' => 'STU-00107', 'name' => 'Vicheka Roeun'],
            ['code' => 'STU-00108', 'name' => 'Kolab Keo'],
            ['code' => 'STU-00109', 'name' => 'Samnang Ouk'],
            ['code' => 'STU-00110', 'name' => 'Channary Phan'],
        ];

        $class1Students = [];
        foreach ($studentsClass1Data as $data) {
            $class1Students[] = Student::firstOrCreate(
                ['class_id' => $class1->id, 'student_code' => $data['code']],
                ['full_name' => $data['name']]
            );
        }

        // Students in Class 2 (CS-Y3-B)
        $studentsClass2Data = [
            ['code' => 'STU-00201', 'name' => 'Kosal Ly'],
            ['code' => 'STU-00202', 'name' => 'Malis Chea'],
            ['code' => 'STU-00203', 'name' => 'Bunthorn Ngeth'],
            ['code' => 'STU-00204', 'name' => 'Neary Chhay'],
            ['code' => 'STU-00205', 'name' => 'Panha Ros'],
            ['code' => 'STU-00206', 'name' => 'Sovann Keat'],
        ];

        $class2Students = [];
        foreach ($studentsClass2Data as $data) {
            $class2Students[] = Student::firstOrCreate(
                ['class_id' => $class2->id, 'student_code' => $data['code']],
                ['full_name' => $data['name']]
            );
        }

        // Students in Class 3 (WEB-Y1-C)
        $studentsClass3Data = [
            ['code' => 'STU-00301', 'name' => 'Theara Sorn'],
            ['code' => 'STU-00302', 'name' => 'Kunthea Vann'],
            ['code' => 'STU-00303', 'name' => 'Chanthy Mao'],
            ['code' => 'STU-00304', 'name' => 'Visal Prak'],
        ];
        foreach ($studentsClass3Data as $data) {
            Student::firstOrCreate(
                ['class_id' => $class3->id, 'student_code' => $data['code']],
                ['full_name' => $data['name']]
            );
        }

        // 6. Attendance Sessions for Class 1 (MIS-Y2-A)
        $sessionDates = ['2026-09-08', '2026-09-15', '2026-09-22', '2026-09-29'];
        $sessions = [];
        foreach ($sessionDates as $date) {
            $sessions[] = AttendanceSession::firstOrCreate(
                [
                    'class_id' => $class1->id,
                    'session_date' => $date,
                    'start_time' => '08:00:00',
                ],
                [
                    'end_time' => '09:30:00',
                ]
            );
        }

        // 7. Attendance Records
        $statuses = [
            AttendanceRecord::STATUS_PRESENT,
            AttendanceRecord::STATUS_PRESENT,
            AttendanceRecord::STATUS_PRESENT,
            AttendanceRecord::STATUS_LATE,
            AttendanceRecord::STATUS_ABSENT,
            AttendanceRecord::STATUS_EXCUSED,
            AttendanceRecord::STATUS_PRESENT,
            AttendanceRecord::STATUS_PRESENT,
        ];

        foreach ($sessions as $sIndex => $session) {
            foreach ($class1Students as $stIndex => $student) {
                $status = $statuses[($stIndex + $sIndex) % count($statuses)];
                $note = null;
                if ($status === AttendanceRecord::STATUS_EXCUSED) {
                    $note = 'Approved medical leave';
                } elseif ($status === AttendanceRecord::STATUS_LATE) {
                    $note = 'Late arrival due to transportation delay';
                }

                AttendanceRecord::firstOrCreate(
                    [
                        'attendance_session_id' => $session->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'status' => $status,
                        'note' => $note,
                    ]
                );
            }
        }
    }
}
