<?php

namespace App\Http\Controllers;

use App\Models\KategoriMenu;
use App\Models\Menu;
use Illuminate\Http\Request;

class ApiMenuController extends Controller
{
    public function index(Request $r)
    {
        $categories = KategoriMenu::where('status', true)
            ->pluck('nama_kategori')
            ->toArray();

        array_unshift($categories, 'Semua');

        $menus = Menu::with([
            'kategoriMenu',
            'fotoMenus'
        ])
            ->where('status', true);

        // Filter kategori
        if ($r->category && $r->category != 'Semua') {

            $menus->whereHas('kategoriMenu', function ($q) use ($r) {
                $q->where('nama_kategori', $r->category);
            });
        }

        // Search menu
        if ($r->search) {

            $menus->where(function ($q) use ($r) {

                $q->where('nama_menu', 'like', '%' . $r->search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $r->search . '%');
            });
        }

        $menus = $menus
            ->orderBy('created_at', 'desc')
            ->get();

        $products = $menus->map(function ($menu) {

            $firstPhoto = $menu->fotoMenus->first();

            return [
                'id' => $menu->id,
                'name' => $menu->nama_menu,
                'price' => (int) $menu->harga,
                'category' => $menu->kategoriMenu?->nama_kategori,
                'image' => $firstPhoto
                    ? asset('storage/' . $firstPhoto->foto_path)
                    : null,
                'description' => $menu->deskripsi,
                'is_ready' => $menu->is_ready,
            ];
        });

        return response()->json([
            'success' => true,
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    public function searchMenus(Request $request)
    {
        try {

            $request->validate([
                'query' => 'required|string|min:1'
            ]);

            $search = $request->query('query');

            $menus = Menu::with([
                'kategoriMenu',
                'fotoMenus'
            ])
                ->where('status', true)
                ->where('is_ready', true)
                ->where('nama_menu', 'like', '%' . $search . '%')
                ->orderBy('nama_menu', 'asc')
                ->get();

            $products = $menus->map(function ($menu) {

                $firstPhoto = $menu->fotoMenus->first();

                return [
                    'id' => $menu->id,
                    'name' => $menu->nama_menu,
                    'price' => (int) $menu->harga,
                    'category' => $menu->kategoriMenu?->nama_kategori,
                    'image' => $firstPhoto
                        ? asset('storage/' . $firstPhoto->foto_path)
                        : null,
                    'description' => $menu->deskripsi,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data menu berhasil ditemukan',
                'data' => $products
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
