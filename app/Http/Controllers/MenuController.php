<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\FotoMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class MenuController extends Controller
{
    public function index()
    {
        try {
            $kategoriMenus = DB::table('kategori_menus')->where('status', true)->get();

            return view('menu.index', [
                'pageTitle' => 'Daftar Menu',
                'kategoriMenus' => $kategoriMenus
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat halaman: ' . $e->getMessage());
        }
    }

    public function data(Request $request)
    {
        try {

            $query = Menu::with(['fotoMenus', 'kategoriMenu'])
                ->where('status', $request->status ?? 1);

            if ($request->has('kategori') && count($request->kategori) > 0) {
                $query->whereIn('kategori_menu_id', $request->kategori);
            }

            if ($request->filled('is_ready')) {
                $query->where('is_ready', $request->is_ready);
            }

            if ($request->filled('search')) {
                $query->where('nama_menu', 'like', '%' . $request->search . '%');
            }

            $menu = $query
                ->orderBy('is_ready', 'desc')
                ->get();

            return response()->json([
                'data' => $menu
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data'
            ], 500);
        }
    }

    public function loadData(Request $request)
    {
        try {

            $query = Menu::with(['fotoMenus', 'kategoriMenu'])
                ->where('status', $request->status ?? 1)
                ->where('is_ready', true);

            if ($request->filled('kategori') && $request->kategori != '0') {
                $menu = $query->where('kategori_menu_id', $request->kategori)->get();
            } else {
                $menu = $query->get();
            }

            return response()->json([
                'data' => $menu
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data'
            ], 500);
        }
    }

    function dataTable(Request $request)
    {
        try {
            $query = Menu::with('kategoriMenu')
                ->where('status', $request->status ?? 1);

            $menu = $query->get();

            return response()->json([
                'data' => $menu
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
            'nama_menu' => 'required|unique:menus,nama_menu',
            'kategori_menu_id' => 'required|exists:kategori_menus,id',
            'harga' => 'required|numeric',
            'foto_menu' => 'required|array|min:1',
            'foto_menu.*' => 'image|mimes:jpg,jpeg,png,webp|max:20480',
        ], [
            'nama_menu.required' => 'Nama menu wajib diisi',
            'nama_menu.unique' => 'Nama menu sudah ada',
            'kategori_menu_id.required' => 'Kategori wajib dipilih',
            'kategori_menu_id.exists' => 'Kategori tidak valid',
            'harga.required' => 'Harga wajib diisi',
            'harga.numeric' => 'Harga harus berupa angka',
            'foto_menu.required' => 'Minimal 1 foto wajib diupload',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $menu = Menu::create([
                'id' => (string) Str::uuid(),
                'nama_menu' => $request->nama_menu,
                'kategori_menu_id' => $request->kategori_menu_id,
                'harga' => $request->harga,
                'deskripsi' => $request->deskripsi,
                'status' => true,
                'is_ready' => true,
            ]);

            if ($request->hasFile('foto_menu')) {
                foreach ($request->file('foto_menu') as $file) {
                    $compressedImage = Image::read($file)
                        ->scale(width: 800)
                        ->toWebp(75);
                    $filename = 'foto_menu/' . time() . '_' . uniqid() . '.webp';
                    Storage::disk('public')->put($filename, (string) $compressedImage);
                    FotoMenu::create([
                        'menu_id' => $menu->id,
                        'foto_path' => $filename
                    ]);
                }
            }

            DB::commit();
            $menu->load(['kategoriMenu', 'fotoMenus']);

            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil ditambahkan',
                'data' => $menu
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $menu = Menu::with(['fotoMenus', 'kategoriMenu'])->findOrFail($id);

            return response()->json($menu);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:menus,id',
            'edit_nama_menu' => 'required|unique:menus,nama_menu,' . $request->id,
            'edit_kategori_menu_id' => 'required|exists:kategori_menus,id',
            'edit_harga' => 'required|numeric',
            'edit_foto_menu' => 'nullable|array',
            'edit_foto_menu.*' => 'image|mimes:jpg,jpeg,png,webp|max:20480',
        ], [
            'edit_nama_menu.required' => 'Nama menu wajib diisi',
            'edit_nama_menu.unique' => 'Nama menu sudah digunakan oleh menu lain',
            'edit_kategori_menu_id.required' => 'Kategori wajib dipilih',
            'edit_kategori_menu_id.exists' => 'Kategori tidak valid',
            'edit_harga.required' => 'Harga wajib diisi',
            'edit_harga.numeric' => 'Harga harus berupa angka',
            'edit_foto_menu.*.image' => 'File harus berupa gambar',
            'edit_foto_menu.*.mimes' => 'Format gambar harus jpg, jpeg, png, atau webp',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $menu = Menu::findOrFail($request->id);

            $menu->update([
                'nama_menu' => $request->edit_nama_menu,
                'kategori_menu_id' => $request->edit_kategori_menu_id,
                'harga' => $request->edit_harga,
                'deskripsi' => $request->edit_deskripsi,
            ]);

            if ($request->hasFile('edit_foto_menu')) {
                foreach ($menu->fotoMenus as $foto) {
                    Storage::disk('public')->delete($foto->foto_path);
                }

                $menu->fotoMenus()->delete();
                foreach ($request->file('edit_foto_menu') as $file) {
                    $compressedImage = Image::read($file)
                        ->scale(width: 800)
                        ->toWebp(75);
                    $filename = 'foto_menu/' . time() . '_' . uniqid() . '.webp';
                    Storage::disk('public')->put($filename, (string) $compressedImage);
                    FotoMenu::create([
                        'menu_id' => $menu->id,
                        'foto_path' => $filename
                    ]);
                }
            }

            DB::commit();
            $menu->load(['fotoMenus', 'kategoriMenu']);

            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil diupdate',
                'data' => $menu
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal update data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deactivate($id)
    {
        try {
            $menu = Menu::findOrFail($id);
            $menu->status = false;
            $menu->save();

            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus menu' . $e->getMessage()
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $menus = Menu::findOrFail($id);
            $menus->status = true;
            $menus->save();

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

    public function toggleReady(Request $request, $id)
    {
        try {
            $request->validate([
                'is_ready' => 'required|boolean'
            ]);

            $menu = Menu::findOrFail($id);

            $menu->update([
                'is_ready' => $request->is_ready
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status menu berhasil diubah',
                'data' => $menu
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update status'
            ], 500);
        }
    }
}
