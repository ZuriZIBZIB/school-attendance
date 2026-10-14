<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\CarbonInterface;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'is_active' => 'boolean',
        ];
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function recordedAttendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'recorded_by');
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function isGuru(): bool
    {
        return $this->role === Role::Guru;
    }

    public function isSiswa(): bool
    {
        return $this->role === Role::Siswa;
    }

    public function teachesClassroom(Classroom $classroom): bool
    {
        return $this->isGuru()
            && $this->teacher !== null
            && $classroom->teacher_id === $this->teacher->id;
    }

        public function canRecordAttendanceOn(CarbonInterface $date): bool
    {
        if ($date->gt(today())) {
            return false;
        }

        return $this->isAdmin() || $date->gte(today()->subDays(7));
    }
}
