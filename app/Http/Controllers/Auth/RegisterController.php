<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nis' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = DB::transaction(function () use ($data) {
            // Kunci baris siswa agar dua pendaftaran bersamaan tidak mengklaim NIS yang sama
            $student = Student::where('nis', $data['nis'])
                ->where('is_active', true)
                ->whereNull('user_id')
                ->lockForUpdate()
                ->first();

            if (! $student) {
                throw ValidationException::withMessages([
                    'nis' => 'NIS tidak ditemukan, tidak aktif, atau sudah memiliki akun.',
                ]);
            }

            $user = User::create([
                'name' => $student->full_name,
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => Role::Siswa,
            ]);

            $student->update(['user_id' => $user->id]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}