<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    // USER LOGIN SAAT INI
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'photo' => 'nullable|image|max:2048'
        ]);

        // update basic data
        $user->name = $request->name;
        $user->email = $request->email;

        // handle upload foto
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('users', 'public');
            $user->foto = $path;
        } else {
            $user->foto = null;
        }

        $user->save();

        return response()->json([
            'message' => 'Profile berhasil diupdate',
            'user' => $user
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:5',
            'confirm_password' => 'required|min:5',
        ]);

        $user = auth()->user();

        // cek password lama
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'message' => 'Password lama salah'
            ], 400);
        }

        // cek password baru sama
        if ($request->new_password !== $request->confirm_password) {
            return response()->json([
                'message' => 'Password tidak sama'
            ], 400);
        }

        // ❗ FIX UTAMA DI SINI
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password berhasil diubah'
        ]);
    }
}
