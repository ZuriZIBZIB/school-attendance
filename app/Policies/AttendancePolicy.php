<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Attendance $attendance): bool
    {
        return $user->isAdmin()
            || $user->teachesClassroom($attendance->classroom)
            || ($user->isSiswa() && $attendance->student->user_id === $user->id);
    }

    public function update(User $user, Attendance $attendance): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->teachesClassroom($attendance->classroom)
            && $attendance->date->gte(today()->subDays(7));
    }
}