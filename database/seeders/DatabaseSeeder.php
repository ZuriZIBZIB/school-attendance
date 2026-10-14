<?php

namespace Database\Seeders;

use App\Enums\AttendanceStatus;
use App\Enums\Role;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $faker = fake('id_ID');

            User::create([
                'name' => 'Administrator',
                'email' => 'admin@sekolah.test',
                'password' => 'password',
                'role' => Role::Admin,
            ]);

            $classes = [
                ['X RPL 1', 10, 'Budi Santoso'],
                ['XI RPL 1', 11, 'Siti Rahmawati'],
                ['XII RPL 1', 12, 'Agus Prasetyo'],
            ];

            // 14 hari terakhir tanpa akhir pekan; hari ini sengaja kosong agar bisa diisi guru
            $days = collect(range(1, 14))
                ->map(fn ($i) => today()->subDays($i))
                ->reject(fn ($d) => $d->isWeekend());

            $studentNo = 0;
            $accountNo = 0;
            $attendanceRows = [];

            foreach ($classes as $i => [$className, $grade, $teacherName]) {
                $teacherUser = User::create([
                    'name' => $teacherName,
                    'email' => 'guru'.($i + 1).'@sekolah.test',
                    'password' => 'password',
                    'role' => Role::Guru,
                ]);

                $teacher = Teacher::create([
                    'user_id' => $teacherUser->id,
                    'nip' => '19800101'.str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'full_name' => $teacherName,
                ]);

                $classroom = Classroom::create([
                    'name' => $className,
                    'grade_level' => $grade,
                    'teacher_id' => $teacher->id,
                ]);

                for ($n = 1; $n <= 10; $n++) {
                    $studentNo++;
                    $gender = $n % 2 === 0 ? 'P' : 'L';
                    $fullName = $faker->name($gender === 'L' ? 'male' : 'female');

                    // 2 siswa pertama tiap kelas punya akun; sisanya tanpa akun (untuk uji register via NIS)
                    $userId = null;
                    if ($n <= 2) {
                        $accountNo++;
                        $userId = User::create([
                            'name' => $fullName,
                            'email' => "siswa{$accountNo}@sekolah.test",
                            'password' => 'password',
                            'role' => Role::Siswa,
                        ])->id;
                    }

                    $student = Student::create([
                        'user_id' => $userId,
                        'classroom_id' => $classroom->id,
                        'nis' => '2026'.str_pad($studentNo, 4, '0', STR_PAD_LEFT),
                        'full_name' => $fullName,
                        'gender' => $gender,
                    ]);

                    foreach ($days as $day) {
                        $attendanceRows[] = [
                            'student_id' => $student->id,
                            'classroom_id' => $classroom->id,
                            'date' => $day->toDateString(),
                            'status' => $this->randomStatus()->value,
                            'note' => null,
                            'recorded_by' => $teacherUser->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            Attendance::insert($attendanceRows);
        });
    }

    private function randomStatus(): AttendanceStatus
    {
        $roll = mt_rand(1, 100);

        return match (true) {
            $roll <= 85 => AttendanceStatus::Hadir,
            $roll <= 91 => AttendanceStatus::Sakit,
            $roll <= 97 => AttendanceStatus::Izin,
            default => AttendanceStatus::Alpa,
        };
    }
}