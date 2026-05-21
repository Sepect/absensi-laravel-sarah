<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login for interns (mobile app)
     * Uses email + password from users table
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
            ->where('role', User::ROLE_INTERN)
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['E-mail tidak valid.'],
            ]);
        }

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kata sandi tidak valid.'],
            ]);
        }

        if ($user->role !== User::ROLE_INTERN) {
            return response()->json([
                'success' => false,
                'message' => 'Akun ini bukan untuk peserta magang.',
            ], 403);
        }

        $token = $user->createToken('intern_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'token' => $token,
                'user' => $this->formatUserData($user),
            ],
        ]);
    }

    /**
     * Login for admins (web)
     */
    public function adminLogin(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
            ->where('role', User::ROLE_ADMIN)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau kata sandi tidak valid.'],
            ]);
        }

        $token = $user->createToken('admin_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'token' => $token,
                'user' => $this->formatUserData($user),
            ],
        ]);
    }

    /**
     * Get current user profile
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => $this->formatUserData($user),
        ]);
    }

    /**
     * Logout (revoke token)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * Format user data for response
     */
    private function formatUserData(User $user): array
    {
        $data = [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
        ];

        if ($user->isAdmin()) {
            $profile = $user->adminProfile;
            $data['profile'] = $profile ? [
                'id' => $profile->id,
                'nama' => $profile->nama,
                'no_telp' => $profile->no_telp,
            ] : null;
        } else {
            $profile = $user->internProfile;
            $data['profile'] = $profile ? [
                'id' => $profile->id,
                'nim_nis' => $profile->nim_nis,
                'nama_lengkap' => $profile->nama_lengkap,
                'asal_sekolah_kampus' => $profile->asal_sekolah_kampus,
                'no_telp' => $profile->no_telp,
                'nama_pembimbing' => $profile->nama_pembimbing,
                'alamat' => $profile->alamat,
                'foto_peserta' => $profile->foto_peserta,
                'kontak_darurat' => $profile->kontak_darurat,
                'tanggal_mulai' => $profile->tanggal_mulai?->toDateString(),
                'tanggal_selesai' => $profile->tanggal_selesai?->toDateString(),
                'status' => $profile->status,
            ] : null;
        }

        return $data;
    }
}
