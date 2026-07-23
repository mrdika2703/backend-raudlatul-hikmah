<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Cek kredensial
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Email atau password salah. Silakan periksa kembali.'
            ], 401);
        }

        // 3. Buat token Sanctum
        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('admin-token')->plainTextToken;

        // 4. Kirim respons JSON ke React
        return response()->json([
            'message' => 'Login berhasil!',
            'token' => $token,
            'user' => $user
        ], 200);
    }

    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Berhasil logout'
        ], 200);
    }
}
