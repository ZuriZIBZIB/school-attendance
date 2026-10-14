<?php

namespace App\Policies;

use App\Models\Classroom;
use App\Models\User;

class ClassroomPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isGuru();
    }

    public function view(User $user, Classroom $classroom): bool
    {
        return $user->isAdmin() || $user->teachesClassroom($classroom);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Classroom $classroom): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Classroom $classroom): bool
    {
        return $user->isAdmin();
    }

        public function recordAttendance(User $user, Classroom $classroom): bool
    {
        return $user->isAdmin() || $user->teachesClassroom($classroom);
    }
}