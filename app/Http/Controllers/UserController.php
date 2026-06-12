<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index', [
            'pageTitle' => 'Users'
        ]);
    }

    public function data(Request $request)
    {
        try {
            $users = User::latest()->where('status', $request->status)->get();

            return response()->json([
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required|max:100',
            'email' => 'required|email|unique:users,email',
            'role'  => 'required',
            'foto'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'name.required'  => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.unique'   => 'Email sudah digunakan',
            'role.required'  => 'Role wajib dipilih',
            'foto.image'     => 'File harus berupa gambar',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $fotoPath = null;

            // upload foto jika ada
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('users', 'public');
            }

            $user = User::create([
                'name'  => $request->name,
                'email' => $request->email,
                'password' => bcrypt('12345'),
                'role'  => $request->role,
                'foto'  => $fotoPath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil ditambahkan',
                'data'    => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Terjadi kesalahan saat menyimpan data"
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'  => 'required|max:100',
            'email' => 'required|email|unique:users,email,' . $id,
            'role'  => 'required',
            'foto'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $fotoPath = $user->foto;

            if ($request->hasFile('foto')) {

                // hapus foto lama
                if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                    Storage::disk('public')->delete($user->foto);
                }

                $fotoPath = $request->file('foto')->store('users', 'public');
            }

            $user->update([
                'name'  => $request->name,
                'email' => $request->email,
                'role'  => $request->role,
                'foto'  => $fotoPath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update data'
            ], 500);
        }
    }

    public function deactivate($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->status = false;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data'
            ], 500);
        }
    }

    public function reset($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->password = bcrypt('12345');
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset password'
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->status = true;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dikembalikan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengembalikan data'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data'
            ], 500);
        }
    }
}
